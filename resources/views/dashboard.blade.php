@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'dashboard'],
    ['schedules.index', 'Jadwal Piket', 'clock'],
    ['piket.upload.form', 'Ambil Bukti', 'scan'],
    ['verification.index', 'Verifikasi', 'shield'],
    ['reports.index', 'Laporan', 'report'],
    ['users.index', 'Pengguna', 'users'],
]])
@section('content')
<section class="relative overflow-hidden rounded-[2rem] bg-[#551414] p-7 text-white shadow-[0_24px_70px_#55141424] before:absolute before:-right-24 before:-top-36 before:h-96 before:w-96 before:rounded-full before:border-[55px] before:border-white/[.04] lg:p-10">
    <div class="relative z-[1] grid items-end gap-8 lg:grid-cols-[1fr_auto]">
        <div class="max-w-2xl">
            <span class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/[.07] px-3 py-1.5 text-[.64rem] font-bold uppercase tracking-[.16em] text-[#edc99d]"><i class="h-1.5 w-1.5 rounded-full bg-[#e7ae48]"></i> Ringkasan hari ini</span>
            <h1 class="text-[clamp(2rem,4vw,3.7rem)] font-semibold leading-[1.04] tracking-[-.055em]">Selamat datang, <span class="font-serif font-normal italic text-[#efbd64]">{{ explode(' ', auth()->user()->name)[0] }}</span>.</h1>
            <p class="mt-4 max-w-xl text-sm leading-7 text-[#e7cfc2]">Pantau aktivitas piket dan selesaikan hal penting hari ini dari satu ruang kerja yang ringkas.</p>
        </div>
        <div class="flex min-w-[210px] items-center gap-4 rounded-2xl border border-white/10 bg-black/10 p-4 backdrop-blur-sm">
            <div class="grid h-12 w-12 place-items-center rounded-2xl bg-[#e7ae48] text-[#551414]"><svg class="h-6 w-6"><use href="#icon-clock"></use></svg></div>
            <div><span class="block text-[.62rem] uppercase tracking-[.14em] text-[#d6aa90]">Waktu lokal</span><strong class="mt-1 block text-xl">{{ now()->format('H:i') }} WIB</strong></div>
        </div>
    </div>
</section>

@php
    $stats = [
        ['Jadwal hari ini', $scheduleCount, 'clock', 'Tugas piket terjadwal', '#f4dfbd'],
        ['Perlu ditinjau', $pendingCount, 'warning', 'Menunggu verifikasi', '#ead1c7'],
        ['Sudah disetujui', $approvedCount, 'success', 'Bukti piket valid', '#d9e5d5'],
        ['Siswa & KM', $studentCount, 'users', 'Pengguna terdaftar', '#d8e2e8'],
    ];
@endphp
<div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach($stats as [$label, $value, $icon, $description, $accent])
        <article class="group relative overflow-hidden rounded-2xl border border-[#e8ddd1] bg-white p-5 shadow-[0_8px_30px_#4d28100a] transition duration-300 hover:-translate-y-1 hover:shadow-[0_16px_40px_#4d281014]">
            <div class="mb-8 flex items-start justify-between"><span class="text-xs font-bold uppercase tracking-[.1em] text-[#8c7162]">{{ $label }}</span><span class="grid h-10 w-10 place-items-center rounded-xl text-[#551414]" style="background: {{ $accent }}"><svg><use href="#icon-{{ $icon }}"></use></svg></span></div>
            <div class="flex items-end justify-between gap-2"><strong class="text-4xl font-semibold tracking-[-.06em] text-[#421717]">{{ str_pad($value, 2, '0', STR_PAD_LEFT) }}</strong><span class="pb-1 text-right text-[.65rem] text-[#9b8173]">{{ $description }}</span></div>
        </article>
    @endforeach
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-[1.35fr_.65fr]">
    <section class="rounded-[1.6rem] border border-[#e8ddd1] bg-white p-6 lg:p-7">
        <div class="mb-6 flex items-end justify-between gap-4"><div><p class="text-[.62rem] font-bold uppercase tracking-[.16em] text-[#a17a64]">Akses cepat</p><h2 class="mt-1 text-xl font-semibold tracking-[-.035em]">Apa yang ingin dikerjakan?</h2></div><span class="text-[.68rem] text-[#9b8173]">Sesuai hak akses Anda</span></div>
        <div class="grid gap-3 sm:grid-cols-2">
            @if(auth()->user()->role==='admin')
                <a class="dashboard-action" href="{{ route('schools.index') }}"><span class="dashboard-action-icon"><svg><use href="#icon-school"></use></svg></span><span><strong>Kelola sekolah</strong><small>Profil dan lokasi sekolah</small></span><b>→</b></a>
                <a class="dashboard-action" href="{{ route('classes.index') }}"><span class="dashboard-action-icon"><svg><use href="#icon-grid"></use></svg></span><span><strong>Kelola kelas</strong><small>Susunan kelas aktif</small></span><b>→</b></a>
                <a class="dashboard-action" href="{{ route('users.index') }}"><span class="dashboard-action-icon"><svg><use href="#icon-users"></use></svg></span><span><strong>Kelola pengguna</strong><small>Akun dan hak akses</small></span><b>→</b></a>
            @endif
            @if(in_array(auth()->user()->role,['siswa','km']))<a class="dashboard-action" href="{{ route('piket.upload.form') }}"><span class="dashboard-action-icon"><svg><use href="#icon-scan"></use></svg></span><span><strong>Ambil bukti piket</strong><small>Foto dan kirim bukti</small></span><b>→</b></a>@endif
            @if(in_array(auth()->user()->role,['admin','km']))<a class="dashboard-action" href="{{ route('schedules.index') }}"><span class="dashboard-action-icon"><svg><use href="#icon-clock"></use></svg></span><span><strong>Atur jadwal</strong><small>Kelola giliran piket</small></span><b>→</b></a>@endif
            @if(in_array(auth()->user()->role,['admin','guru','km']))<a class="dashboard-action" href="{{ route('verification.index') }}"><span class="dashboard-action-icon"><svg><use href="#icon-shield"></use></svg></span><span><strong>Verifikasi bukti</strong><small>Tinjau kiriman terbaru</small></span><b>→</b></a>@endif
        </div>
    </section>
    <aside class="relative overflow-hidden rounded-[1.6rem] bg-[#e7ae48] p-7 text-[#421717] before:absolute before:-bottom-16 before:-right-12 before:h-44 before:w-44 before:rounded-full before:border-[28px] before:border-[#551414]/[.06]">
        <span class="relative grid h-11 w-11 place-items-center rounded-2xl bg-[#551414] text-white"><svg><use href="#icon-chart"></use></svg></span>
        <p class="relative mt-10 text-[.62rem] font-extrabold uppercase tracking-[.16em] text-[#704315]">Catatan hari ini</p>
        <h2 class="relative mt-2 text-2xl font-semibold leading-tight tracking-[-.04em]">Konsistensi kecil membangun budaya sekolah.</h2>
        <p class="relative mt-4 text-xs leading-6 text-[#704315]">Pastikan setiap bukti dikirim dari lokasi sekolah dan sesuai jadwal yang ditentukan.</p>
    </aside>
</div>
@endsection
