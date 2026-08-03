<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $students = User::query()
            ->with('schoolClass.school')
            ->whereIn('role', ['siswa', 'km'])
            ->when($request->filled('class_id'), fn ($query) => $query->where('class_id', $request->integer('class_id')))
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($query) => $query
                ->where('name', 'like', '%'.$request->string('search').'%')
                ->orWhere('email', 'like', '%'.$request->string('search').'%')))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('students.index', ['students' => $students, 'classes' => $this->classes()]);
    }

    public function create(Request $request): View
    {
        $student = new User(['class_id' => $request->integer('class_id') ?: null, 'role' => 'siswa']);

        return view('students.form', ['student' => $student, 'classes' => $this->classes()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('students.index', ['class_id' => $data['class_id']])->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }

    public function edit(User $student): View
    {
        abort_unless(in_array($student->role, ['siswa', 'km'], true), 404);

        return view('students.form', ['student' => $student, 'classes' => $this->classes()]);
    }

    public function update(Request $request, User $student): RedirectResponse
    {
        abort_unless(in_array($student->role, ['siswa', 'km'], true), 404);
        $data = $this->validated($request, $student);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        $student->update($data);

        return redirect()->route('students.index', ['class_id' => $data['class_id']])->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(User $student): RedirectResponse
    {
        abort_unless(in_array($student->role, ['siswa', 'km'], true), 404);
        $student->delete();

        return back()->with('success', 'Siswa berhasil dihapus.');
    }

    private function validated(Request $request, ?User $student = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($student)],
            'phone' => ['nullable', 'regex:/^62[0-9]{8,13}$/'],
            'role' => ['required', Rule::in(['siswa', 'km'])],
            'class_id' => ['required', 'exists:classes,id'],
            'password' => [$student ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    private function classes()
    {
        return SchoolClass::with('school')
            ->where('school_id', School::primary()->id)
            ->orderBy('name')
            ->get();
    }
}
