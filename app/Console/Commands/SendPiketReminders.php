<?php

namespace App\Console\Commands;

use App\Jobs\SendPiketReminderJob;
use App\Models\NotificationLog;
use App\Models\PiketSchedule;
use Illuminate\Console\Command;

class SendPiketReminders extends Command
{
    protected $signature = 'piket:send-reminders {--date= : Tanggal YYYY-MM-DD untuk pengujian}';

    protected $description = 'Mengantrekan pengingat WhatsApp untuk siswa yang piket hari ini';

    public function handle(): int
    {
        $date = $this->option('date') ? now()->createFromFormat('Y-m-d', $this->option('date')) : today();
        $day = $date->englishDayOfWeek;
        $count = 0;

        PiketSchedule::with(['user.schoolClass'])->where('day_of_week', $day)->chunkById(100, function ($schedules) use ($date, &$count) {
            foreach ($schedules as $schedule) {
                $user = $schedule->user;
                if (blank($user->phone)) {
                    continue;
                }
                $message = "Halo {$user->name}, hari ini jadwal piket kamu di kelas ".($user->schoolClass?->name ?? '-').'. Jangan lupa kerjakan piket dan kirim foto bukti sebelum jam 17:00 WIB ya!';
                $log = NotificationLog::query()
                    ->where('user_id', $user->id)
                    ->where('schedule_id', $schedule->id)
                    ->whereDate('date', $date)
                    ->where('channel', 'whatsapp')
                    ->first();

                if (! $log) {
                    $log = NotificationLog::query()->create([
                        'user_id' => $user->id,
                        'schedule_id' => $schedule->id,
                        'date' => $date->toDateString(),
                        'channel' => 'whatsapp',
                        'phone' => $user->phone,
                        'message' => $message,
                        'status' => 'queued',
                    ]);
                }
                if ($log->wasRecentlyCreated || $log->status === 'failed') {
                    SendPiketReminderJob::dispatch($log->id)->onQueue('notifications');
                    $count++;
                }
            }
        });
        $this->info("{$count} pengingat diantrekan.");

        return self::SUCCESS;
    }
}
