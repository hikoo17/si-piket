@extends('layouts.app', ['title' => 'Pengaturan Sekolah'])

@section('content')
<div class="mx-auto max-w-5xl space-y-6 pb-12">
    <div class="space-y-1.5">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pengaturan Sekolah</h1>
        <p class="text-sm text-slate-500">Kelola profil, koordinat presensi, radius jangkauan, & pengingat WhatsApp dalam satu halaman.</p>
    </div>

    <!-- Single Card: semua pengaturan -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
        <form class="divide-y divide-slate-100" method="POST" action="{{ route('schools.update', $school) }}">
            @csrf
            @method('PUT')

            <!-- Section 1: Identitas -->
            <section class="space-y-4 p-6 sm:p-8">
                <header class="flex items-center gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-indigo-50 text-indigo-600">
                        <x-icon name="heroicon-o-building-office-2" class="h-5 w-5" />
                    </span>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Profil & Identitas</h2>
                        <p class="text-xs text-slate-500">Informasi umum sekolah yang terdaftar dalam sistem piket.</p>
                    </div>
                </header>
                <div class="space-y-1.5">
                    <label for="school-name" class="text-xs font-semibold uppercase tracking-wider text-slate-600">Nama Sekolah</label>
                    <input id="school-name" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="name" type="text" value="{{ old('name', $school->name) }}" placeholder="Contoh: SMAN 2 Tasikmalaya" required>
                </div>
            </section>

            <!-- Section 2: Lokasi & Radius -->
            <section class="space-y-6 p-6 sm:p-8">
                <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-indigo-50 text-indigo-600">
                            <x-icon name="heroicon-o-map-pin" class="h-5 w-5" />
                        </span>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Lokasi & Radius Presensi</h2>
                            <p class="text-xs text-slate-500">Tentukan titik pusat sekolah & batas radius area piket siswa.</p>
                        </div>
                    </div>
                    <button id="use-current-location" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 transition hover:bg-slate-100 active:bg-slate-200" type="button">
                        <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                        Gunakan Lokasi Saya
                    </button>
                </header>

                <div class="space-y-2">
                    <label for="location-search" class="text-xs font-semibold uppercase tracking-wider text-slate-600">Cari Alamat / Nama Tempat</label>
                    <div class="flex gap-2">
                        <div class="relative w-full">
                            <input id="location-search" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" type="search" name="address" value="{{ old('address', $school->address) }}" placeholder="Ketik nama tempat, jalan, atau daerah..." autocomplete="off">
                        </div>
                        <button id="search-location" class="inline-flex shrink-0 items-center rounded-xl bg-indigo-600 px-5 py-3 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-500 active:scale-95" type="button">
                            <x-icon name="heroicon-o-magnifying-glass" class="h-4 w-4" />
                            <span class="hidden sm:inline">Cari</span>
                        </button>
                    </div>
                    <div id="location-results" class="space-y-1 text-xs"></div>
                </div>

                <div class="space-y-3">
                    <div class="relative">
                        <div id="school-location-map" class="h-80 w-full rounded-2xl border border-slate-200 shadow-inner z-0 sm:h-[22rem]" data-default-latitude="-7.32709600" data-default-longitude="108.22034900"></div>
                        <script id="location-catalog" type="application/json">{!! json_encode($locationCatalog, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
                    </div>

                    <div class="flex items-center justify-between gap-2 rounded-xl border border-rose-100 bg-rose-50/60 p-3 text-xs text-rose-800">
                        <div class="flex items-center gap-2">
                            <span class="relative inline-flex h-3 w-3 shrink-0">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-400 opacity-60"></span>
                                <span class="relative inline-flex h-3 w-3 rounded-full bg-rose-500 ring-4 ring-rose-200/60"></span>
                            </span>
                            <span>Garis lingkaran merah pada peta menandai batas radius jangkauan presensi (angka pada peta = jarak dalam meter).</span>
                        </div>
                    </div>
                    <p id="location-status" class="text-xs font-medium text-slate-500" aria-live="polite"></p>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="space-y-1.5">
                        <label for="school-latitude" class="text-xs font-semibold uppercase tracking-wider text-slate-600">Latitude</label>
                        <input id="school-latitude" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="latitude" type="number" min="-90" max="90" step="0.00000001" value="{{ old('latitude', $school->latitude) }}" placeholder="Contoh: -7.32709600" required>
                    </div>
                    <div class="space-y-1.5">
                        <label for="school-longitude" class="text-xs font-semibold uppercase tracking-wider text-slate-600">Longitude</label>
                        <input id="school-longitude" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="longitude" type="number" min="-180" max="180" step="0.00000001" value="{{ old('longitude', $school->longitude) }}" placeholder="Contoh: 108.22034900" required>
                    </div>
                    <div class="space-y-1.5">
                        <label for="school-radius" class="text-xs font-semibold uppercase tracking-wider text-slate-600">Radius Jangkauan (Meter)</label>
                        <div class="relative">
                            <input id="school-radius" class="w-full rounded-xl border border-indigo-200 bg-indigo-50/40 px-3 py-3 pr-12 text-sm font-semibold text-indigo-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="radius_meters" type="number" min="10" max="2000" step="1" value="{{ old('radius_meters', $school->radius_meters ?? 100) }}" placeholder="100" required>
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-indigo-400">m</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 3: Waktu Piket -->
            <section class="space-y-4 p-6 sm:p-8">
                <header class="flex items-center gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-indigo-50 text-indigo-600">
                        <x-icon name="heroicon-o-clock" class="h-5 w-5" />
                    </span>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Jadwal & Waktu Piket</h2>
                        <p class="text-xs text-slate-500">Batasan jam upload foto bukti piket pagi & piket pulang.</p>
                    </div>
                </header>

                <div class="space-y-3 rounded-xl border border-indigo-200/80 bg-indigo-50/40 p-4">
                    <h3 class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-indigo-900">
                        <x-icon name="heroicon-o-sun" class="h-4 w-4 text-indigo-500" />
                        Piket Pagi
                    </h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-indigo-800">Jam Mulai Upload</label>
                            <input class="w-full rounded-xl border border-indigo-200 bg-white p-3 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="upload_start_time" type="time" value="{{ old('upload_start_time', $school->upload_start_time ? substr($school->upload_start_time, 0, 5) : '06:00') }}" placeholder="06:00" required>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-indigo-800">Batas Akhir Upload</label>
                            <input class="w-full rounded-xl border border-indigo-200 bg-white p-3 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="upload_deadline" type="time" value="{{ old('upload_deadline', $school->upload_deadline ? substr($school->upload_deadline, 0, 5) : '07:15') }}" placeholder="07:15" required>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 rounded-xl border border-amber-200/80 bg-amber-50/40 p-4">
                    <h3 class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-900">
                        <x-icon name="heroicon-o-moon" class="h-4 w-4 text-amber-500" />
                        Piket Pulang Sekolah
                    </h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-amber-800">Jam Mulai Upload</label>
                            <input class="w-full rounded-xl border border-amber-200 bg-white p-3 text-sm text-slate-900 focus:border-amber-500 focus:outline-none focus:ring-4 focus:ring-amber-500/10" name="return_upload_start_time" type="time" value="{{ old('return_upload_start_time', $school->return_upload_start_time ? substr($school->return_upload_start_time, 0, 5) : '14:00') }}" placeholder="14:00" required>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-amber-800">Batas Akhir Upload</label>
                            <input class="w-full rounded-xl border border-amber-200 bg-white p-3 text-sm text-slate-900 focus:border-amber-500 focus:outline-none focus:ring-4 focus:ring-amber-500/10" name="return_upload_deadline" type="time" value="{{ old('return_upload_deadline', $school->return_upload_deadline ? substr($school->return_upload_deadline, 0, 5) : '17:00') }}" placeholder="17:00" required>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 4: WhatsApp -->
            <section class="space-y-4 p-6 sm:p-8">
                <header class="flex items-center gap-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                        <x-icon name="heroicon-o-chat-bubble-left-right" class="h-5 w-5" />
                    </span>
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Pengingat WhatsApp Otomatis</h2>
                        <p class="text-xs text-slate-500">Atur pesan notifikasi harian ke siswa yang bertugas piket.</p>
                    </div>
                </header>

                <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50/50 p-4 cursor-pointer transition hover:bg-slate-100/60">
                    <input type="checkbox" name="whatsapp_enabled" value="1" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" @checked(old('whatsapp_enabled', $school->whatsapp_enabled))>
                    <div>
                        <span class="block text-sm font-semibold text-slate-900">Aktifkan Notifikasi WhatsApp</span>
                        <span class="text-xs text-slate-500">Pesan pengingat akan otomatis dikirim sesuai jam di bawah ini.</span>
                    </div>
                </label>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5 rounded-xl border border-indigo-200/80 bg-indigo-50/40 p-4">
                        <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-indigo-900">
                            <x-icon name="heroicon-o-sun" class="h-4 w-4 text-indigo-500" />
                            Piket Pagi
                        </label>
                        <input class="mt-1 w-full rounded-xl border border-indigo-200 bg-white px-3 py-2.5 text-sm text-slate-900 transition focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10" name="whatsapp_send_time" type="time" value="{{ old('whatsapp_send_time', $school->whatsapp_send_time ? substr($school->whatsapp_send_time, 0, 5) : '06:00') }}" placeholder="06:00" required>
                    </div>
                    <div class="space-y-1.5 rounded-xl border border-amber-200/80 bg-amber-50/40 p-4">
                        <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-amber-900">
                            <x-icon name="heroicon-o-moon" class="h-4 w-4 text-amber-500" />
                            Piket Pulang
                        </label>
                        <input class="mt-1 w-full rounded-xl border border-amber-200 bg-white px-3 py-2.5 text-sm text-slate-900 transition focus:border-amber-500 focus:outline-none focus:ring-4 focus:ring-amber-500/10" name="whatsapp_send_time_return" type="time" value="{{ old('whatsapp_send_time_return', $school->whatsapp_send_time_return ? substr($school->whatsapp_send_time_return, 0, 5) : '14:00') }}" placeholder="14:00" required>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Template Pesan</label>
                    <textarea class="min-h-28 w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900 transition focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10" name="whatsapp_message_template" maxlength="1000" placeholder="Halo {nama}, hari ini jadwal piket kamu di kelas {kelas}.">{{ old('whatsapp_message_template', $school->whatsapp_message_template) }}</textarea>
                    <p class="text-xs text-slate-500">Placeholder yang tersedia: <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-emerald-600">{nama}</code>, <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-emerald-600">{kelas}</code>, <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-emerald-600">{jenis_piket}</code>, <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-emerald-600">{hari}</code>, <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-emerald-600">{tanggal}</code>.</p>
                </div>
            </section>

            <!-- Submit -->
            <div class="flex justify-end p-6 pt-4 sm:p-8 sm:pt-4">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-8 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 active:scale-95 sm:w-auto">
                    <x-icon name="heroicon-o-check-circle" class="h-5 w-5" />
                    Simpan Semua Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Test WhatsApp (card terpisah) -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
        <div class="border-b border-emerald-100 bg-emerald-50/50 px-6 py-4 sm:px-8">
            <header class="flex items-center gap-3">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-emerald-100 text-emerald-600">
                    <x-icon name="heroicon-o-paper-airplane" class="h-5 w-5" />
                </span>
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Tes Koneksi Fonnte (WhatsApp)</h2>
                    <p class="text-xs text-slate-500">Uji coba kirim satu pesan ke nomor WhatsApp tanpa mengubah konfigurasi di atas.</p>
                </div>
            </header>
        </div>
        <div class="p-6 sm:p-8">
            <form method="POST" action="{{ route('schools.test-whatsapp', $school) }}" class="space-y-3">
                @csrf
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="w-full">
                        <input class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-900 transition focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10" name="test_whatsapp_phone" type="text" value="{{ old('test_whatsapp_phone') }}" placeholder="Contoh: 628123456789" required inputmode="numeric">
                        <p class="mt-1 text-xs text-slate-500">Gunakan awalan <code class="font-semibold text-slate-700">628...</code></p>
                        @error('test_whatsapp_phone')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button class="inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-500 active:scale-95 sm:w-auto" type="button" onclick="this.form.submit()">
                        <x-icon name="heroicon-o-paper-airplane" class="h-4 w-4" />
                        Kirim Pesan Tes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/app.js'])
@endpush
