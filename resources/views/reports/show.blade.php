@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
]])
@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div><a href="{{ route('reports.index') }}" class="text-sm font-semibold text-amber-800">&larr; Kembali ke laporan</a><h1 class="mt-2 text-2xl font-bold text-slate-900">Detail Laporan Piket</h1></div>
    <span class="rounded-full px-3 py-1 text-xs font-bold uppercase {{ $log->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($log->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-900') }}">{{ $log->status }}</span>
</div>
<div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
    <section class="rounded-xl border border-[#fce4c4] bg-white p-5 shadow-sm">
        <h2 class="mb-3 text-lg font-bold text-[#6d1a1a]">Gambar Bukti</h2>
        @if($log->photo_path)<a href="{{ Storage::url($log->photo_path) }}" target="_blank"><img src="{{ Storage::url($log->photo_path) }}" class="max-h-[520px] w-full rounded-lg object-contain" alt="Bukti {{ $log->user->name }}"></a>@else<p class="text-sm text-amber-700">Tidak ada gambar bukti.</p>@endif
    </section>
    <section class="rounded-xl border border-[#fce4c4] bg-white p-5 shadow-sm"><h2 class="mb-3 text-lg font-bold text-[#6d1a1a]">Informasi</h2><dl class="grid gap-3 text-sm sm:grid-cols-2">
        <div><dt class="font-semibold text-amber-900">Siswa</dt><dd>{{ $log->user->name }}</dd></div><div><dt class="font-semibold text-amber-900">Kelas</dt><dd>{{ $log->user->schoolClass?->name ?? '-' }}</dd></div><div><dt class="font-semibold text-amber-900">Tanggal</dt><dd>{{ $log->date->locale('id')->translatedFormat('j F Y') }}</dd></div><div><dt class="font-semibold text-amber-900">Jadwal</dt><dd>{{ $log->schedule?->day_of_week ?? '-' }} · {{ $log->schedule?->shift_label ?? '-' }}</dd></div><div><dt class="font-semibold text-amber-900">Jarak</dt><dd>{{ $log->distance_meters ?? '-' }} m</dd></div><div><dt class="font-semibold text-amber-900">Akurasi</dt><dd>{{ $log->accuracy_meters ?? '-' }} m</dd></div><div><dt class="font-semibold text-amber-900">Koordinat</dt><dd>{{ $log->latitude ?? '-' }}, {{ $log->longitude ?? '-' }}</dd></div><div><dt class="font-semibold text-amber-900">Waktu foto</dt><dd>{{ ($log->photo_captured_at ?? $log->created_at)?->locale('id')->translatedFormat('j F Y, H:i') ?? '-' }} WIB</dd></div><div><dt class="font-semibold text-amber-900">Diverifikasi oleh</dt><dd>{{ $log->verifier?->name ?? '-' }}</dd></div><div><dt class="font-semibold text-amber-900">Waktu verifikasi</dt><dd>{{ $log->verified_at?->locale('id')->translatedFormat('j F Y, H:i') ?? '-' }} WIB</dd></div><div class="sm:col-span-2"><dt class="font-semibold text-amber-900">Deskripsi</dt><dd class="whitespace-pre-line">{{ $log->description ?: '-' }}</dd></div><div class="sm:col-span-2"><dt class="font-semibold text-amber-900">Alasan penolakan</dt><dd>{{ $log->rejection_reason ?? '-' }}</dd></div>
    </dl></section>
</div>
@endsection
