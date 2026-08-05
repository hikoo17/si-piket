@extends('layouts.app', ['title' => 'Pengaturan Sekolah'])

@section('content')
<div class="max-w-4xl space-y-6">
    <!-- Header Section -->
    <div class="space-y-1">
        <span class="inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
            Konfigurasi Aplikasi
        </span>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pengaturan Sekolah</h1>
        <p class="text-sm text-slate-500">
            Kelola profil, titik koordinat lokasi, radius jangkauan, dan batasan jam operasional sekolah.
        </p>
    </div>

    <!-- Form Section -->
    <form class="form-card space-y-6 p-6 sm:p-8" method="POST" action="{{ route('schools.update', $school) }}">
        @csrf
        @method('PUT')

        <!-- Nama Sekolah -->
        <div class="space-y-1.5">
            <label for="school-name" class="text-sm font-medium text-slate-700">Nama Sekolah</label>
            <input id="school-name" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="name" type="text" value="{{ old('name', $school->name) }}" required>
        </div>

        <hr class="border-slate-100">

        <!-- Peta & Lokasi -->
        <div class="space-y-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-sm font-medium text-slate-800">Posisi & Titik Koordinat</h3>
                    <p class="text-xs text-slate-500">Klik peta atau geser penanda ke lokasi tepat sekolah.</p>
                </div>
                <button id="use-current-location" class="btn btn-secondary" type="button">
                    Gunakan lokasi saya
                </button>
            </div>

            <div class="flex gap-2">
                <input id="location-search" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" type="search" name="address" value="{{ old('address', $school->address) }}" placeholder="Cari nama lokasi atau alamat..." autocomplete="off">
                <button id="search-location" class="btn btn-primary" type="button">Cari</button>
            </div>
            
            <div id="location-results" class="space-y-1 text-xs text-slate-600"></div>

            <div id="school-location-map" class="h-80 w-full overflow-hidden rounded-xl border border-slate-200 shadow-inner" data-default-latitude="-7.32709600" data-default-longitude="108.22034900" data-location-catalog='@json($locationCatalog)'></div>
            
            <div class="flex items-center gap-2 rounded-xl bg-slate-50 p-3 text-xs text-slate-600">
                <span class="inline-block h-2.5 w-2.5 rounded-full bg-rose-500 ring-4 ring-rose-100"></span>
                <span>Area transparan pada peta menandai jangkauan radius lokasi sekolah.</span>
            </div>
            
            <p id="location-status" class="text-xs text-slate-500" aria-live="polite"></p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-1.5">
                <label for="school-latitude" class="text-xs font-semibold text-slate-600">Latitude</label>
                <input id="school-latitude" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="latitude" type="number" min="-90" max="90" step="0.00000001" value="{{ old('latitude', $school->latitude) }}" required>
            </div>
            <div class="space-y-1.5">
                <label for="school-longitude" class="text-xs font-semibold text-slate-600">Longitude</label>
                <input id="school-longitude" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="longitude" type="number" min="-180" max="180" step="0.00000001" value="{{ old('longitude', $school->longitude) }}" required>
            </div>
        </div>

        <div class="space-y-1.5">
            <label for="school-radius" class="text-xs font-semibold text-slate-600">Radius (Meter)</label>
            <input id="school-radius" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="radius_meters" type="number" min="10" max="1000" step="1" value="{{ old('radius_meters', $school->radius_meters) }}" required>
        </div>

        <hr class="border-slate-100">

        <!-- Jam Upload -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium text-slate-800">Jam Piket Pagi</h3>
                <p class="text-xs text-slate-500">Atur waktu siswa dapat mengirim bukti piket pagi.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600">Jam Mulai Pagi</label>
                <input class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="upload_start_time" type="time" value="{{ old('upload_start_time', $school->upload_start_time ? substr($school->upload_start_time, 0, 5) : '') }}" required>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-600">Batas Akhir Pagi</label>
                <input class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="upload_deadline" type="time" value="{{ old('upload_deadline', $school->upload_deadline ? substr($school->upload_deadline, 0, 5) : '') }}" required>
            </div>
            </div>
        </div>

        <div class="space-y-4 rounded-xl border border-amber-200 bg-amber-50/50 p-4">
            <div>
                <h3 class="text-sm font-medium text-slate-800">Jam Piket Pulang</h3>
                <p class="text-xs text-slate-500">Atur waktu siswa dapat mengirim bukti piket setelah kegiatan sekolah.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-600">Jam Mulai Pulang</label>
                    <input class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="return_upload_start_time" type="time" value="{{ old('return_upload_start_time', $school->return_upload_start_time ? substr($school->return_upload_start_time, 0, 5) : '14:00') }}" required>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-600">Batas Akhir Pulang</label>
                    <input class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="return_upload_deadline" type="time" value="{{ old('return_upload_deadline', $school->return_upload_deadline ? substr($school->return_upload_deadline, 0, 5) : '17:00') }}" required>
                </div>
            </div>
        </div>

        <hr class="border-slate-100">

        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium text-slate-800">Notifikasi WhatsApp</h3>
                <p class="text-xs text-slate-500">Pengingat dikirim ke nomor WhatsApp siswa/KM yang memiliki jadwal hari itu.</p>
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" name="whatsapp_enabled" value="1" @checked(old('whatsapp_enabled', $school->whatsapp_enabled))> Aktifkan pengingat otomatis</label>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="space-y-1.5 text-xs font-semibold text-slate-600">Jam Pengiriman
                    <input class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm font-normal text-slate-900" name="whatsapp_send_time" type="time" value="{{ old('whatsapp_send_time', $school->whatsapp_send_time ? substr($school->whatsapp_send_time, 0, 5) : '06:00') }}" required>
                </label>
            </div>
            <label class="block space-y-1.5 text-xs font-semibold text-slate-600">Template Pesan
                <textarea class="min-h-24 w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm font-normal text-slate-900" name="whatsapp_message_template" maxlength="1000" placeholder="Halo {nama}, hari ini jadwal piket kamu di kelas {kelas}.">{{ old('whatsapp_message_template', $school->whatsapp_message_template) }}</textarea>
                <span class="font-normal text-slate-500">Placeholder: <code>{nama}</code>, <code>{kelas}</code>, <code>{jenis_piket}</code>, <code>{hari}</code>, <code>{tanggal}</code>.</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2 flex justify-end">
            <button type="submit" class="btn btn-primary w-full sm:w-auto">
                Simpan Perubahan
            </button>
        </div>
    </form>

    <form class="form-card space-y-4 p-6 sm:p-8" method="POST" action="{{ route('schools.test-whatsapp', $school) }}">
        @csrf
        <div><h3 class="text-sm font-medium text-slate-800">Tes Koneksi Fonnte</h3><p class="text-xs text-slate-500">Kirim satu pesan percobaan tanpa mengubah pengaturan di atas.</p></div>
        <div class="flex flex-col gap-3 sm:flex-row"><div class="w-full"><input class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900" name="test_whatsapp_phone" type="text" value="{{ old('test_whatsapp_phone') }}" placeholder="628xxxxxxxxxx" required inputmode="numeric" aria-describedby="test-whatsapp-help"><p id="test-whatsapp-help" class="mt-1 text-xs text-slate-500">Wajib diisi dengan format 628xxxxxxxxxx.</p>@error('test_whatsapp_phone')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div><button class="btn btn-secondary whitespace-nowrap" type="submit">Kirim Pesan Tes</button></div>
    </form>
</div>
@endsection
