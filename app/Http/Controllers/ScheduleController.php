<?php

namespace App\Http\Controllers;

use App\Models\PiketSchedule;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $schedules = PiketSchedule::with('user.schoolClass');
        if ($request->user()->role === 'km') {
            $schedules->whereHas('user', fn ($query) => $query->where('class_id', $request->user()->class_id));
        }

        $schedules
            ->when($request->filled('search'), fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery
                ->where('name', 'like', '%'.$request->string('search').'%')))
            ->when($request->filled('class_id'), fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery
                ->where('class_id', $request->integer('class_id'))))
            ->when($request->filled('day_of_week'), fn ($query) => $query->where('day_of_week', $request->input('day_of_week')))
            ->when($request->filled('shift'), fn ($query) => $query->where('shift', $request->input('shift')))
            ->latest('id');

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
            'schedules' => $schedules->paginate(20)->withQueryString(),
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
                Rule::unique('piket_schedules')->where(fn ($query) => $query
                    ->where('user_id', $request->input('user_id'))
                    ->where('shift', $request->input('shift'))),
            ],
            'shift' => ['required', Rule::in(['morning', 'afternoon'])],
        ], [
            'day_of_week.unique' => 'Siswa tersebut sudah memiliki jenis piket yang sama pada hari yang dipilih.',
        ]);
        $target = User::findOrFail($data['user_id']);
        if (! in_array($target->role, ['siswa', 'km'], true) || ! $target->class_id) {
            throw ValidationException::withMessages(['user_id' => 'Jadwal hanya dapat diberikan kepada siswa atau KM yang memiliki kelas.']);
        }
        abort_if($request->user()->role === 'km' && $target->class_id !== $request->user()->class_id, 403);
        PiketSchedule::query()->create($data);

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function update(Request $request, PiketSchedule $schedule): RedirectResponse
    {
        abort_if($request->user()->role === 'km' && $schedule->user->class_id !== $request->user()->class_id, 403);

        $data = $request->validate([
            'day_of_week' => [
                'required',
                Rule::in(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']),
                Rule::unique('piket_schedules')->where(fn ($query) => $query
                    ->where('user_id', $schedule->user_id)
                    ->where('shift', $request->input('shift')))->ignore($schedule->id),
            ],
            'shift' => ['required', Rule::in(['morning', 'afternoon'])],
        ], [
            'day_of_week.unique' => 'Siswa tersebut sudah memiliki jenis piket yang sama pada hari yang dipilih.',
        ]);

        $schedule->update($data);

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Request $request, PiketSchedule $schedule): RedirectResponse
    {
        abort_if($request->user()->role === 'km' && $schedule->user->class_id !== $request->user()->class_id, 403);
        $schedule->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}
