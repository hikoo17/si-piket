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
        return view('classes.index', ['classes' => SchoolClass::with('school')->paginate(15)]);
    }

    public function create(): View
    {
        return view('classes.form', ['class' => new SchoolClass, 'schools' => School::all()]);
    }

    public function edit(SchoolClass $class): View
    {
        return view('classes.form', ['class' => $class, 'schools' => School::all()]);
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
        $class->delete();

        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    private function validated(Request $request, ?SchoolClass $class = null): array
    {
        return $request->validate([
            'school_id' => ['required', 'exists:schools,id'],
            'name' => ['required', 'string', 'max:100', Rule::unique('classes')->where('school_id', $request->input('school_id'))->ignore($class)],
        ]);
    }
}
