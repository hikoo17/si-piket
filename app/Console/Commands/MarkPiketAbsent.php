<?php

namespace App\Console\Commands;

use App\Models\PiketLog;
use App\Models\PiketSchedule;
use Illuminate\Console\Command;

class MarkPiketAbsent extends Command
{
    protected $signature = 'piket:mark-absent {--date= : Tanggal YYYY-MM-DD untuk pengujian}';

    protected $description = 'Menandai siswa yang tidak mengirim bukti sebagai absent';

    public function handle(): int
    {
        $date = $this->option('date') ? now()->createFromFormat('Y-m-d', $this->option('date')) : today();
        $count = 0;
        PiketSchedule::where('day_of_week', $date->englishDayOfWeek)->chunkById(100, function ($schedules) use ($date, &$count) {
            foreach ($schedules as $schedule) {
                if (PiketLog::where('schedule_id', $schedule->id)->whereDate('date', $date)->exists()) {
                    continue;
                }
                PiketLog::create(['schedule_id' => $schedule->id, 'user_id' => $schedule->user_id, 'date' => $date, 'status' => 'absent']);
                $count++;
            }
        });
        $this->info("{$count} siswa ditandai absent.");

        return self::SUCCESS;
    }
}
