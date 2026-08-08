@extends('layouts.app', ['title' => 'Pengaturan Sekolah'])

@section('content')
<div class="mx-auto max-w-5xl space-y-8 pb-12">
    <!-- Header Section -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pengaturan Sekolah</h1>
            <p class="text-sm text-slate-500">Kelola profil sekolah, koordinat presensi, radius jangkauan, dan pengingat WhatsApp.</p>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form class="space-y-6" method="POST" action="{{ route('schools.update', $school) }}">
        @csrf
        @method('PUT')

        <!-- Card 1: Identitas Sekolah -->
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8 space-y-4">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-semibold text-slate-900">Profil & Identitas</h2>
                <p class="text-xs text-slate-500">Informasi umum sekolah yang terdaftar dalam sistem piket.</p>
            </div>
            
            <div class="space-y-1.5">
                <label for="school-name" class="text-xs font-semibold uppercase tracking-wider text-slate-600">Nama Sekolah</label>
                <input id="school-name" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="name" type="text" value="{{ old('name', $school->name) }}" placeholder="Contoh: SMKN 2 Tasikmalaya" required>
            </div>
        </div>

        <!-- Card 2: Lokasi & Radius Peta -->
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8 space-y-6">
            <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Lokasi & Radius Presensi</h2>
                    <p class="text-xs text-slate-500">Tentukan titik pusat lokasi sekolah dan batas radius area siswa melakukan piket.</p>
                </div>
                <button id="use-current-location" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-200 active:bg-slate-300 transition" type="button">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    Gunakan Lokasi Saya
                </button>
            </div>

            <!-- Search Field -->
            <div class="space-y-2">
                <label for="location-search" class="text-xs font-semibold uppercase tracking-wider text-slate-600">Cari Alamat / Nama Tempat</label>
                <div class="flex gap-2">
                    <div class="relative w-full">
                        <input id="location-search" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" type="search" name="address" value="{{ old('address', $school->address) }}" placeholder="Ketik nama tempat, jalan, atau daerah..." autocomplete="off">
                        <svg class="absolute left-3.5 top-3.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <button id="search-location" class="rounded-xl bg-indigo-600 px-5 py-3 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 transition active:scale-95" type="button">
                        Cari
                    </button>
                </div>
                <div id="location-results" class="space-y-1 text-xs"></div>
            </div>

            <!-- Map Display -->
            <div class="space-y-3">
                <div id="school-location-map" class="h-80 w-full rounded-2xl border border-slate-200 shadow-inner z-0" data-default-latitude="-7.32709600" data-default-longitude="108.22034900"></div>
                <script id="location-catalog" type="application/json">{!! json_encode($locationCatalog, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
                
                <div class="flex items-center justify-between gap-2 rounded-xl bg-rose-50/60 border border-rose-100 p-3 text-xs text-rose-800">
                    <div class="flex items-center gap-2">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-rose-500 ring-4 ring-rose-200/60 animate-pulse"></span>
                        <span>Area lingkaran merah transparan menandai batas radius jangkauan presensi sekolah.</span>
                    </div>
                </div>
                <p id="location-status" class="text-xs font-medium text-slate-500" aria-live="polite"></p>
            </div>

            <!-- Lat, Long & Radius Inputs -->
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="space-y-1.5">
                    <label for="school-latitude" class="text-xs font-semibold uppercase tracking-wider text-slate-600">Latitude</label>
                    <input id="school-latitude" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="latitude" type="number" min="-90" max="90" step="0.00000001" value="{{ old('latitude', $school->latitude) }}" required>
                </div>
                <div class="space-y-1.5">
                    <label for="school-longitude" class="text-xs font-semibold uppercase tracking-wider text-slate-600">Longitude</label>
                    <input id="school-longitude" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="longitude" type="number" min="-180" max="180" step="0.00000001" value="{{ old('longitude', $school->longitude) }}" required>
                </div>
                <div class="space-y-1.5">
                    <label for="school-radius" class="text-xs font-semibold uppercase tracking-wider text-slate-600">Radius Jangkauan (Meter)</label>
                    <input id="school-radius" class="w-full rounded-xl border border-indigo-200 bg-indigo-50/30 p-3 text-sm font-semibold text-indigo-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="radius_meters" type="number" min="10" max="2000" step="1" value="{{ old('radius_meters', $school->radius_meters ?? 100) }}" required>
                </div>
            </div>
        </div>

        <!-- Card 3: Waktu Piket -->
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-semibold text-slate-900">Jadwal & Waktu Piket</h2>
                <p class="text-xs text-slate-500">Batasan jam upload foto bukti piket pagi dan piket pulang.</p>
            </div>

            <!-- Piket Pagi -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Piket Pagi</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-slate-600">Jam Mulai Upload</label>
                        <input class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:outline-none" name="upload_start_time" type="time" value="{{ old('upload_start_time', $school->upload_start_time ? substr($school->upload_start_time, 0, 5) : '06:00') }}" required>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-slate-600">Batas Akhir Upload</label>
                        <input class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:outline-none" name="upload_deadline" type="time" value="{{ old('upload_deadline', $school->upload_deadline ? substr($school->upload_deadline, 0, 5) : '07:15') }}" required>
                    </div>
                </div>
            </div>

            <!-- Piket Pulang -->
            <div class="space-y-3 rounded-xl border border-amber-200/80 bg-amber-50/40 p-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900">Piket Pulang Sekolah</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-amber-800">Jam Mulai Upload</label>
                        <input class="w-full rounded-xl border border-amber-200 bg-white p-3 text-sm text-slate-900 focus:border-amber-500 focus:outline-none" name="return_upload_start_time" type="time" value="{{ old('return_upload_start_time', $school->return_upload_start_time ? substr($school->return_upload_start_time, 0, 5) : '14:00') }}" required>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-amber-800">Batas Akhir Upload</label>
                        <input class="w-full rounded-xl border border-amber-200 bg-white p-3 text-sm text-slate-900 focus:border-amber-500 focus:outline-none" name="return_upload_deadline" type="time" value="{{ old('return_upload_deadline', $school->return_upload_deadline ? substr($school->return_upload_deadline, 0, 5) : '17:00') }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: WhatsApp Notification -->
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-semibold text-slate-900">Pengingat WhatsApp Otomatis</h2>
                <p class="text-xs text-slate-500">Atur pesan notifikasi harian yang dikirim ke siswa yang bertugas piket.</p>
            </div>

            <div class="space-y-4">
                <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50/50 p-4 cursor-pointer hover:bg-slate-100/60 transition">
                    <input type="checkbox" name="whatsapp_enabled" value="1" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(old('whatsapp_enabled', $school->whatsapp_enabled))>
                    <div>
                        <span class="text-sm font-semibold text-slate-900 block">Aktifkan Notifikasi WhatsApp</span>
                        <span class="text-xs text-slate-500">Pesan pengingat akan otomatis dikirim sesuai jam di bawah ini.</span>
                    </div>
                </label>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Jam Pengiriman Pesan</label>
                    <input class="w-full sm:w-1/2 rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:outline-none" name="whatsapp_send_time" type="time" value="{{ old('whatsapp_send_time', $school->whatsapp_send_time ? substr($school->whatsapp_send_time, 0, 5) : '06:00') }}" required>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Template Pesan</label>
                    <textarea class="min-h-28 w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:outline-none" name="whatsapp_message_template" maxlength="1000" placeholder="Halo {nama}, hari ini jadwal piket kamu di kelas {kelas}.">{{ old('whatsapp_message_template', $school->whatsapp_message_template) }}</textarea>
                    <p class="text-xs text-slate-500">Placeholder yang tersedia: <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-indigo-600">{nama}</code>, <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-indigo-600">{kelas}</code>, <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-indigo-600">{jenis_piket}</code>, <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-indigo-600">{hari}</code>, <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-indigo-600">{tanggal}</code>.</p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-2">
            <button type="submit" class="w-full sm:w-auto rounded-xl bg-indigo-600 px-8 py-3.5 text-sm font-semibold text-white shadow-md hover:bg-indigo-500 transition active:scale-95">
                Simpan Semua Perubahan
            </button>
        </div>
    </form>

    <!-- Test WhatsApp Card -->
    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8 space-y-4">
        <div class="border-b border-slate-100 pb-4">
            <h2 class="text-base font-semibold text-slate-900">Tes Koneksi Fonnte (WhatsApp)</h2>
            <p class="text-xs text-slate-500">Uji coba kirim satu pesan ke nomor WhatsApp tanpa mengubah konfigurasi di atas.</p>
        </div>

        <form method="POST" action="{{ route('schools.test-whatsapp', $school) }}" class="space-y-3">
            @csrf
            <div class="flex flex-col gap-3 sm:flex-row">
                <div class="w-full">
                    <input class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900 focus:border-indigo-500 focus:bg-white focus:outline-none" name="test_whatsapp_phone" type="text" value="{{ old('test_whatsapp_phone') }}" placeholder="Contoh: 628123456789" required inputmode="numeric">
                    <p class="mt-1 text-xs text-slate-500">Gunakan awalan <code class="font-semibold text-slate-700">628...</code></p>
                    @error('test_whatsapp_phone')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button class="h-11 whitespace-nowrap rounded-xl bg-slate-800 px-6 text-xs font-semibold text-white hover:bg-slate-700 transition active:scale-95" type="button" onclick="this.form.submit()">
                    Kirim Pesan Tes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/app.js'])
@endpush