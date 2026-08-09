@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
    ['users.index', 'Pengguna', 'heroicon-o-users'],
]])

@php
    $role = $role ?? auth()->user()->role;
    $scopeText = $scopeClass ? ('kelas ' . $scopeClass->name) : 'sekolah';

    $cards = [];
    if ($role === 'siswa') {
        $statusLabel = match ($myLog?->status) {
            'approved' => 'Disetujui',
            'pending' => 'Menunggu',
            'rejected' => 'Ditolak',
            default => 'Belum',
        };
        $cards = [
            ['Jadwal Piket', $mySchedule ? 1 : 0, 'heroicon-o-calendar-days', 'Giliran piket hari ini', 'text-amber-600 bg-amber-50 border-amber-200/60'],
            ['Bukti Terkirim', $myLog ? 1 : 0, 'heroicon-o-camera', 'Foto bukti hari ini', 'text-indigo-600 bg-indigo-50 border-indigo-200/60'],
            ['Status Bukti', $statusLabel, 'heroicon-o-information-circle', 'Verifikasi bukti piket', 'text-slate-600 bg-slate-100 border-slate-200'],
        ];
    } elseif ($role === 'km') {
        $cards = [
            ['Anggota Kelas', $memberCount, 'heroicon-o-users', 'Siswa & KM terdaftar', 'text-slate-600 bg-slate-100 border-slate-200'],
            ['Piket Pagi', $morningScheduleCount, 'heroicon-o-sun', 'Jadwal pagi ' . $scopeText, 'text-amber-600 bg-amber-50 border-amber-200/60'],
            ['Piket Pulang', $afternoonScheduleCount, 'heroicon-o-moon', 'Jadwal pulang ' . $scopeText, 'text-indigo-600 bg-indigo-50 border-indigo-200/60'],
            ['Belum Kirim', max(0, $memberCount - $submittedCount), 'heroicon-o-exclamation-triangle', 'Anggota belum upload', 'text-rose-600 bg-rose-50 border-rose-200/60'],
            ['Perlu Ditinjau', $pendingCount, 'heroicon-o-shield-check', 'Menunggu verifikasi', 'text-amber-600 bg-amber-50 border-amber-200/60'],
        ];
    } elseif (in_array($role, ['guru_piket', 'wali_kelas'])) {
        $cards = [
            ['Piket Pagi', $morningScheduleCount, 'heroicon-o-sun', 'Jadwal pagi ' . $scopeText, 'text-amber-600 bg-amber-50 border-amber-200/60'],
            ['Piket Pulang', $afternoonScheduleCount, 'heroicon-o-moon', 'Jadwal pulang ' . $scopeText, 'text-indigo-600 bg-indigo-50 border-indigo-200/60'],
            ['Perlu Ditinjau', $pendingCount, 'heroicon-o-shield-check', 'Menunggu verifikasi', 'text-amber-600 bg-amber-50 border-amber-200/60'],
            ['Disetujui', $approvedCount, 'heroicon-o-check-circle', 'Bukti piket valid', 'text-emerald-600 bg-emerald-50 border-emerald-200/60'],
            ['Anggota', $memberCount, 'heroicon-o-users', 'Siswa & KM ' . $scopeText, 'text-slate-600 bg-slate-100 border-slate-200'],
        ];
    } else {
        $cards = [
            ['Piket Pagi', $morningScheduleCount ?? 0, 'heroicon-o-sun', 'Jadwal pagi hari ini', 'text-amber-600 bg-amber-50 border-amber-200/60'],
            ['Piket Pulang', $afternoonScheduleCount ?? 0, 'heroicon-o-moon', 'Jadwal pulang hari ini', 'text-indigo-600 bg-indigo-50 border-indigo-200/60'],
            ['Perlu Ditinjau', $pendingCount ?? 0, 'heroicon-o-exclamation-triangle', 'Menunggu verifikasi', 'text-amber-600 bg-amber-50 border-amber-200/60'],
            ['Sudah Disetujui', $approvedCount ?? 0, 'heroicon-o-check-circle', 'Bukti piket valid', 'text-emerald-600 bg-emerald-50 border-emerald-200/60'],
            ['Siswa & KM', $studentCount ?? 0, 'heroicon-o-users', 'Pengguna terdaftar', 'text-slate-600 bg-slate-100 border-slate-200'],
        ];
    }

    $roleLabel = match ($role) {
        'siswa' => 'Siswa',
        'km' => 'Ketua Kelas',
        'guru_piket' => 'Guru Piket',
        'wali_kelas' => 'Wali Kelas',
        'admin' => 'Administrator',
        default => 'Pengguna',
    };
@endphp

@section('content')
<div class="space-y-5">
    <!-- Hero / Welcome Banner -->
    <section class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-amber-400 via-yellow-400 to-amber-500 p-7 text-amber-950 shadow-sm border border-amber-500/20 before:absolute before:-right-24 before:-top-36 before:h-96 before:w-96 before:rounded-full before:border-[55px] before:border-white/20 lg:p-10">
        <div class="relative z-[1] grid items-end gap-8 lg:grid-cols-[1fr_auto]">
            <div class="max-w-2xl">
                <h1 class="text-[clamp(2rem,4vw,3.7rem)] font-semibold leading-[1.04] tracking-[-.055em]">
                    Selamat datang, <span class="rounded-lg bg-white/80 px-2 font-serif font-normal italic text-amber-950 shadow-sm">{{ explode(' ', auth()->user()->name)[0] }}</span>.
                </h1>
                <p class="mt-4 max-w-xl text-sm font-medium leading-7 text-amber-950/75">
                    Pantau aktivitas piket sebagai <span class="font-bold">{{ $roleLabel }}</span> dan selesaikan hal penting hari ini dari satu ruang kerja yang ringkas.
                </p>
            </div>

            <div class="flex min-w-[210px] items-center gap-4 rounded-2xl border border-amber-950/10 bg-white/30 p-4 backdrop-blur-sm">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-white text-amber-700 shadow-sm">
                    <x-icon name="heroicon-o-clock" class="h-6 w-6" />
                </div>
                <div>
                    <span class="block text-[.62rem] font-bold uppercase tracking-[.14em] text-amber-900/60">Waktu lokal</span>
                    <strong class="mt-0.5 block text-xl font-bold text-amber-950">{{ now()->format('H:i') }} WIB</strong>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Cards Grid (per role) -->
    <div class="grid gap-3.5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        @foreach($cards as [$label, $value, $icon, $description, $style])
            <article class="group flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-slate-300">
                <div>
                    <div class="mb-3 flex items-center justify-between gap-2">
                        <span class="text-[0.7rem] font-bold uppercase tracking-wider text-slate-500">{{ $label }}</span>
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg border {{ $style }}">
                            <x-icon name="{{ $icon }}" class="h-4 w-4" />
                        </span>
                    </div>
                    <strong class="block text-2xl font-bold tracking-tight text-slate-900">
                        @if(is_numeric($value)){{ str_pad($value, 2, '0', STR_PAD_LEFT) }}@else{{ $value }}@endif
                    </strong>
                </div>
                <div class="mt-2.5 border-t border-slate-100 pt-2.5">
                    <span class="text-[0.7rem] font-medium text-slate-500">{{ $description }}</span>
                </div>
            </article>
        @endforeach
    </div>

    <!-- Role-specific Info Section -->
    @if($role === 'siswa')
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h2 class="text-base font-bold tracking-tight text-slate-900">Piket Saya Hari Ini</h2>
                <p class="text-xs text-slate-500">Lihat jadwal dan status bukti piket kamu hari ini.</p>
            </div>

            @if($mySchedules->isNotEmpty())
                @php
                    $morningSchedule = $mySchedules->get('morning');
                    $afternoonSchedule = $mySchedules->get('afternoon');
                    $morningLog = $myLogs->get('morning');
                    $afternoonLog = $myLogs->get('afternoon');
                    $statusLabelFor = fn ($log) => match ($log?->status) {
                        'approved' => 'Sudah diterima',
                        'pending' => 'Menunggu verifikasi',
                        'rejected' => 'Ditolak',
                        default => 'Belum mengirim',
                    };
                    $statusBadgeFor = fn ($log) => match ($log?->status) {
                        'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                        'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                        'rejected' => 'bg-rose-100 text-rose-700 border-rose-200',
                        default => 'bg-slate-100 text-slate-600 border-slate-200',
                    };
                @endphp
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <div class="flex flex-col justify-between rounded-lg border border-amber-200/80 bg-amber-50/50 p-4">
                        <div>
                            <div class="flex items-center gap-1.5 text-amber-800">
                                <x-icon name="heroicon-o-sun" class="h-4 w-4" />
                                <p class="text-xs font-bold uppercase tracking-wider">Piket Pagi</p>
                            </div>
                            <p class="mt-2 text-xl font-bold tracking-tight text-amber-950">
                                {{ substr($school->upload_start_time ?? '00:00', 0, 5) }} – {{ substr($school->upload_deadline ?? '00:00', 0, 5) }}
                            </p>
                        </div>
                        <p class="mt-1.5 text-xs text-amber-800/80">{{ $morningSchedule ? 'Jadwal aktif untuk kamu.' : 'Jadwal pagi belum tersedia.' }}</p>
                    </div>

                    <div class="flex flex-col justify-between rounded-lg border border-indigo-200/80 bg-indigo-50/50 p-4">
                        <div>
                            <div class="flex items-center gap-1.5 text-indigo-800">
                                <x-icon name="heroicon-o-moon" class="h-4 w-4" />
                                <p class="text-xs font-bold uppercase tracking-wider">Piket Pulang</p>
                            </div>
                            <p class="mt-2 text-xl font-bold tracking-tight text-indigo-950">
                                {{ substr($school->return_upload_start_time ?? '00:00', 0, 5) }} – {{ substr($school->return_upload_deadline ?? '00:00', 0, 5) }}
                            </p>
                        </div>
                        <p class="mt-1.5 text-xs text-indigo-800/80">{{ $afternoonSchedule ? 'Jadwal aktif untuk kamu.' : 'Jadwal pulang belum tersedia.' }}</p>
                    </div>

                    <div class="flex flex-col justify-between rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Status Bukti Piket</p>
                            <p class="mt-2">
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold {{ $statusBadgeFor($myLog) }}">{{ $statusLabelFor($myLog) }}</span>
                            </p>
                            <div class="mt-3 space-y-1.5 text-xs">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-slate-500">Bukti pagi</span>
                                    <span class="rounded-full border px-2 py-0.5 font-bold {{ $statusBadgeFor($morningLog) }}">{{ $statusLabelFor($morningLog) }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-slate-500">Bukti pulang</span>
                                    <span class="rounded-full border px-2 py-0.5 font-bold {{ $statusBadgeFor($afternoonLog) }}">{{ $statusLabelFor($afternoonLog) }}</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('piket.upload.form') }}" class="mt-3 inline-flex h-9 items-center justify-center gap-2 rounded-lg bg-amber-500 px-4 text-xs font-bold text-amber-950 shadow-sm transition hover:bg-amber-400">
                            <x-icon name="heroicon-o-camera" class="h-4 w-4" />
                            <span>{{ $myLog ? 'Ambil Bukti Ulang' : 'Ambil Bukti Piket' }}</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-slate-100 text-slate-400">
                        <x-icon name="heroicon-o-calendar-days" class="h-5 w-5" />
                    </span>
                    <h3 class="mt-2 text-xs font-bold text-slate-800">Tidak ada jadwal piket</h3>
                    <p class="mt-0.5 text-[0.7rem] text-slate-500">Kamu tidak memiliki jadwal piket yang terdaftar untuk hari ini.</p>
                </div>
            @endif
        </section>
    @elseif($role === 'km')
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold tracking-tight text-slate-900">Ringkasan Kelas</h2>
                    <p class="text-xs text-slate-500">Pantau keikutsertaan piket {{ $scopeClass ? 'Kelas ' . $scopeClass->name : 'seluruh sekolah' }} hari ini.</p>
                </div>
            </div>
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Sudah Kirim</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $submittedCount }} <span class="text-sm font-medium text-slate-400">/ {{ $memberCount }}</span></p>
                </div>
                <div class="rounded-lg border border-rose-200/80 bg-rose-50/50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-rose-700">Belum Kirim</p>
                    <p class="mt-1 text-2xl font-bold text-rose-700">{{ max(0, $memberCount - $submittedCount) }}</p>
                </div>
                <div class="rounded-lg border border-amber-200/80 bg-amber-50/50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-700">Perlu Ditinjau</p>
                    <p class="mt-1 text-2xl font-bold text-amber-700">{{ $pendingCount }}</p>
                </div>
            </div>

            @if($unsubmitted->isNotEmpty())
                <div class="mt-4 rounded-lg border border-rose-200/80 bg-rose-50/40 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-rose-700">Belum Kirim Bukti ({{ $unsubmitted->count() }})</p>
                    <ul class="mt-2 max-h-56 space-y-1.5 overflow-y-auto pr-1">
                        @foreach($unsubmitted as $student)
                            <li class="flex items-center justify-between gap-2 rounded-md bg-white px-3 py-2 text-xs">
                                <span class="font-semibold text-slate-800">{{ $student->name }}</span>
                                <span class="text-[0.7rem] text-slate-400">{{ $student->schoolClass?->name }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Bukti Piket Saya</p>
                <div class="mt-2 grid gap-2 sm:grid-cols-2">
                    @foreach(['morning' => 'Bukti pagi', 'afternoon' => 'Bukti pulang'] as $shift => $label)
                        @php
                            $log = $myLogs->get($shift);
                            $status = match ($log?->status) {
                                'approved' => ['Sudah diterima', 'bg-emerald-100 text-emerald-700 border-emerald-200'],
                                'pending' => ['Menunggu verifikasi', 'bg-amber-100 text-amber-700 border-amber-200'],
                                'rejected' => ['Ditolak', 'bg-rose-100 text-rose-700 border-rose-200'],
                                default => ['Belum mengirim', 'bg-slate-100 text-slate-600 border-slate-200'],
                            };
                        @endphp
                        <div class="flex items-center justify-between rounded-md bg-white px-3 py-2 text-xs">
                            <span class="font-semibold text-slate-700">{{ $label }}</span>
                            <span class="rounded-full border px-2 py-0.5 font-bold {{ $status[1] }}">{{ $status[0] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @elseif(in_array($role, ['guru_piket', 'wali_kelas']))
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h2 class="text-base font-bold tracking-tight text-slate-900">Verifikasi Bukti</h2>
                <p class="text-xs text-slate-500">Tinjau kiriman bukti piket {{ $scopeClass ? 'Kelas ' . $scopeClass->name : 'seluruh sekolah' }} yang masih menunggu persetujuan.</p>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-amber-200/80 bg-amber-50/50 p-4">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-white text-amber-700 shadow-sm">
                        <x-icon name="heroicon-o-shield-check" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="text-sm font-bold text-amber-950">{{ $pendingCount }} bukti menunggu verifikasi</p>
                        <p class="text-xs text-amber-800/80">Segera tinjau agar presensi tercatat.</p>
                    </div>
                </div>
                <a href="{{ route('verification.index') }}" class="inline-flex h-9 items-center justify-center gap-2 rounded-lg bg-amber-500 px-4 text-xs font-bold text-amber-950 shadow-sm transition hover:bg-amber-400">
                    <span>Verifikasi Sekarang</span>
                    <x-icon name="heroicon-o-arrow-right" class="h-3.5 w-3.5" />
                </a>
            </div>

            @if($unsubmitted->isNotEmpty())
                <div class="mt-4 rounded-lg border border-rose-200/80 bg-rose-50/40 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-rose-700">Belum Kirim Bukti ({{ $unsubmitted->count() }})</p>
                    <ul class="mt-2 max-h-56 space-y-1.5 overflow-y-auto pr-1">
                        @foreach($unsubmitted as $student)
                            <li class="flex items-center justify-between gap-2 rounded-md bg-white px-3 py-2 text-xs">
                                <span class="font-semibold text-slate-800">{{ $student->name }}</span>
                                <span class="text-[0.7rem] text-slate-400">{{ $student->schoolClass?->name }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>
    @else
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold tracking-tight text-slate-900">Jam Piket Hari Ini</h2>
                    <p class="text-xs text-slate-500">Ikuti rentang waktu yang ditentukan. Upload di luar jam ini akan ditolak.</p>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="flex flex-col justify-between rounded-lg border border-amber-200/80 bg-amber-50/50 p-4">
                    <div>
                        <div class="flex items-center gap-1.5 text-amber-800">
                            <x-icon name="heroicon-o-sun" class="h-4 w-4" />
                            <p class="text-xs font-bold uppercase tracking-wider">Piket Masuk</p>
                        </div>
                        <p class="mt-2 text-xl font-bold tracking-tight text-amber-950">
                            {{ substr($school->upload_start_time ?? '00:00', 0, 5) }} – {{ substr($school->upload_deadline ?? '00:00', 0, 5) }}
                        </p>
                    </div>
                    <p class="mt-1.5 text-xs text-amber-800/80">Foto bukti hanya dapat dikirim pada jam ini.</p>
                </div>

                <div class="flex flex-col justify-between rounded-lg border border-indigo-200/80 bg-indigo-50/50 p-4">
                    <div>
                        <div class="flex items-center gap-1.5 text-indigo-800">
                            <x-icon name="heroicon-o-moon" class="h-4 w-4" />
                            <p class="text-xs font-bold uppercase tracking-wider">Piket Pulang</p>
                        </div>
                        <p class="mt-2 text-xl font-bold tracking-tight text-indigo-950">
                            {{ substr($school->return_upload_start_time ?? '00:00', 0, 5) }} – {{ substr($school->return_upload_deadline ?? '00:00', 0, 5) }}
                        </p>
                    </div>
                    <p class="mt-1.5 text-xs text-indigo-800/80">Foto bukti hanya dapat dikirim pada jam ini.</p>
                </div>
            </div>
        </section>
    @endif

    <!-- Main Content & Side Note Section -->
    <div class="grid gap-5 xl:grid-cols-[1.35fr_.65fr]">
        <!-- Akses Cepat / Quick Actions -->
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h2 class="text-base font-bold tracking-tight text-slate-900">Akses Cepat</h2>
                <p class="text-xs text-slate-500">Pilih menu pekerjaan sesuai hak akses Anda</p>
            </div>

            <div class="grid gap-2.5 sm:grid-cols-2">
                @if($role === 'admin')
                    <a class="group flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50/50 p-3.5 transition hover:border-amber-400 hover:bg-amber-50/30" href="{{ route('schools.index') }}">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-white border border-slate-200 text-slate-700 shadow-sm transition group-hover:border-amber-400 group-hover:text-amber-600">
                                <x-icon name="heroicon-o-academic-cap" class="h-4 w-4" />
                            </span>
                            <div>
                                <strong class="block text-xs font-bold text-slate-800 group-hover:text-amber-900">Pengaturan Sekolah</strong>
                                <small class="text-[0.7rem] text-slate-500">Profil, lokasi & jam upload</small>
                            </div>
                        </div>
                        <x-icon name="heroicon-o-arrow-right" class="h-3.5 w-3.5 text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600" />
                    </a>

                    <a class="group flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50/50 p-3.5 transition hover:border-amber-400 hover:bg-amber-50/30" href="{{ route('classes.index') }}">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-white border border-slate-200 text-slate-700 shadow-sm transition group-hover:border-amber-400 group-hover:text-amber-600">
                                <x-icon name="heroicon-o-squares-2x2" class="h-4 w-4" />
                            </span>
                            <div>
                                 <strong class="block text-xs font-bold text-slate-800 group-hover:text-amber-900">Manajemen Kelas</strong>
                                <small class="text-[0.7rem] text-slate-500">Susunan kelas aktif</small>
                            </div>
                        </div>
                        <x-icon name="heroicon-o-arrow-right" class="h-3.5 w-3.5 text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600" />
                    </a>

                    <a class="group flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50/50 p-3.5 transition hover:border-amber-400 hover:bg-amber-50/30" href="{{ route('students.index') }}">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-white border border-slate-200 text-slate-700 shadow-sm transition group-hover:border-amber-400 group-hover:text-amber-600">
                                <x-icon name="heroicon-o-user-group" class="h-4 w-4" />
                            </span>
                            <div>
                                <strong class="block text-xs font-bold text-slate-800 group-hover:text-amber-900">Kelola Siswa</strong>
                                <small class="text-[0.7rem] text-slate-500">Anggota kelas dan KM</small>
                            </div>
                        </div>
                        <x-icon name="heroicon-o-arrow-right" class="h-3.5 w-3.5 text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600" />
                    </a>

                    <a class="group flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50/50 p-3.5 transition hover:border-amber-400 hover:bg-amber-50/30" href="{{ route('users.index') }}">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-white border border-slate-200 text-slate-700 shadow-sm transition group-hover:border-amber-400 group-hover:text-amber-600">
                                <x-icon name="heroicon-o-users" class="h-4 w-4" />
                            </span>
                            <div>
                                 <strong class="block text-xs font-bold text-slate-800 group-hover:text-amber-900">Manajemen Pengguna</strong>
                                <small class="text-[0.7rem] text-slate-500">Akun dan hak akses</small>
                            </div>
                        </div>
                        <x-icon name="heroicon-o-arrow-right" class="h-3.5 w-3.5 text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600" />
                    </a>
                @endif

                @if(in_array($role, ['siswa', 'km']))
                    <a class="group flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50/50 p-3.5 transition hover:border-amber-400 hover:bg-amber-50/30" href="{{ route('piket.upload.form') }}">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-white border border-slate-200 text-slate-700 shadow-sm transition group-hover:border-amber-400 group-hover:text-amber-600">
                                <x-icon name="heroicon-o-qr-code" class="h-4 w-4" />
                            </span>
                            <div>
                                <strong class="block text-xs font-bold text-slate-800 group-hover:text-amber-900">Ambil Bukti Piket</strong>
                                <small class="text-[0.7rem] text-slate-500">Foto dan kirim bukti</small>
                            </div>
                        </div>
                        <x-icon name="heroicon-o-arrow-right" class="h-3.5 w-3.5 text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600" />
                    </a>
                @endif

                @if(in_array($role, ['admin', 'km']))
                    <a class="group flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50/50 p-3.5 transition hover:border-amber-400 hover:bg-amber-50/30" href="{{ route('schedules.index') }}">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-white border border-slate-200 text-slate-700 shadow-sm transition group-hover:border-amber-400 group-hover:text-amber-600">
                                <x-icon name="heroicon-o-clock" class="h-4 w-4" />
                            </span>
                            <div>
                                <strong class="block text-xs font-bold text-slate-800 group-hover:text-amber-900">Atur Jadwal</strong>
                                <small class="text-[0.7rem] text-slate-500">Kelola giliran piket</small>
                            </div>
                        </div>
                        <x-icon name="heroicon-o-arrow-right" class="h-3.5 w-3.5 text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600" />
                    </a>
                @endif

                @if(in_array($role, ['admin', 'guru_piket', 'wali_kelas', 'km']))
                    <a class="group flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50/50 p-3.5 transition hover:border-amber-400 hover:bg-amber-50/30" href="{{ route('verification.index') }}">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-white border border-slate-200 text-slate-700 shadow-sm transition group-hover:border-amber-400 group-hover:text-amber-600">
                                <x-icon name="heroicon-o-shield-check" class="h-4 w-4" />
                            </span>
                            <div>
                                <strong class="block text-xs font-bold text-slate-800 group-hover:text-amber-900">Verifikasi Bukti</strong>
                                <small class="text-[0.7rem] text-slate-500">Tinjau kiriman terbaru</small>
                            </div>
                        </div>
                        <x-icon name="heroicon-o-arrow-right" class="h-3.5 w-3.5 text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600" />
                    </a>
                @endif

                @if(in_array($role, ['guru_piket', 'wali_kelas']))
                    <a class="group flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50/50 p-3.5 transition hover:border-amber-400 hover:bg-amber-50/30" href="{{ route('reports.index') }}">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-white border border-slate-200 text-slate-700 shadow-sm transition group-hover:border-amber-400 group-hover:text-amber-600">
                                <x-icon name="heroicon-o-clipboard-document-list" class="h-4 w-4" />
                            </span>
                            <div>
                                <strong class="block text-xs font-bold text-slate-800 group-hover:text-amber-900">Laporan</strong>
                                <small class="text-[0.7rem] text-slate-500">Rekap presensi piket</small>
                            </div>
                        </div>
                        <x-icon name="heroicon-o-arrow-right" class="h-3.5 w-3.5 text-slate-400 transition group-hover:translate-x-1 group-hover:text-amber-600" />
                    </a>
                @endif
            </div>
        </section>

        <!-- Side Card Info -->
        <aside class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div>
                <span class="grid h-9 w-9 place-items-center rounded-lg bg-amber-500 text-amber-950 shadow-sm">
                    <x-icon name="heroicon-o-chart-bar" class="h-4 w-4" />
                </span>
                <span class="mt-4 block text-[0.65rem] font-bold uppercase tracking-wider text-slate-400">Catatan Hari Ini</span>
                <h3 class="mt-1.5 text-base font-bold tracking-tight text-slate-900">Konsistensi kecil membangun budaya sekolah.</h3>
                <p class="mt-2 text-xs leading-relaxed text-slate-500">
                    Pastikan setiap bukti piket diambil secara langsung dari lokasi sekolah dan diunggah sesuai tenggat waktu yang ditentukan.
                </p>
            </div>

            <div class="mt-5 border-t border-slate-100 pt-3 text-[0.7rem] font-semibold text-slate-400">
                Piket App &bull; SMAN 1 Tasikmalaya
            </div>
        </aside>
    </div>
</div>
@endsection
