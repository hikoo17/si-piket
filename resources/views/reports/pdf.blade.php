@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'dashboard'],
    ['schedules.index', 'Jadwal Piket', 'clock'],
    ['piket.upload.form', 'Ambil Bukti', 'scan'],
    ['verification.index', 'Verifikasi', 'shield'],
    ['reports.index', 'Laporan', 'report'],
]])
@section('content')
<h1 class="mb-5 text-2xl font-bold text-[#6d1a1a]">Laporan Piket</h1>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead><tr class="border-b border-[#fce4c4] bg-[#6d1a1a] text-left text-xs uppercase tracking-wider text-white"><th class="p-3">Tanggal</th><th class="p-3">Siswa</th><th class="p-3">Kelas</th><th class="p-3">Status</th><th class="p-3">Jarak</th></tr></thead>
    <tbody class="divide-y divide-[#fce4c4]">@foreach($logs as $log)<tr class="transition hover:bg-[#fffdf5]"><td class="p-3">{{ $log->date->format('Y-m-d') }}</td><td class="p-3 font-medium text-[#4a1c1c]">{{ $log->user->name }}</td><td class="p-3 text-xs text-[#8d6e63]">{{ $log->user->schoolClass?->name }}</td><td class="p-3">{{ $log->status }}</td><td class="p-3">{{ $log->distance_meters }} m</td></tr>@endforeach</tbody>
</table>
@endsection
