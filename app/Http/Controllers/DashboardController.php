<?php

namespace App\Http\Controllers;

use App\Models\PiketLog;
use App\Models\PiketSchedule;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $school = School::primary();
        $logs = PiketLog::query()->whereDate('date', today());
        $schedules = PiketSchedule::query()->where('day_of_week', now()->englishDayOfWeek);
        $students = User::query()->whereIn('role', ['siswa', 'km']);
        $classes = SchoolClass::query();

        if ($user->role === 'km' || ($user->role === 'guru' && $user->class_id)) {
            $logs->whereHas('user', fn ($query) => $query->where('class_id', $user->class_id));
            $schedules->whereHas('user', fn ($query) => $query->where('class_id', $user->class_id));
            $students->where('class_id', $user->class_id);
            $classes->whereKey($user->class_id);
        } elseif ($user->role === 'siswa') {
            $logs->where('user_id', $user->id);
        }

        return view('dashboard', [
            'school' => $school,
            'morningScheduleCount' => (clone $schedules)->where('shift', 'morning')->count(),
            'afternoonScheduleCount' => (clone $schedules)->where('shift', 'afternoon')->count(),
            'pendingCount' => (clone $logs)->where('status', 'pending')->count(),
            'approvedCount' => (clone $logs)->where('status', 'approved')->count(),
            'studentCount' => $students->count(),
            'classCount' => $classes->count(),
        ]);
    }
}
