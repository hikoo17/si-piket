<?php

namespace Database\Seeders;

use App\Models\PiketSchedule;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClassStudentsSeeder extends Seeder
{
    use WithoutModelEvents;

    private const CLASS_NAME = 'X RPL 1';

    private const STUDENT_NAMES = [
        'Ahmad Fauzi',
        'Siti Nurhaliza',
        'Budi Santoso',
        'Dewi Lestari',
        'Eko Prasetyo',
        'Fitriani Putri',
        'Gunawan Wibowo',
        'Hani Ramadhani',
        'Irfan Maulana',
        'Juli Astuti',
        'Kurniawan Saputra',
        'Lina Marlina',
        'Muhammad Rizki',
        'Nova Sintia',
        'Oki Permana',
        'Putri Ayu',
        'Qori Ramadhan',
        'Rina Mustika',
        'Samsul Arifin',
        'Tuti Indah',
        'Ujang Kurnia',
        'Vina Oktaviani',
        'Wawan Setiawan',
        'Yuni Safitri',
        'Zainal Abidin',
        'Ani Susanti',
        'Bayu Aji',
        'Cici Permata',
        'Dedi Kuswara',
        'Elisa Rahma',
        'Ferdiansyah',
        'Gita Maharani',
        'Hendra Gunawan',
        'Indah Permata',
    ];

    public function run(): void
    {
        $school = School::primary();

        $class = SchoolClass::query()->updateOrCreate(
            ['school_id' => $school->id, 'name' => self::CLASS_NAME],
            [],
        );

        $password = Hash::make('password');

        foreach (self::STUDENT_NAMES as $index => $name) {
            $number = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $email = 'siswa' . $number . '@' . self::slug($class->name) . '.test';

            User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'role' => 'siswa',
                    'class_id' => $class->id,
                    'password' => $password,
                    'phone' => '628' . str_pad((string) (10000000 + $index), 8, '0', STR_PAD_LEFT),
                    'email_verified_at' => now(),
                ],
            );
        }

        $this->seedPiketSchedules($class);
    }

    private function seedPiketSchedules(SchoolClass $class): void
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $shifts = ['morning', 'afternoon'];

        $slots = [];
        foreach ($days as $day) {
            foreach ($shifts as $shift) {
                $slots[] = ['day_of_week' => $day, 'shift' => $shift];
            }
        }

        $students = $class->users()->orderBy('id')->get();
        $slotCount = count($slots);

        foreach ($students as $index => $student) {
            $slot = $slots[$index % $slotCount];

            PiketSchedule::query()->updateOrCreate(
                [
                    'user_id' => $student->id,
                    'day_of_week' => $slot['day_of_week'],
                    'shift' => $slot['shift'],
                ],
                [],
            );
        }
    }

    private function slug(string $value): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '', $value));
    }
}
