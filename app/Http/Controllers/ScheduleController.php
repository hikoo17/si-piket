<?php

namespace App\Http\Controllers;

use App\Models\PiketSchedule;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $schedules = PiketSchedule::with('user.schoolClass');
        $users = User::whereIn('role', ['siswa', 'km']);
        if ($request->user()->role === 'km') {
            $schedules->whereHas('user', fn ($query) => $query->where('class_id', $request->user()->class_id));
            $users->where('class_id', $request->user()->class_id);
        }

        return view('schedules.index', ['schedules' => $schedules->paginate(20), 'users' => $users->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'day_of_week' => [
                'required',
                Rule::in(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']),
                Rule::unique('piket_schedules')->where(fn ($query) => $query->where('user_id', $request->input('user_id'))),
            ],
        ], [
            'day_of_week.unique' => 'Siswa tersebut sudah memiliki jadwal pada hari yang dipilih.',
        ]);
        $target = User::findOrFail($data['user_id']);
        abort_if($request->user()->role === 'km' && $target->class_id !== $request->user()->class_id, 403);
        PiketSchedule::query()->create($data);

        return back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function destroy(Request $request, PiketSchedule $schedule): RedirectResponse
    {
        abort_if($request->user()->role === 'km' && $schedule->user->class_id !== $request->user()->class_id, 403);
        $schedule->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}
