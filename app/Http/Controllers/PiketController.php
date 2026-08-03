<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\PiketLog;
use App\Models\PiketSchedule;
use App\Services\GeoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PiketController extends Controller
{
    public function create(Request $request): View
    {
        abort_unless(in_array($request->user()->role, ['siswa', 'km'], true), 403);

        return view('piket.upload');
    }

    public function storeUpload(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'photo' => ['required', 'string'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['required', 'numeric', 'between:0,1000'],
        ]);

        $user = $request->user();
        abort_unless(in_array($user->role, ['siswa', 'km'], true), 403);
        if ((float) $validated['accuracy'] > 300) {
            return back()->withInput()->with('error', 'Akurasi lokasi masih di atas 300 meter. Aktifkan GPS dan lokasi presisi, lalu coba lagi.');
        }
        $school = $user->schoolClass?->school;

        if (! $school) {
            return back()->withInput()->with('error', 'Data sekolah pengguna tidak ditemukan.');
        }

        $schedule = PiketSchedule::query()
            ->whereBelongsTo($user)
            ->where('day_of_week', now()->englishDayOfWeek)
            ->first();

        if (! $schedule) {
            return back()->withInput()->with('error', 'Kamu tidak memiliki jadwal piket hari ini.');
        }

        $currentTime = now()->format('H:i');
        $startTime = substr($school->upload_start_time, 0, 5);
        $deadlineTime = substr($school->upload_deadline, 0, 5);
        $withinSchedule = $startTime <= $deadlineTime
            ? $currentTime >= $startTime && $currentTime <= $deadlineTime
            : $currentTime >= $startTime || $currentTime <= $deadlineTime;

        if (! $withinSchedule) {
            return back()->withInput()->with('error', 'Upload hanya dapat dilakukan pada jam piket yang ditentukan.');
        }

        $existingLog = $schedule->logs()->whereDate('date', today())->first();

        if ($existingLog && $existingLog->status === 'approved') {
            return back()->withInput()->with('error', 'Bukti piket hari ini sudah disetujui.');
        }

        $distance = (int) round(GeoService::getDistanceMeters(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            (float) $school->latitude,
            (float) $school->longitude,
        ));

        if ($distance > $school->radius_meters) {
            AuditLog::create(['user_id' => $user->id, 'action' => 'piket.upload.rejected_geofence', 'metadata' => ['distance' => $distance], 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);

            return back()->withInput()->with(
                'error',
                'Gagal! Kamu berada di luar area sekolah ('.$distance.'m dari lokasi).',
            );
        }

        [$image, $extension] = $this->decodePhoto($validated['photo']);
        $path = $existingLog?->photo_path ?: 'piket/'.Str::uuid().'.'.$extension;

        if ($existingLog?->photo_path) {
            Storage::disk('public')->delete($existingLog->photo_path);
        }

        if (! Storage::disk('public')->put($path, $image)) {
            return back()->withInput()->with('error', 'Gagal menyimpan foto bukti piket.');
        }

        try {
            if ($existingLog) {
                $existingLog->update([
                    'photo_path' => $path,
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'distance_meters' => $distance,
                    'accuracy_meters' => $validated['accuracy'],
                    'location_captured_at' => now(),
                    'photo_captured_at' => now(),
                    'status' => 'pending',
                ]);
                $log = $existingLog;
                AuditLog::create(['user_id' => $user->id, 'action' => 'piket.upload.resubmitted', 'auditable_type' => PiketLog::class, 'auditable_id' => $log->id, 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);
            } else {
                $log = PiketLog::query()->create([
                    'schedule_id' => $schedule->id,
                    'user_id' => $user->id,
                    'date' => today(),
                    'photo_path' => $path,
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'distance_meters' => $distance,
                    'accuracy_meters' => $validated['accuracy'],
                    'location_captured_at' => now(), 'photo_captured_at' => now(), 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent(),
                    'status' => 'pending',
                ]);
                AuditLog::create(['user_id' => $user->id, 'action' => 'piket.upload.submitted', 'auditable_type' => PiketLog::class, 'auditable_id' => $log->id, 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);
            }
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);

            throw $exception;
        }

        return back()->with('success', $existingLog ? 'Bukti piket berhasil diperbarui! Menunggu verifikasi Guru/KM.' : 'Berhasil upload bukti piket! Menunggu verifikasi Guru/KM.');
    }

    /**
     * @return array{string, string}
     */
    private function decodePhoto(string $photo): array
    {
        if (! preg_match('/^data:image\/(jpeg|png|webp);base64,([A-Za-z0-9+\/=\r\n]+)$/', $photo, $matches)) {
            throw ValidationException::withMessages([
                'photo' => 'Format foto tidak valid. Gunakan gambar JPEG, PNG, atau WebP.',
            ]);
        }

        $image = base64_decode($matches[2], true);

        if ($image === false || strlen($image) > 5 * 1024 * 1024) {
            throw ValidationException::withMessages([
                'photo' => 'Foto tidak valid atau ukurannya melebihi 5 MB.',
            ]);
        }

        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->buffer($image);
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (! isset($extensions[$mimeType])) {
            throw ValidationException::withMessages([
                'photo' => 'Isi foto bukan gambar JPEG, PNG, atau WebP yang valid.',
            ]);
        }

        return [$image, $extensions[$mimeType]];
    }
}
