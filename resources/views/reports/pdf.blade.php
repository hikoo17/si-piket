@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
]])
@section('content')
<h1 class="mb-5 text-2xl font-bold text-amber-950">Laporan Piket</h1>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead><tr class="border-b border-amber-200 bg-amber-500 text-left text-xs uppercase tracking-wider text-white"><th class="p-3">Tanggal & Waktu</th><th class="p-3">Siswa</th><th class="p-3">Kelas</th><th class="p-3">Jenis</th><th class="p-3">Status</th><th class="p-3">Jarak</th><th class="p-3">Deskripsi</th></tr></thead>
    <tbody class="divide-y divide-amber-100">@foreach($logs as $log)<tr class="transition hover:bg-amber-50/60"><td class="p-3">{{ $log->date->locale('id')->translatedFormat('j F Y') }}<br>{{ ($log->photo_captured_at ?? $log->created_at)?->format('H:i') ?? '-' }} WIB</td><td class="p-3 font-medium text-amber-950">{{ $log->user->name }}</td><td class="p-3 text-xs text-amber-700">{{ $log->user->schoolClass?->name }}</td><td class="p-3">{{ $log->schedule?->shift_label }}</td><td class="p-3">{{ $log->status }}</td><td class="p-3">{{ $log->distance_meters }} m</td><td class="p-3">{{ $log->description ?: '-' }}</td></tr>@endforeach</tbody>
</table>
@endsection
