<?php

namespace App\Http\Controllers;

use App\Models\PiketLog;
use App\Models\PiketSchedule;
use App\Models\SchoolClass;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::query();
        $students = User::query()->whereIn('role', ['siswa', 'km']);

        if ($this->restrictedClassId($request)) {
            $classes->whereKey($this->restrictedClassId($request));
            $students->where('class_id', $this->restrictedClassId($request));
        }

        return view('reports.index', ['logs' => $this->query($request)->paginate(30)->withQueryString(), 'classes' => $classes->get(), 'students' => $students->get()]);
    }

    public function show(Request $request, PiketLog $log): View
    {
        $log->load(['user.schoolClass', 'schedule', 'verifier']);

        if ($this->restrictedClassId($request) && $log->user->class_id !== $this->restrictedClassId($request)) {
            throw new AccessDeniedHttpException;
        }

        return view('reports.show', compact('log'));
    }

    public function csv(Request $request): StreamedResponse
    {
        $logs = $this->query($request)->get();

        return response()->streamDownload(function () use ($logs) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tanggal', 'Waktu', 'Siswa', 'Kelas', 'Jenis Piket', 'Status', 'Jarak (m)', 'Akurasi (m)', 'Deskripsi']);
            foreach ($logs as $log) {
                fputcsv($out, [$log->date->format('Y-m-d'), ($log->photo_captured_at ?? $log->created_at)?->format('H:i'), $log->user->name, $log->user->schoolClass?->name, $log->schedule?->shift_label, $log->status, $log->distance_meters, $log->accuracy_meters, $log->description]);
            } fclose($out);
        }, 'laporan-piket.csv', ['Content-Type' => 'text/csv']);
    }

    public function pdf(Request $request)
    {
        return Pdf::loadView('reports.pdf', ['logs' => $this->query($request)->get()])->download('laporan-piket.pdf');
    }

    public function summary(Request $request): View
    {
        $request->validate([
            'period' => ['nullable', 'in:previous_mondays,preceding_week'],
            'from' => ['nullable', 'date', 'required_with:to'],
            'to' => ['nullable', 'date', 'required_with:from', 'after_or_equal:from'],
            'class_id' => ['nullable', 'integer'],
            'shift' => ['nullable', 'in:morning,afternoon'],
        ]);
        [$from, $to] = $this->summaryPeriod($request);
        $restrictedClassId = $this->restrictedClassId($request);
        $schedules = PiketSchedule::with('user.schoolClass')
            ->when($restrictedClassId, fn ($query) => $query->whereHas('user', fn ($user) => $user->where('class_id', $restrictedClassId)))
            ->when($request->filled('class_id'), fn ($query) => $query->whereHas('user', fn ($user) => $user->where('class_id', $request->class_id)))
            ->when($request->filled('shift'), fn ($query) => $query->where('shift', $request->shift))
            ->get();

        $logs = PiketLog::query()
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->whereIn('schedule_id', $schedules->pluck('id'))
            ->get()
            ->keyBy(fn ($log) => $log->schedule_id.'|'.$log->date->toDateString());

        $summary = collect();
        foreach (CarbonPeriod::create($from, $to) as $date) {
            foreach ($schedules->where('day_of_week', $date->englishDayOfWeek) as $schedule) {
                $log = $logs->get($schedule->id.'|'.$date->toDateString());
                $student = $schedule->user;
                $row = $summary->get($student->id, [
                    'user' => $student,
                    'expected' => 0,
                    'attended' => 0,
                    'missed' => 0,
                    'dates' => collect(),
                ]);
                $row['expected']++;
                if ($log && in_array($log->status, ['pending', 'approved'], true)) {
                    $row['attended']++;
                } else {
                    $row['missed']++;
                    $row['dates']->push(['date' => $date->copy(), 'shift' => $schedule->shift_label]);
                }
                $summary->put($student->id, $row);
            }
        }

        $classes = SchoolClass::query()
            ->when($restrictedClassId, fn ($query) => $query->whereKey($restrictedClassId))
            ->get();

        return view('reports.summary', [
            'summary' => $summary->filter(fn ($row) => $row['missed'] > 0)->sortByDesc('missed')->values(),
            'from' => $from,
            'to' => $to,
            'classes' => $classes,
        ]);
    }

    /** @return array{Carbon, Carbon} */
    private function summaryPeriod(Request $request): array
    {
        $preset = $request->input('period', 'previous_mondays');
        if ($preset === 'preceding_week') {
            $from = today()->subWeek()->startOfWeek();

            return [$from, $from->copy()->endOfWeek()];
        }

        if ($request->filled('from') && $request->filled('to')) {
            return [Carbon::parse($request->from)->startOfDay(), Carbon::parse($request->to)->startOfDay()];
        }

        $to = today()->previous(Carbon::MONDAY);

        return [$to->copy()->subWeeks(3), $to];
    }

    private function query(Request $request)
    {
        return PiketLog::with(['user.schoolClass', 'schedule', 'verifier'])
            ->when($this->restrictedClassId($request), fn ($q, $classId) => $q->whereHas('user', fn ($u) => $u->where('class_id', $classId)))
            ->when($request->filled('month'), fn ($q) => $q->whereBetween('date', [$request->month.'-01', date('Y-m-t', strtotime($request->month.'-01'))]))
            ->when($request->filled('class_id'), fn ($q) => $q->whereHas('user', fn ($u) => $u->where('class_id', $request->class_id)))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('shift'), fn ($q) => $q->whereHas('schedule', fn ($schedule) => $schedule->where('shift', $request->shift)))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest('date');
    }

    private function restrictedClassId(Request $request): ?int
    {
        $user = $request->user();

        return $user->role === 'km' || $user->role === 'wali_kelas'
            ? (int) $user->class_id
            : null;
    }
}
