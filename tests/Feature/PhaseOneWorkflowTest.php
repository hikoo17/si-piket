<?php

namespace Tests\Feature;

use App\Models\PiketLog;
use App\Models\PiketSchedule;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PhaseOneWorkflowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_access_master_data_but_student_cannot(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($admin)->get(route('schools.index'))->assertOk();
        $this->actingAs($student)->get(route('schools.index'))->assertForbidden();
    }

    #[Test]
    public function admin_can_update_school_upload_times_without_timezone_conversion(): void
    {
        [$class] = $this->classes();
        $school = $class->school;
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('schools.update', $school), [
            'name' => $school->name,
            'latitude' => $school->latitude,
            'longitude' => $school->longitude,
            'radius_meters' => $school->radius_meters,
            'upload_start_time' => '07:15',
            'upload_deadline' => '09:45',
        ])->assertSessionHas('success');

        $school->refresh();
        $this->assertSame('07:15', substr($school->upload_start_time, 0, 5));
        $this->assertSame('09:45', substr($school->upload_deadline, 0, 5));
        $this->actingAs($admin)->get(route('schools.edit', $school))
            ->assertSee('value="07:15"', false)
            ->assertSee('value="09:45"', false);
    }

    #[Test]
    public function km_cannot_schedule_a_student_from_another_class(): void
    {
        [$firstClass, $secondClass] = $this->classes();
        $km = User::factory()->create(['role' => 'km', 'class_id' => $firstClass->id]);
        $student = User::factory()->create(['role' => 'siswa', 'class_id' => $secondClass->id]);

        $this->actingAs($km)->post(route('schedules.store'), [
            'user_id' => $student->id,
            'day_of_week' => 'Monday',
        ])->assertForbidden();
    }

    #[Test]
    public function admin_can_schedule_sunday_but_cannot_duplicate_a_schedule(): void
    {
        [$class] = $this->classes();
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'siswa', 'class_id' => $class->id]);

        $data = ['user_id' => $student->id, 'day_of_week' => 'Sunday'];
        $this->actingAs($admin)->post(route('schedules.store'), $data)->assertSessionHas('success');
        $this->actingAs($admin)->post(route('schedules.store'), $data)->assertSessionHasErrors('day_of_week');

        $this->assertDatabaseCount('piket_schedules', 1);
    }

    #[Test]
    public function teacher_can_approve_pending_evidence(): void
    {
        [$class] = $this->classes();
        $teacher = User::factory()->create(['role' => 'guru']);
        $student = User::factory()->create(['role' => 'siswa', 'class_id' => $class->id]);
        $schedule = PiketSchedule::create(['user_id' => $student->id, 'day_of_week' => 'Monday']);
        $log = PiketLog::create(['schedule_id' => $schedule->id, 'user_id' => $student->id, 'date' => today(), 'status' => 'pending']);

        $this->actingAs($teacher)->patch(route('verification.approve', $log))->assertSessionHas('success');

        $this->assertDatabaseHas('piket_logs', ['id' => $log->id, 'status' => 'approved', 'verified_by' => $teacher->id]);
    }

    /** @return array{SchoolClass, SchoolClass} */
    private function classes(): array
    {
        $school = School::create(['name' => 'SMK Test', 'latitude' => -6.2, 'longitude' => 106.8, 'radius_meters' => 100]);

        return [
            SchoolClass::create(['school_id' => $school->id, 'name' => 'XII RPL 1']),
            SchoolClass::create(['school_id' => $school->id, 'name' => 'XII RPL 2']),
        ];
    }
}
