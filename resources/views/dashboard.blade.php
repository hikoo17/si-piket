@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
    ['users.index', 'Pengguna', 'heroicon-o-users'],
]])

@section('content')
<div class="space-y-6">
    <!-- Hero / Welcome Banner -->
<section class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-amber-400 via-yellow-400 to-amber-500 p-7 text-amber-950 shadow-[0_24px_70px_rgba(217,119,6,.18)] before:absolute before:-right-24 before:-top-36 before:h-96 before:w-96 before:rounded-full before:border-[55px] before:border-white/20 lg:p-10">

    <div class="relative z-[1] grid items-end gap-8 lg:grid-cols-[1fr_auto]">

        <div class="max-w-2xl">

            <h1 class="text-[clamp(2rem,4vw,3.7rem)] font-semibold leading-[1.04] tracking-[-.055em]">Selamat datang, <span class="rounded-lg bg-white/80 px-2 font-serif font-normal italic text-amber-950 shadow-sm">{{ explode(' ', auth()->user()->name)[0] }}</span>.</h1>

            <p class="mt-4 max-w-xl text-sm font-medium leading-7 text-amber-950/75">Pantau aktivitas piket dan selesaikan hal penting hari ini dari satu ruang kerja yang ringkas.</p>

        </div>

        <div class="flex min-w-[210px] items-center gap-4 rounded-2xl border border-amber-950/10 bg-white/30 p-4 backdrop-blur-sm">

            <div class="grid h-12 w-12 place-items-center rounded-2xl bg-white text-amber-700"><x-icon name="heroicon-o-clock" class="h-6 w-6" /></div>

            <div><span class="block text-[.62rem] uppercase tracking-[.14em] text-[#d6aa90]">Waktu lokal</span><strong class="mt-1 block text-xl">{{ now()->format('H:i') }} WIB</strong></div>

        </div>

    </div>

</section>

    <!-- Stats Cards Grid -->
    @php
        $stats = [
            ['Piket Pagi', $morningScheduleCount, 'heroicon-o-clock', 'Jadwal pagi hari ini', 'text-amber-600 bg-amber-50 border-amber-100'],
            ['Piket Pulang', $afternoonScheduleCount, 'heroicon-o-clock', 'Jadwal pulang hari ini', 'text-indigo-600 bg-indigo-50 border-indigo-100'],
            ['Perlu Ditinjau', $pendingCount, 'heroicon-o-exclamation-triangle', 'Menunggu verifikasi', 'text-amber-600 bg-amber-50 border-amber-100'],
            ['Sudah Disetujui', $approvedCount, 'heroicon-o-check-circle', 'Bukti piket valid', 'text-emerald-600 bg-emerald-50 border-emerald-100'],
            ['Siswa & KM', $studentCount, 'heroicon-o-users', 'Pengguna terdaftar', 'text-slate-600 bg-slate-100 border-slate-200'],
        ];
    @endphp

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach($stats as [$label, $value, $icon, $description, $style])
            <article class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300 hover:shadow-md">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $label }}</span>
                    <span class="grid h-10 w-10 place-items-center rounded-xl border {{ $style }}">
                        <x-icon name="{{ $iconMap[$icon] ?? 'heroicon-o-question-mark-circle' }}" class="h-5 w-5" />
                    </span>
                </div>
                <div class="flex items-baseline justify-between gap-2">
                    <strong class="text-3xl font-bold tracking-tight text-slate-900">{{ str_pad($value, 2, '0', STR_PAD_LEFT) }}</strong>
                    <span class="text-xs text-slate-500">{{ $description }}</span>
                </div>
            </article>
        @endforeach
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-lg font-bold tracking-tight text-slate-900">Jam Piket Hari Ini</h2>
                <p class="text-xs text-slate-500">Ikuti rentang waktu yang ditentukan admin. Upload di luar jam ini akan ditolak.</p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ now()->translatedFormat('l, d F Y') }}</span>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-amber-700">Piket Masuk / Pagi</p>
                <p class="mt-2 text-2xl font-bold text-amber-950">{{ substr($school->upload_start_time, 0, 5) }}–{{ substr($school->upload_deadline, 0, 5) }}</p>
                <p class="mt-1 text-xs text-amber-800">Foto bukti hanya dapat dikirim pada jam ini.</p>
            </div>
            <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-indigo-700">Piket Pulang</p>
                <p class="mt-2 text-2xl font-bold text-indigo-950">{{ substr($school->return_upload_start_time, 0, 5) }}–{{ substr($school->return_upload_deadline, 0, 5) }}</p>
                <p class="mt-1 text-xs text-indigo-800">Foto bukti hanya dapat dikirim pada jam ini.</p>
            </div>
        </div>
    </section>

    <!-- Main Content & Side Note Section -->
    <div class="grid gap-6 xl:grid-cols-[1.35fr_.65fr]">
        <!-- Akses Cepat / Quick Actions -->
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold tracking-tight text-slate-900">Akses Cepat</h2>
                    <p class="text-xs text-slate-500">Pilih menu pekerjaan sesuai hak akses Anda</p>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                @if(auth()->user()->role==='admin')
                    <a class="group flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/50 p-4 transition hover:border-amber-200 hover:bg-amber-50/30" href="{{ route('schools.index') }}">
                        <div class="flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-lg bg-white border border-slate-200 text-slate-700 shadow-sm transition group-hover:border-amber-300 group-hover:text-amber-600">
                                <x-icon name="heroicon-o-academic-cap" class="h-5 w-5" />
                            </span>
                            <div>
                                <strong class="block text-sm font-semibold text-slate-800 group-hover:text-amber-600">Pengaturan Sekolah</strong>
                                <small class="text-xs text-slate-500">Profil, lokasi & jam upload</small>
                            </div>
                        </div>
                        <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600">→</span>
                    </a>

                    <a class="group flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/50 p-4 transition hover:border-amber-200 hover:bg-amber-50/30" href="{{ route('classes.index') }}">
                        <div class="flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-lg bg-white border border-slate-200 text-slate-700 shadow-sm transition group-hover:border-amber-300 group-hover:text-amber-600">
                                <x-icon name="heroicon-o-squares-2x2" class="h-5 w-5" />
                            </span>
                            <div>
                                <strong class="block text-sm font-semibold text-slate-800 group-hover:text-amber-600">Kelola Kelas</strong>
                                <small class="text-xs text-slate-500">Susunan kelas aktif</small>
                            </div>
                        </div>
                        <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600">→</span>
                    </a>

                    <a class="group flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/50 p-4 transition hover:border-amber-200 hover:bg-amber-50/30" href="{{ route('students.index') }}">
                        <div class="flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-lg bg-white border border-slate-200 text-slate-700 shadow-sm transition group-hover:border-amber-300 group-hover:text-amber-600">
                                <x-icon name="heroicon-o-user-group" class="h-5 w-5" />
                            </span>
                            <div>
                                <strong class="block text-sm font-semibold text-slate-800 group-hover:text-amber-600">Kelola Siswa</strong>
                                <small class="text-xs text-slate-500">Anggota kelas dan KM</small>
                            </div>
                        </div>
                        <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600">→</span>
                    </a>

                    <a class="group flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/50 p-4 transition hover:border-amber-200 hover:bg-amber-50/30" href="{{ route('users.index') }}">
                        <div class="flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-lg bg-white border border-slate-200 text-slate-700 shadow-sm transition group-hover:border-amber-300 group-hover:text-amber-600">
                                <x-icon name="heroicon-o-users" class="h-5 w-5" />
                            </span>
                            <div>
                                <strong class="block text-sm font-semibold text-slate-800 group-hover:text-amber-600">Kelola Pengguna</strong>
                                <small class="text-xs text-slate-500">Akun dan hak akses</small>
                            </div>
                        </div>
                        <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600">→</span>
                    </a>
                @endif

                @if(in_array(auth()->user()->role,['siswa','km']))
                    <a class="group flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/50 p-4 transition hover:border-amber-200 hover:bg-amber-50/30" href="{{ route('piket.upload.form') }}">
                        <div class="flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-lg bg-white border border-slate-200 text-slate-700 shadow-sm transition group-hover:border-amber-300 group-hover:text-amber-600">
                                <x-icon name="heroicon-o-qr-code" class="h-5 w-5" />
                            </span>
                            <div>
                                <strong class="block text-sm font-semibold text-slate-800 group-hover:text-amber-600">Ambil Bukti Piket</strong>
                                <small class="text-xs text-slate-500">Foto dan kirim bukti</small>
                            </div>
                        </div>
                        <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600">→</span>
                    </a>
                @endif

                @if(in_array(auth()->user()->role,['admin','km']))
                    <a class="group flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/50 p-4 transition hover:border-amber-200 hover:bg-amber-50/30" href="{{ route('schedules.index') }}">
                        <div class="flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-lg bg-white border border-slate-200 text-slate-700 shadow-sm transition group-hover:border-amber-300 group-hover:text-amber-600">
                                <x-icon name="heroicon-o-clock" class="h-5 w-5" />
                            </span>
                            <div>
                                <strong class="block text-sm font-semibold text-slate-800 group-hover:text-amber-600">Atur Jadwal</strong>
                                <small class="text-xs text-slate-500">Kelola giliran piket</small>
                            </div>
                        </div>
                        <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600">→</span>
                    </a>
                @endif

                @if(in_array(auth()->user()->role,['admin','guru','km']))
                    <a class="group flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/50 p-4 transition hover:border-amber-200 hover:bg-amber-50/30" href="{{ route('verification.index') }}">
                        <div class="flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-lg bg-white border border-slate-200 text-slate-700 shadow-sm transition group-hover:border-amber-300 group-hover:text-amber-600">
                                <x-icon name="heroicon-o-shield-check" class="h-5 w-5" />
                            </span>
                            <div>
                                <strong class="block text-sm font-semibold text-slate-800 group-hover:text-amber-600">Verifikasi Bukti</strong>
                                <small class="text-xs text-slate-500">Tinjau kiriman terbaru</small>
                            </div>
                        </div>
                        <span class="text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600">→</span>
                    </a>
                @endif
            </div>
        </section>

        <!-- Side Card Info -->
        <aside class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-slate-50 p-6 sm:p-7">
            <div>
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-amber-500 text-white shadow-sm">
                    <x-icon name="heroicon-o-chart-bar" class="h-5 w-5" />
                </span>
                <span class="mt-6 block text-xs font-bold uppercase tracking-wider text-slate-400">Catatan Hari Ini</span>
                <h3 class="mt-2 text-xl font-bold tracking-tight text-slate-900">Konsistensi kecil membangun budaya sekolah.</h3>
                <p class="mt-3 text-xs leading-relaxed text-slate-600">
                    Pastikan setiap bukti piket diambil secara langsung dari lokasi sekolah dan diunggah sesuai tenggat waktu yang ditentukan.
                </p>
            </div>
            
            <div class="mt-6 border-t border-slate-200/80 pt-4 text-xs font-medium text-slate-500">
                Piket App &bull; SMAN 1 Tasikmalaya
            </div>
        </aside>
    </div>
</div>
@endsection
