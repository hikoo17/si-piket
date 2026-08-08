<?php

namespace Database\Seeders;

use App\Models\PiketLog;
use App\Models\PiketSchedule;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Database\Seeders\ClassStudentsSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $school = School::query()->updateOrCreate(
            ['name' => 'SMAN 1 Tasikmalaya'],
            [
                'address' => 'Jl. Rumah Sakit Umum No. 28, Empangsari, Kec. Tawang, Kota Tasikmalaya, Jawa Barat',
                'latitude' => -7.32709600,
                'longitude' => 108.22034900,
                'radius_meters' => 100,
                'upload_start_time' => '05:00',
                'upload_deadline' => '23:59',
                'return_upload_start_time' => '14:00',
                'return_upload_deadline' => '23:59',
            ],
        );

        $rpl = SchoolClass::query()->updateOrCreate(
            ['school_id' => $school->id, 'name' => 'XII RPL 1'],
            [],
        );
        $tkj = SchoolClass::query()->updateOrCreate(
            ['school_id' => $school->id, 'name' => 'XII TKJ 1'],
            [],
        );

        $password = Hash::make('password');
        $admin = $this->user('Administrator', 'admin@si-piket.test', 'admin', null, $password, '628111111111');
        $teacher = $this->user('Guru Piket', 'guru@si-piket.test', 'guru', null, $password, '628122222222');
        $km = $this->user('Ketua Kelas RPL', 'km@si-piket.test', 'km', $rpl->id, $password, '628133333333');
        $student = $this->user('Siswa RPL', 'siswa@si-piket.test', 'siswa', $rpl->id, $password, '628144444444');
        $otherStudent = $this->user('Siswa TKJ', 'siswa.tkj@si-piket.test', 'siswa', $tkj->id, $password, '628155555555');

        $this->call(ClassStudentsSeeder::class);

        $day = now()->englishDayOfWeek;

        foreach ([$km, $student, $otherStudent] as $scheduledUser) {
            PiketSchedule::query()->updateOrCreate(
                ['user_id' => $scheduledUser->id, 'day_of_week' => $day, 'shift' => 'morning'],
                [],
            );
        }

        $sampleSchedule = PiketSchedule::query()->where('user_id', $student->id)->where('day_of_week', $day)->firstOrFail();
        PiketLog::query()->updateOrCreate(
            ['schedule_id' => $sampleSchedule->id, 'date' => today()->subDay()],
            [
                'user_id' => $student->id,
                'status' => 'approved',
                'distance_meters' => 12,
                'accuracy_meters' => 8,
                'verified_by' => $teacher->id,
                'verified_at' => now(),
            ],
        );
    }

    private function user(
        string $name,
        string $email,
        string $role,
        ?int $classId,
        string $password,
        string $phone,
    ): User {
        return User::query()->updateOrCreate(
            ['email' => $email],
            compact('name', 'role', 'password', 'phone') + ['class_id' => $classId, 'email_verified_at' => now()],
        );
    }
}
