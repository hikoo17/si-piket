<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

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
        $data = $this->validated($request);
        if ($request->has('whatsapp_enabled') || $request->has('whatsapp_send_time')) {
            $data['whatsapp_enabled'] = $request->boolean('whatsapp_enabled');
        }
        $school->update($data);

        return redirect()->route('schools.index')->with('success', 'Sekolah berhasil diperbarui.');
    }

    public function testWhatsapp(Request $request, School $school, WhatsAppService $whatsApp): RedirectResponse
    {
        abort_unless($school->is($this->primarySchool()), 404);
        $data = $request->validate([
            'test_whatsapp_phone' => ['bail', 'required', 'string', 'regex:/^62[0-9]{8,13}$/'],
        ]);

        try {
            $whatsApp->send($data['test_whatsapp_phone'], 'Tes notifikasi WhatsApp dari SI Piket.');
        } catch (RuntimeException $exception) {
            return redirect()->route('schools.index')
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        return redirect()->route('schools.index')->with('success', 'Pesan tes WhatsApp berhasil dikirim.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'between:10,1000'],
            'upload_start_time' => ['required', 'date_format:H:i'],
            // A schedule may cross midnight, for example 22:00 to 02:00.
            'upload_deadline' => ['required', 'date_format:H:i'],
            'return_upload_start_time' => ['required', 'date_format:H:i'],
            'return_upload_deadline' => ['required', 'date_format:H:i'],
            'whatsapp_enabled' => ['nullable', 'boolean'],
            'whatsapp_send_time' => ['sometimes', 'required', 'date_format:H:i'],
            'whatsapp_message_template' => ['nullable', 'string', 'max:1000'],
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
