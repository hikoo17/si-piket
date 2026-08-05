<?php

namespace App\Console\Commands;

use App\Models\PiketLog;
use App\Models\PiketSchedule;
use App\Models\School;
use Illuminate\Console\Command;

class MarkPiketAbsent extends Command
{
    protected $signature = 'piket:mark-absent {--date= : Tanggal YYYY-MM-DD untuk pengujian} {--shift= : morning atau afternoon}';

    protected $description = 'Menandai siswa yang tidak mengirim bukti sebagai absent';

    public function handle(): int
    {
        $date = $this->option('date') ? now()->createFromFormat('Y-m-d', $this->option('date')) : today();
        $shift = $this->option('shift');
        if ($shift && ! in_array($shift, ['morning', 'afternoon'], true)) {
            $this->error('Shift harus morning atau afternoon.');

            return self::FAILURE;
        }
        if (! $this->option('date') && $shift) {
            $school = School::primary();
            $deadline = $shift === 'afternoon' ? $school->return_upload_deadline : $school->upload_deadline;
            if (now()->format('H:i') <= substr($deadline, 0, 5)) {
                return self::SUCCESS;
            }
        }
        $count = 0;
        PiketSchedule::where('day_of_week', $date->englishDayOfWeek)
            ->when($shift, fn ($query) => $query->where('shift', $shift))
            ->chunkById(100, function ($schedules) use ($date, &$count) {
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
