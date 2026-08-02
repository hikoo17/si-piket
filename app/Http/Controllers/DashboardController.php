<?php

namespace App\Http\Controllers;

use App\Models\PiketLog;
use App\Models\PiketSchedule;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $logs = PiketLog::query()->whereDate('date', today());

        if ($user->role === 'km') {
            $logs->whereHas('user', fn ($query) => $query->where('class_id', $user->class_id));
        } elseif ($user->role === 'siswa') {
            $logs->where('user_id', $user->id);
        }

        return view('dashboard', [
            'scheduleCount' => PiketSchedule::query()->where('day_of_week', now()->englishDayOfWeek)->count(),
            'pendingCount' => (clone $logs)->where('status', 'pending')->count(),
            'approvedCount' => (clone $logs)->where('status', 'approved')->count(),
            'studentCount' => User::query()->whereIn('role', ['siswa', 'km'])->count(),
            'classCount' => SchoolClass::query()->count(),
        ]);
    }
}
