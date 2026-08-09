<?php

namespace App\Console\Commands;

use App\Jobs\SendPiketReminderJob;
use App\Models\NotificationLog;
use App\Models\PiketSchedule;
use App\Models\School;
use Illuminate\Console\Command;

class SendPiketReminders extends Command
{
    protected $signature = 'piket:send-reminders {--date= : Tanggal YYYY-MM-DD untuk pengujian}';

    protected $description = 'Mengantrekan pengingat WhatsApp untuk siswa yang piket hari ini';

    public function handle(): int
    {
        $date = $this->option('date') ? now()->createFromFormat('Y-m-d', $this->option('date')) : today();
        $school = School::primary();
        if (! $school->whatsapp_enabled) {
            $this->info('Notifikasi WhatsApp sedang nonaktif.');

            return self::SUCCESS;
        }
        if (! $this->option('date') && now()->format('H:i') !== substr($school->whatsapp_send_time, 0, 5)) {
            return self::SUCCESS;
        }
        $day = $date->englishDayOfWeek;
        $nowTime = $this->option('date') ? null : now()->format('H:i');

        // Tentukan shift yang dikirim berdasarkan jam pengiriman
        // (mode tes --date: kirim semua shift)
        if ($this->option('date')) {
            $shift = null;
        } elseif ($nowTime === substr($school->whatsapp_send_time, 0, 5)) {
            $shift = 'morning';
        } elseif ($nowTime === substr($school->whatsapp_send_time_return ?? '14:00', 0, 5)) {
            $shift = 'afternoon';
        } else {
            return self::SUCCESS;
        }

        $count = 0;

        $schedulesQuery = PiketSchedule::with(['user.schoolClass'])
            ->where('day_of_week', $day)
            ->when($shift, fn ($query) => $query->where('shift', $shift));

        $schedulesQuery->chunkById(100, function ($schedules) use ($date, $day, $school, &$count) {
            foreach ($schedules as $schedule) {
                $user = $schedule->user;
                if (blank($user->phone)) {
                    continue;
                }
                $message = strtr($school->whatsapp_message_template ?: 'Halo {nama}, hari ini jadwal {jenis_piket} kamu di kelas {kelas}. Jangan lupa kirim foto bukti piket.', [
                    '{nama}' => $user->name,
                    '{kelas}' => $user->schoolClass?->name ?? '-',
                    '{jenis_piket}' => $schedule->shift_label,
                    '{hari}' => $day,
                    '{tanggal}' => $date->format('d/m/Y'),
                ]);
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
