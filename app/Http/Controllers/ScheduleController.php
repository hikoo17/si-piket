<?php

namespace App\Http\Controllers;

use App\Models\PiketSchedule;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $schedulesQuery = PiketSchedule::with('user.schoolClass');
        if ($request->user()->role === 'km') {
            $schedulesQuery->whereHas('user', fn ($query) => $query->where('class_id', $request->user()->class_id));
        }

        $schedulesQuery
            ->when($request->filled('search'), fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery
                ->where('name', 'like', '%'.$request->string('search').'%')))
            ->when($request->filled('class_id'), fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery
                ->where('class_id', $request->integer('class_id'))))
            ->when($request->filled('day_of_week'), fn ($query) => $query->where('day_of_week', $request->input('day_of_week')))
            ->when($request->filled('shift'), fn ($query) => $query->where('shift', $request->input('shift')));

        $all = $schedulesQuery
            ->orderBy('user_id')
            ->orderBy('day_of_week')
            ->orderBy('shift')
            ->get();

        $groups = $all->groupBy(fn (PiketSchedule $schedule) => $schedule->user_id.'|'.$schedule->day_of_week)->values();

        $perPage = 20;
        $page = $request->integer('page', 1);
        $schedules = new LengthAwarePaginator(
            $groups->forPage($page, $perPage),
            $groups->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        $classes = User::query()
            ->with('schoolClass')
            ->whereIn('role', ['siswa', 'km'])
            ->whereNotNull('class_id')
            ->when($request->user()->role === 'km', fn ($query) => $query->where('class_id', $request->user()->class_id))
            ->get()
            ->pluck('schoolClass')
            ->filter()
            ->unique('id')
            ->sortBy('name');

        return view('schedules.index', [
            'schedules' => $schedules,
            'classes' => $classes,
        ]);
    }

    public function create(Request $request): View
    {
        return view('schedules.form', ['users' => $this->availableUsers($request), 'schedule' => new PiketSchedule]);
    }

    public function edit(Request $request, PiketSchedule $schedule): View
    {
        abort_if($request->user()->role === 'km' && $schedule->user->class_id !== $request->user()->class_id, 403);

        return view('schedules.form', ['users' => $this->availableUsers($request), 'schedule' => $schedule]);
    }

    private function availableUsers(Request $request)
    {
        return User::query()
            ->with('schoolClass')
            ->whereIn('role', ['siswa', 'km'])
            ->whereNotNull('class_id')
            ->when($request->user()->role === 'km', fn ($query) => $query->where('class_id', $request->user()->class_id))
            ->get()
            ->sortBy(fn (User $user) => ($user->schoolClass?->name ?? '').$user->name);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'day_of_week' => [
                'required',
                Rule::in(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']),
            ],
        ]);
        $target = User::findOrFail($data['user_id']);
        if (! in_array($target->role, ['siswa', 'km'], true) || ! $target->class_id) {
            throw ValidationException::withMessages(['user_id' => 'Jadwal hanya dapat diberikan kepada siswa atau KM yang memiliki kelas.']);
        }
        abort_if($request->user()->role === 'km' && $target->class_id !== $request->user()->class_id, 403);

        $created = 0;
        foreach (['morning', 'afternoon'] as $shift) {
            $schedule = PiketSchedule::query()->firstOrCreate([
                'user_id' => $target->id,
                'day_of_week' => $data['day_of_week'],
                'shift' => $shift,
            ]);
            if ($schedule->wasRecentlyCreated) {
                $created++;
            }
        }

        return redirect()->route('schedules.index')->with(
            'success',
            $created > 0
                ? 'Jadwal piket pagi & pulang berhasil ditambahkan.'
                : 'Siswa tersebut sudah memiliki jadwal pada hari yang dipilih.',
        );
    }

    public function update(Request $request, PiketSchedule $schedule): RedirectResponse
    {
        abort_if($request->user()->role === 'km' && $schedule->user->class_id !== $request->user()->class_id, 403);

        $data = $request->validate([
            'day_of_week' => ['required', Rule::in(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'])],
        ]);

        $conflict = PiketSchedule::query()
            ->where('user_id', $schedule->user_id)
            ->where('day_of_week', $data['day_of_week'])
            ->where('day_of_week', '<>', $schedule->day_of_week)
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages(['day_of_week' => 'Siswa tersebut sudah memiliki jadwal pada hari yang dipilih.']);
        }

        PiketSchedule::query()
            ->where('user_id', $schedule->user_id)
            ->where('day_of_week', $schedule->day_of_week)
            ->update(['day_of_week' => $data['day_of_week']]);

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Request $request, PiketSchedule $schedule): RedirectResponse
    {
        abort_if($request->user()->role === 'km' && $schedule->user->class_id !== $request->user()->class_id, 403);

        PiketSchedule::query()->where('user_id', $schedule->user_id)
            ->where('day_of_week', $schedule->day_of_week)
            ->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}
