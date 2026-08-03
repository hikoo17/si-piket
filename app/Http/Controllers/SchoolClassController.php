<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function index(): View
    {
        return view('classes.index', [
            'classes' => SchoolClass::with('school')
                ->withCount(['users as student_count' => fn ($query) => $query->whereIn('role', ['siswa', 'km'])])
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('classes.form', ['class' => new SchoolClass, 'school' => $this->primarySchool()]);
    }

    public function show(SchoolClass $class): View
    {
        $class->load('school')->loadCount(['users as student_count' => fn ($query) => $query->whereIn('role', ['siswa', 'km'])]);

        return view('classes.show', [
            'class' => $class,
            'students' => $class->users()->whereIn('role', ['siswa', 'km'])->orderBy('name')->paginate(20),
        ]);
    }

    public function edit(SchoolClass $class): View
    {
        return view('classes.form', ['class' => $class, 'school' => $this->primarySchool()]);
    }

    public function store(Request $request): RedirectResponse
    {
        SchoolClass::query()->create($this->validated($request));

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $class->update($this->validated($request, $class));

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        if ($class->users()->exists()) {
            return back()->withErrors(['class' => 'Kelas yang masih memiliki anggota tidak dapat dihapus. Pindahkan atau hapus siswa terlebih dahulu.']);
        }
        $class->delete();

        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    private function validated(Request $request, ?SchoolClass $class = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('classes')->where('school_id', $this->primarySchool()->id)->ignore($class)],
        ]);
        $data['school_id'] = $this->primarySchool()->id;

        return $data;
    }

    private function primarySchool(): School
    {
        return School::primary();
    }
}
