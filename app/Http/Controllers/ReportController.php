<?php

namespace App\Http\Controllers;

use App\Models\PiketLog;
use App\Models\SchoolClass;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('reports.index', ['logs' => $this->query($request)->paginate(30)->withQueryString(), 'classes' => SchoolClass::all(), 'students' => User::whereIn('role', ['siswa', 'km'])->get()]);
    }

    public function show(Request $request, PiketLog $log): View
    {
        $log->load(['user.schoolClass', 'schedule', 'verifier']);

        if ($request->user()->role === 'km' && $log->user->class_id !== $request->user()->class_id) {
            throw new AccessDeniedHttpException;
        }

        return view('reports.show', compact('log'));
    }

    public function csv(Request $request): StreamedResponse
    {
        $logs = $this->query($request)->get();

        return response()->streamDownload(function () use ($logs) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tanggal', 'Siswa', 'Kelas', 'Jenis Piket', 'Status', 'Jarak (m)', 'Akurasi (m)']);
            foreach ($logs as $log) {
                fputcsv($out, [$log->date->format('Y-m-d'), $log->user->name, $log->user->schoolClass?->name, $log->schedule?->shift_label, $log->status, $log->distance_meters, $log->accuracy_meters]);
            } fclose($out);
        }, 'laporan-piket.csv', ['Content-Type' => 'text/csv']);
    }

    public function pdf(Request $request)
    {
        return Pdf::loadView('reports.pdf', ['logs' => $this->query($request)->get()])->download('laporan-piket.pdf');
    }

    private function query(Request $request)
    {
        return PiketLog::with(['user.schoolClass', 'schedule', 'verifier'])->when($request->filled('month'), fn ($q) => $q->whereBetween('date', [$request->month.'-01', date('Y-m-t', strtotime($request->month.'-01'))]))->when($request->filled('class_id'), fn ($q) => $q->whereHas('user', fn ($u) => $u->where('class_id', $request->class_id)))->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->user_id))->when($request->filled('shift'), fn ($q) => $q->whereHas('schedule', fn ($schedule) => $schedule->where('shift', $request->shift)))->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))->latest('date');
    }
}
