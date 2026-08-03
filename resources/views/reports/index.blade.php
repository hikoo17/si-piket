@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'dashboard'],
    ['schedules.index', 'Jadwal Piket', 'clock'],
    ['piket.upload.form', 'Ambil Bukti', 'scan'],
    ['verification.index', 'Verifikasi', 'shield'],
    ['reports.index', 'Laporan', 'report'],
]])
@section('content')
<h1 class="mb-5 text-2xl font-bold text-[#6d1a1a]">Laporan Piket</h1>
<form class="mb-5 flex flex-wrap items-end gap-2 rounded-xl border border-[#fce4c4] bg-white p-4 shadow-sm">
    <div class="flex-1 min-w-[160px]">
        <label class="mb-1 block text-xs font-semibold text-[#5d4037]">Bulan</label>
        <input type="month" name="month" value="{{ request('month') }}" class="w-full rounded-lg border border-[#fce4c4] bg-white p-2.5 text-sm outline-none transition focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10">
    </div>
    <div class="flex-1 min-w-[160px]">
        <label class="mb-1 block text-xs font-semibold text-[#5d4037]">Kelas</label>
        <select name="class_id" class="w-full rounded-lg border border-[#fce4c4] bg-white p-2.5 text-sm outline-none transition focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10"><option value="">Semua kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(request('class_id')==$class->id)>{{ $class->name }}</option>@endforeach</select>
    </div>
    <div class="flex-1 min-w-[140px]">
        <label class="mb-1 block text-xs font-semibold text-[#5d4037]">Status</label>
        <select name="status" class="w-full rounded-lg border border-[#fce4c4] bg-white p-2.5 text-sm outline-none transition focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10"><option value="">Semua status</option>@foreach(['approved'=>'Disetujui','pending'=>'Menunggu','rejected'=>'Ditolak','absent'=>'Absen'] as $statusKey=>$statusLabel)<option value="{{ $statusKey }}" @selected(request('status')==$statusKey)>{{ $statusLabel }}</option>@endforeach</select>
    </div>
    <div class="flex gap-2">
        <button class="rounded-lg bg-[#6d1a1a] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-px hover:bg-[#5a1515]">Filter</button>
        <a href="{{ route('reports.csv',request()->query()) }}" class="rounded-lg bg-[#fbc02d] px-4 py-2.5 text-sm font-semibold text-[#4a1c1c] shadow-sm transition hover:-translate-y-px hover:bg-[#f9a825]">CSV</a>
        <a href="{{ route('reports.pdf',request()->query()) }}" class="rounded-lg bg-[#c62828] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-px hover:bg-[#b71c1c]">PDF</a>
    </div>
</form>
<div class="overflow-auto rounded-xl border border-[#fce4c4] bg-white shadow-sm">
    <table class="w-full">
        <thead><tr class="border-b border-[#fce4c4] bg-[#6d1a1a] text-left text-xs uppercase tracking-wider text-white"><th class="p-3">Tanggal</th><th>Siswa</th><th>Kelas</th><th>Status</th><th>Jarak</th><th>Akurasi</th></tr></thead>
        <tbody class="divide-y divide-[#fce4c4]">@foreach($logs as $log)<tr class="transition hover:bg-[#fffdf5]">
            <td class="p-3 text-sm text-[#4a1c1c]">{{ $log->date->format('d/m/Y') }}</td>
            <td class="p-3 text-sm font-medium text-[#4a1c1c]">{{ $log->user->name }}</td>
            <td class="p-3 text-xs text-[#8d6e63]">{{ $log->user->schoolClass?->name }}</td>
            <td class="p-3"><span class="inline-block rounded-full px-2 py-0.5 text-[.65rem] font-bold uppercase {{ $log->status==='approved' ? 'bg-emerald-100 text-emerald-800' : ($log->status==='pending' ? 'bg-amber-100 text-amber-800' : ($log->status==='rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">{{ $log->status }}</span></td>
            <td class="p-3 text-sm text-[#4a1c1c]">{{ $log->distance_meters }} m</td>
            <td class="p-3 text-sm text-[#4a1c1c]">{{ $log->accuracy_meters }} m</td>
        </tr>@endforeach</tbody>
    </table>
</div>
{{ $logs->links() }}
@endsection
