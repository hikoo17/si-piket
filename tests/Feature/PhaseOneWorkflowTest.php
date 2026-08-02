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
