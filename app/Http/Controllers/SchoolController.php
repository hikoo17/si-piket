<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function index(): View
    {
        return view('schools.form', [
            'school' => $this->primarySchool(),
            'locationCatalog' => $this->locationCatalog(),
        ]);
    }

    public function edit(School $school): View
    {
        abort_unless($school->is($this->primarySchool()), 404);

        return view('schools.form', [
            'school' => $school,
            'locationCatalog' => $this->locationCatalog(),
        ]);
    }

    public function update(Request $request, School $school): RedirectResponse
    {
        abort_unless($school->is($this->primarySchool()), 404);
        $school->update($this->validated($request));

        return redirect()->route('schools.index')->with('success', 'Sekolah berhasil diperbarui.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'google_place_id' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'between:10,1000'],
            'upload_start_time' => ['required', 'date_format:H:i'],
            // A schedule may cross midnight, for example 22:00 to 02:00.
            'upload_deadline' => ['required', 'date_format:H:i'],
        ]);
    }

    private function locationCatalog(): array
    {
        $savedSchools = School::query()
            ->select(['name', 'address', 'latitude', 'longitude'])
            ->get()
            ->map(fn (School $school) => [
                'name' => $school->name,
                'address' => $school->address ?: $school->name,
                'latitude' => (float) $school->latitude,
                'longitude' => (float) $school->longitude,
                'source' => 'Tersimpan',
            ])
            ->all();

        return [
            [
                'name' => 'SMAN 1 Tasikmalaya',
                'address' => 'Jl. Rumah Sakit Umum No. 28, Empangsari, Kec. Tawang, Kota Tasikmalaya, Jawa Barat',
                'latitude' => -7.327096,
                'longitude' => 108.220349,
                'source' => 'Google Maps',
            ],
            ...$savedSchools,
        ];
    }

    private function primarySchool(): School
    {
        return School::primary();
    }
}
