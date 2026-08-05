<?php

namespace Tests\Feature;

use App\Models\PiketLog;
use App\Models\PiketSchedule;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PiketUploadTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_student_can_upload_valid_piket_evidence_inside_school_radius(): void
    {
        Storage::fake('public');
        Carbon::setTestNow('2026-08-03 08:00:00');
        [$user, , $schedule] = $this->createScheduledUser(radius: 100);

        $response = $this->actingAs($user)->post(route('piket.upload'), [
            'photo' => $this->validPngDataUrl(),
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'accuracy' => 10,
            'schedule_id' => $schedule->id,
        ]);

        $response->assertSessionHas('success', 'Berhasil upload bukti piket! Menunggu verifikasi Guru/KM.');
        $log = $user->piketLogs()->sole();

        $this->assertSame('pending', $log->status);
        $this->assertSame(0, $log->distance_meters);
        Storage::disk('public')->assertExists($log->photo_path);
    }

    #[Test]
    public function upload_is_rejected_outside_school_radius(): void
    {
        Storage::fake('public');
        Carbon::setTestNow('2026-08-03 08:00:00');
        [$user, , $schedule] = $this->createScheduledUser(radius: 50);

        $response = $this->actingAs($user)->post(route('piket.upload'), [
            'photo' => $this->validPngDataUrl(),
            'latitude' => -6.201000,
            'longitude' => 106.816666,
            'accuracy' => 10,
            'schedule_id' => $schedule->id,
        ]);

        $response->assertSessionHas('error', 'Gagal! Kamu berada di luar area sekolah (111m dari lokasi).');
        $this->assertDatabaseCount('piket_logs', 0);
        Storage::disk('public')->assertDirectoryEmpty('piket');
    }

    #[Test]
    public function upload_requires_photo_and_coordinates(): void
    {
        Carbon::setTestNow('2026-08-03 08:00:00');
        [$user] = $this->createScheduledUser();

        $response = $this->actingAs($user)->post(route('piket.upload'));

        $response->assertInvalid(['photo', 'latitude', 'longitude']);
    }

    #[Test]
    public function student_can_resubmit_after_rejection_but_not_after_approval(): void
    {
        Storage::fake('public');
        Carbon::setTestNow('2026-08-03 08:00:00');
        [$user, $school, $schedule] = $this->createScheduledUser();

        $log = PiketLog::query()->create([
            'schedule_id' => $user->piketSchedules()->sole()->id,
            'user_id' => $user->id,
            'date' => today(),
            'status' => 'rejected',
        ]);

        $this->actingAs($user)->post(route('piket.upload'), [
            'photo' => $this->validPngDataUrl(),
            'latitude' => $school->latitude,
            'longitude' => $school->longitude,
            'accuracy' => 10,
            'schedule_id' => $schedule->id,
        ])->assertSessionHas('success');

        $this->assertDatabaseCount('piket_logs', 1);
        $this->assertSame('pending', $log->fresh()->status);

        $log->update(['status' => 'approved']);

        $this->actingAs($user)->post(route('piket.upload'), [
            'photo' => $this->validPngDataUrl(),
            'latitude' => $school->latitude,
            'longitude' => $school->longitude,
            'accuracy' => 10,
            'schedule_id' => $schedule->id,
        ])->assertSessionHas('error', 'Bukti piket hari ini sudah disetujui.');

        $this->assertDatabaseCount('piket_logs', 1);
    }

    #[Test]
    public function morning_and_afternoon_evidence_use_their_own_upload_windows(): void
    {
        Storage::fake('public');
        Carbon::setTestNow('2026-08-03 15:00:00');
        [$user, $school, $morning] = $this->createScheduledUser();
        $school->update([
            'upload_start_time' => '06:00',
            'upload_deadline' => '08:00',
            'return_upload_start_time' => '14:00',
            'return_upload_deadline' => '17:00',
        ]);
        $afternoon = PiketSchedule::create([
            'user_id' => $user->id,
            'day_of_week' => 'Monday',
            'shift' => 'afternoon',
        ]);
        $payload = [
            'photo' => $this->validPngDataUrl(),
            'latitude' => $school->latitude,
            'longitude' => $school->longitude,
            'accuracy' => 10,
        ];

        $this->actingAs($user)->post(route('piket.upload'), $payload + ['schedule_id' => $morning->id])
            ->assertSessionHas('error', 'Upload hanya dapat dilakukan pada jam Piket Pagi yang ditentukan.');
        $this->actingAs($user)->post(route('piket.upload'), $payload + ['schedule_id' => $afternoon->id])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('piket_logs', ['schedule_id' => $afternoon->id, 'status' => 'pending']);
        $this->assertDatabaseMissing('piket_logs', ['schedule_id' => $morning->id]);
    }

    /**
     * @return array{User, School, PiketSchedule}
     */
    private function createScheduledUser(int $radius = 100): array
    {
        $school = School::query()->create([
            'name' => 'SMK Test',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'accuracy' => 10,
            'radius_meters' => $radius,
        ]);
        $class = SchoolClass::query()->create([
            'school_id' => $school->id,
            'name' => 'XII RPL 1',
        ]);
        $user = User::factory()->create(['class_id' => $class->id]);

        $schedule = PiketSchedule::query()->create([
            'user_id' => $user->id,
            'day_of_week' => 'Monday',
            'shift' => 'morning',
        ]);

        return [$user, $school, $schedule];
    }

    private function validPngDataUrl(): string
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';
    }
}
