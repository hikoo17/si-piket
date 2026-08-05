<?php

namespace Tests\Feature;

use App\Jobs\SendPiketReminderJob;
use App\Models\PiketSchedule;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AutomationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function reminder_command_queues_one_notification_per_scheduled_user(): void
    {
        Queue::fake();
        [$user, $schedule] = $this->scheduledUser(phone: '628123456789', whatsappEnabled: true);

        $this->artisan('piket:send-reminders', ['--date' => '2026-08-03'])->assertSuccessful();

        $this->assertDatabaseHas('notification_logs', ['user_id' => $user->id, 'schedule_id' => $schedule->id, 'status' => 'queued']);
        Queue::assertPushed(SendPiketReminderJob::class, 1);
        $this->artisan('piket:send-reminders', ['--date' => '2026-08-03'])->assertSuccessful();
        Queue::assertPushed(SendPiketReminderJob::class, 1);
    }

    #[Test]
    public function disabled_whatsapp_setting_does_not_queue_reminders(): void
    {
        Queue::fake();
        $this->scheduledUser(phone: '628123456789');

        $this->artisan('piket:send-reminders', ['--date' => '2026-08-03'])->assertSuccessful();

        $this->assertDatabaseCount('notification_logs', 0);
        Queue::assertNothingPushed();
    }

    #[Test]
    public function admin_can_send_a_test_whatsapp_message(): void
    {
        Http::fake(['api.fonnte.com/*' => Http::response(['status' => true, 'id' => 'message-1'])]);
        $school = School::create(['name' => 'SMAN 1 Tasikmalaya', 'latitude' => -6.2, 'longitude' => 106.8, 'radius_meters' => 100]);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('schools.test-whatsapp', $school), [
            'test_whatsapp_phone' => '628123456789',
        ])->assertSessionHas('success');

        Http::assertSent(fn ($request) => $request['target'] === '628123456789');
    }

    #[Test]
    public function absent_command_creates_absent_log_only_when_no_submission_exists(): void
    {
        [$user, $schedule] = $this->scheduledUser();

        $this->artisan('piket:mark-absent', ['--date' => '2026-08-03'])->assertSuccessful();
        $this->assertDatabaseHas('piket_logs', ['user_id' => $user->id, 'schedule_id' => $schedule->id, 'status' => 'absent']);
        $this->artisan('piket:mark-absent', ['--date' => '2026-08-03'])->assertSuccessful();
        $this->assertSame(1, $user->piketLogs()->count());
    }

    #[Test]
    public function absent_command_can_process_only_one_shift(): void
    {
        [$user, $morning] = $this->scheduledUser();
        $afternoon = PiketSchedule::create([
            'user_id' => $user->id,
            'day_of_week' => 'Monday',
            'shift' => 'afternoon',
        ]);

        $this->artisan('piket:mark-absent', ['--date' => '2026-08-03', '--shift' => 'morning'])->assertSuccessful();

        $this->assertDatabaseHas('piket_logs', ['schedule_id' => $morning->id, 'status' => 'absent']);
        $this->assertDatabaseMissing('piket_logs', ['schedule_id' => $afternoon->id]);
    }

    private function scheduledUser(?string $phone = null, bool $whatsappEnabled = false): array
    {
        $school = School::create(['name' => 'SMAN 1 Tasikmalaya', 'latitude' => -6.2, 'longitude' => 106.8, 'radius_meters' => 100, 'whatsapp_enabled' => $whatsappEnabled]);
        $class = SchoolClass::create(['school_id' => $school->id, 'name' => 'XII RPL 1']);
        $user = User::factory()->create(['class_id' => $class->id, 'phone' => $phone]);
        $schedule = PiketSchedule::create(['user_id' => $user->id, 'day_of_week' => 'Monday']);

        return [$user, $schedule];
    }
}
