<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\PiketLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function index(Request $request): View
    {
        $logs = PiketLog::with(['user.schoolClass', 'schedule'])->latest();
        if ($request->user()->role === 'km') {
            $logs->whereHas('user', fn ($query) => $query->where('class_id', $request->user()->class_id));
        }

        return view('verification.index', ['logs' => $logs->paginate(20)]);
    }

    public function approve(Request $request, PiketLog $log): RedirectResponse
    {
        $this->authorizeLog($request, $log);
        abort_unless($log->status === 'pending', 422);
        $log->update(['status' => 'approved', 'verified_by' => $request->user()->id, 'verified_at' => now(), 'rejection_reason' => null]);
        AuditLog::create(['user_id' => $request->user()->id, 'action' => 'piket.approved', 'auditable_type' => PiketLog::class, 'auditable_id' => $log->id, 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);

        return back()->with('success', 'Bukti piket disetujui.');
    }

    public function reject(Request $request, PiketLog $log): RedirectResponse
    {
        $this->authorizeLog($request, $log);
        abort_unless($log->status === 'pending', 422);
        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:255']]);
        $log->update($data + ['status' => 'rejected', 'verified_by' => $request->user()->id, 'verified_at' => now()]);
        AuditLog::create(['user_id' => $request->user()->id, 'action' => 'piket.rejected', 'auditable_type' => PiketLog::class, 'auditable_id' => $log->id, 'metadata' => $data, 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);

        return back()->with('success', 'Bukti piket ditolak.');
    }

    private function authorizeLog(Request $request, PiketLog $log): void
    {
        abort_if($request->user()->role === 'km' && $log->user->class_id !== $request->user()->class_id, 403);
    }
}
