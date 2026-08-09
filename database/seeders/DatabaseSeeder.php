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

        $classXII4 = SchoolClass::query()->updateOrCreate(
            ['school_id' => $school->id, 'name' => 'XII-4'],
            [],
        );

        $password = Hash::make('password');
        $admin = $this->user('Administrator', 'admin@si-piket.test', 'admin', null, $password, '628111111111');
        $waliKelas = $this->user('Wali Kelas XII-4', 'wali.kelas@si-piket.test', 'wali_kelas', $classXII4->id, $password, '628166666666');
        $km = $this->user('Ketua Kelas XII-4', 'km@si-piket.test', 'km', $classXII4->id, $password, '628133333333');
        $student = $this->user('Siswa XII-4', 'siswa@si-piket.test', 'siswa', $classXII4->id, $password, '628144444444');
        $otherStudent = $this->user('Siswa XII-4', 'siswa.xii4@si-piket.test', 'siswa', $classXII4->id, $password, '628155555555');

        $this->call(ClassStudentsSeeder::class);

        $sampleSchedule = PiketSchedule::query()->where('user_id', $student->id)->firstOrFail();
        PiketLog::query()->updateOrCreate(
            ['schedule_id' => $sampleSchedule->id, 'date' => today()->subDay()],
            [
                'user_id' => $student->id,
                'status' => 'approved',
                'distance_meters' => 12,
                'accuracy_meters' => 8,
                'verified_by' => $waliKelas->id,
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
