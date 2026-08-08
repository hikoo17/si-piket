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
    public function school_is_a_singleton_setting_without_create_or_delete_routes(): void
    {
        $this->assertFalse(app('router')->has('schools.create'));
        $this->assertFalse(app('router')->has('schools.store'));
        $this->assertFalse(app('router')->has('schools.destroy'));
        $this->assertTrue(app('router')->has('schools.index'));
        $this->assertTrue(app('router')->has('schools.update'));
    }

    #[Test]
    public function class_is_automatically_assigned_to_the_primary_school(): void
    {
        $school = School::create(['name' => 'SMAN 1 Tasikmalaya', 'latitude' => -7.327096, 'longitude' => 108.220349, 'radius_meters' => 100]);
        $otherSchool = School::create(['name' => 'Sekolah Lain', 'latitude' => -6.2, 'longitude' => 106.8, 'radius_meters' => 100]);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('classes.store'), [
            'name' => 'X IPA 1',
            'school_id' => $otherSchool->id,
        ])->assertRedirect(route('classes.index'));

        $this->assertDatabaseHas('classes', ['name' => 'X IPA 1', 'school_id' => $school->id]);
        $this->assertDatabaseMissing('classes', ['name' => 'X IPA 1', 'school_id' => $otherSchool->id]);
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
            'return_upload_start_time' => '14:15',
            'return_upload_deadline' => '16:45',
        ])->assertSessionHas('success');

        $school->refresh();
        $this->assertSame('07:15', substr($school->upload_start_time, 0, 5));
        $this->assertSame('09:45', substr($school->upload_deadline, 0, 5));
        $this->assertSame('14:15', substr($school->return_upload_start_time, 0, 5));
        $this->assertSame('16:45', substr($school->return_upload_deadline, 0, 5));
        $this->actingAs($admin)->get(route('schools.edit', $school))
            ->assertSee('value="07:15"', false)
            ->assertSee('value="09:45"', false);
        $this->actingAs($admin)->get(route('schools.edit', $school))
            ->assertSee('value="14:15"', false)
            ->assertSee('value="16:45"', false);
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
            'shift' => 'morning',
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
        $this->assertDatabaseCount('piket_schedules', 2);

        // Mengirim ulang hari yang sama tidak menambah jadwal duplikat.
        $this->actingAs($admin)->post(route('schedules.store'), $data)->assertSessionHas('success');
        $this->assertDatabaseCount('piket_schedules', 2);
    }

    #[Test]
    public function admin_can_edit_a_schedule_day(): void
    {
        [$class] = $this->classes();
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'siswa', 'class_id' => $class->id]);
        $schedule = PiketSchedule::create(['user_id' => $student->id, 'day_of_week' => 'Monday']);

        $this->actingAs($admin)->put(route('schedules.update', $schedule), ['day_of_week' => 'Sunday', 'shift' => 'morning'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('piket_schedules', ['id' => $schedule->id, 'day_of_week' => 'Sunday']);
    }

    #[Test]
    public function student_can_have_morning_and_afternoon_schedules_on_the_same_day(): void
    {
        [$class] = $this->classes();
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'siswa', 'class_id' => $class->id]);

        $this->actingAs($admin)->post(route('schedules.store'), [
            'user_id' => $student->id,
            'day_of_week' => 'Monday',
            'shift' => 'morning',
        ])->assertSessionHas('success');
        $this->actingAs($admin)->post(route('schedules.store'), [
            'user_id' => $student->id,
            'day_of_week' => 'Monday',
            'shift' => 'afternoon',
        ])->assertSessionHas('success');

        $this->assertDatabaseCount('piket_schedules', 2);
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

    #[Test]
    public function approved_evidence_is_not_shown_on_verification_page(): void
    {
        [$class] = $this->classes();
        $teacher = User::factory()->create(['role' => 'guru']);
        $student = User::factory()->create(['role' => 'siswa', 'class_id' => $class->id]);
        $schedule = PiketSchedule::create(['user_id' => $student->id, 'day_of_week' => 'Monday']);
        PiketLog::create(['schedule_id' => $schedule->id, 'user_id' => $student->id, 'date' => today(), 'status' => 'approved']);

        $this->actingAs($teacher)->get(route('verification.index'))
            ->assertOk()
            ->assertSee('Tidak ada bukti yang menunggu verifikasi.')
            ->assertDontSee($student->name);
    }

    #[Test]
    public function teacher_can_open_report_detail_page(): void
    {
        [$class] = $this->classes();
        $teacher = User::factory()->create(['role' => 'guru']);
        $student = User::factory()->create(['role' => 'siswa', 'class_id' => $class->id]);
        $schedule = PiketSchedule::create(['user_id' => $student->id, 'day_of_week' => 'Monday']);
        $log = PiketLog::create(['schedule_id' => $schedule->id, 'user_id' => $student->id, 'date' => today(), 'status' => 'approved', 'photo_path' => 'piket/example.jpg']);

        $this->actingAs($teacher)->get(route('reports.show', $log))
            ->assertOk()
            ->assertSee('Detail Laporan Piket')
            ->assertSee($student->name)
            ->assertSee('piket/example.jpg');
    }

    #[Test]
    public function teacher_with_a_class_can_only_access_that_class(): void
    {
        [$firstClass, $secondClass] = $this->classes();
        $teacher = User::factory()->create(['role' => 'guru', 'class_id' => $firstClass->id]);
        $firstStudent = User::factory()->create(['role' => 'siswa', 'class_id' => $firstClass->id]);
        $secondStudent = User::factory()->create(['role' => 'siswa', 'class_id' => $secondClass->id]);
        $firstSchedule = PiketSchedule::create(['user_id' => $firstStudent->id, 'day_of_week' => 'Monday']);
        $secondSchedule = PiketSchedule::create(['user_id' => $secondStudent->id, 'day_of_week' => 'Monday']);
        $firstLog = PiketLog::create(['schedule_id' => $firstSchedule->id, 'user_id' => $firstStudent->id, 'date' => today(), 'status' => 'pending']);
        $secondLog = PiketLog::create(['schedule_id' => $secondSchedule->id, 'user_id' => $secondStudent->id, 'date' => today(), 'status' => 'pending']);

        $this->actingAs($teacher)->get(route('verification.index'))
            ->assertOk()
            ->assertSee($firstStudent->name)
            ->assertDontSee($secondStudent->name);
        $this->actingAs($teacher)->get(route('reports.index'))
            ->assertOk()
            ->assertSee($firstStudent->name)
            ->assertDontSee($secondStudent->name);
        $this->actingAs($teacher)->get(route('reports.show', $secondLog))->assertForbidden();
        $this->actingAs($teacher)->patch(route('verification.approve', $secondLog))->assertForbidden();
        $this->actingAs($teacher)->patch(route('verification.approve', $firstLog))->assertSessionHas('success');
    }

    #[Test]
    public function teacher_without_a_class_can_access_all_classes(): void
    {
        [$firstClass, $secondClass] = $this->classes();
        $teacher = User::factory()->create(['role' => 'guru', 'class_id' => null]);
        $firstStudent = User::factory()->create(['role' => 'siswa', 'class_id' => $firstClass->id]);
        $secondStudent = User::factory()->create(['role' => 'siswa', 'class_id' => $secondClass->id]);
        $firstSchedule = PiketSchedule::create(['user_id' => $firstStudent->id, 'day_of_week' => 'Monday']);
        $secondSchedule = PiketSchedule::create(['user_id' => $secondStudent->id, 'day_of_week' => 'Monday']);
        PiketLog::create(['schedule_id' => $firstSchedule->id, 'user_id' => $firstStudent->id, 'date' => today(), 'status' => 'pending']);
        $secondLog = PiketLog::create(['schedule_id' => $secondSchedule->id, 'user_id' => $secondStudent->id, 'date' => today(), 'status' => 'pending']);

        $this->actingAs($teacher)->get(route('verification.index'))
            ->assertOk()
            ->assertSee($firstStudent->name)
            ->assertSee($secondStudent->name);
        $this->actingAs($teacher)->get(route('reports.index'))
            ->assertOk()
            ->assertSee($firstStudent->name)
            ->assertSee($secondStudent->name);
        $this->actingAs($teacher)->get(route('reports.show', $secondLog))->assertOk();
    }

    /** @return array{SchoolClass, SchoolClass} */
    private function classes(): array
    {
        $school = School::create(['name' => 'SMAN 1 Tasikmalaya', 'latitude' => -6.2, 'longitude' => 106.8, 'radius_meters' => 100]);

        return [
            SchoolClass::create(['school_id' => $school->id, 'name' => 'XII RPL 1']),
            SchoolClass::create(['school_id' => $school->id, 'name' => 'XII RPL 2']),
        ];
    }
}
