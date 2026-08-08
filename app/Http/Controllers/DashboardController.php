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
        $role = $user->role;

        $logs = PiketLog::query()->whereDate('date', today());
        $schedules = PiketSchedule::query()->where('day_of_week', now()->englishDayOfWeek);
        $students = User::query()->whereIn('role', ['siswa', 'km']);
        $classes = SchoolClass::query();

        if (in_array($role, ['km', 'guru'], true) && $user->class_id) {
            $logs->whereHas('user', fn ($query) => $query->where('class_id', $user->class_id));
            $schedules->whereHas('user', fn ($query) => $query->where('class_id', $user->class_id));
            $students->where('class_id', $user->class_id);
            $classes->whereKey($user->class_id);
        } elseif ($role === 'siswa') {
            $logs->where('user_id', $user->id);
            $schedules->where('user_id', $user->id);
        }

        $data = [
            'school' => $school,
            'role' => $role,
            'morningScheduleCount' => (clone $schedules)->where('shift', 'morning')->count(),
            'afternoonScheduleCount' => (clone $schedules)->where('shift', 'afternoon')->count(),
            'pendingCount' => (clone $logs)->where('status', 'pending')->count(),
            'approvedCount' => (clone $logs)->where('status', 'approved')->count(),
            'studentCount' => $students->count(),
            'classCount' => $classes->count(),
        ];

        if (in_array($role, ['siswa', 'km'], true)) {
            $data['mySchedules'] = (clone $schedules)->where('user_id', $user->id)->get()->keyBy('shift');
            $data['mySchedule'] = $data['mySchedules']->first();
            $data['myLogs'] = (clone $logs)
                ->where('user_id', $user->id)
                ->with('schedule')
                ->latest()
                ->get()
                ->keyBy(fn (PiketLog $log) => $log->schedule?->shift ?? 'unknown');
            $data['myLog'] = $data['myLogs']->first();
        }

        if (in_array($role, ['km', 'guru'], true)) {
            $data['memberCount'] = $students->count();
            $data['scheduleCount'] = (clone $schedules)->count();
            $data['morningScheduleCount'] = (clone $schedules)->where('shift', 'morning')->count();
            $data['afternoonScheduleCount'] = (clone $schedules)->where('shift', 'afternoon')->count();
            $data['submittedCount'] = (clone $logs)->whereIn('status', ['pending', 'approved'])->distinct()->count('user_id');

            $scheduledUserIds = (clone $schedules)->pluck('user_id')->unique();
            $submittedUserIds = (clone $logs)->pluck('user_id')->unique();
            $data['unsubmitted'] = User::query()
                ->whereIn('id', $scheduledUserIds)
                ->whereNotIn('id', $submittedUserIds)
                ->with('schoolClass')
                ->orderBy('name')
                ->get();
        }

        return view('dashboard', $data);
    }
}
