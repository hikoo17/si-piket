@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
]])
@section('content')
<h1 class="mb-5 text-2xl font-bold text-slate-900">Laporan Piket</h1>
<form class="filter-panel mb-5 flex flex-wrap items-end gap-2 p-4">
    <div class="flex-1 min-w-[160px]">
        <label class="mb-1 block text-xs font-semibold text-amber-900">Bulan</label>
         <input type="month" name="month" value="{{ request('month') }}" class="w-full">
    </div>
    <div class="flex-1 min-w-[160px]">
        <label class="mb-1 block text-xs font-semibold text-amber-900">Kelas</label>
         <select name="class_id" class="w-full"><option value="">Semua kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(request('class_id')==$class->id)>{{ $class->name }}</option>@endforeach</select>
    </div>
    <div class="flex-1 min-w-[140px]">
        <label class="mb-1 block text-xs font-semibold text-amber-900">Jenis Piket</label>
        <select name="shift" class="w-full"><option value="">Semua jenis</option><option value="morning" @selected(request('shift')==='morning')>Piket Pagi</option><option value="afternoon" @selected(request('shift')==='afternoon')>Piket Pulang</option></select>
    </div>
    <div class="flex-1 min-w-[140px]">
        <label class="mb-1 block text-xs font-semibold text-amber-900">Status</label>
         <select name="status" class="w-full"><option value="">Semua status</option>@foreach(['approved'=>'Disetujui','pending'=>'Menunggu','rejected'=>'Ditolak','absent'=>'Absen'] as $statusKey=>$statusLabel)<option value="{{ $statusKey }}" @selected(request('status')==$statusKey)>{{ $statusLabel }}</option>@endforeach</select>
    </div>
    <div class="flex gap-2">
        <button class="btn btn-primary">Filter</button>
        <a href="{{ route('reports.csv',request()->query()) }}" class="btn btn-secondary">CSV</a>
        <a href="{{ route('reports.pdf',request()->query()) }}" class="btn btn-secondary">PDF</a>
    </div>
</form>
<div class="table-shell overflow-auto">
    <table class="data-table">
        <thead><tr><th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Jenis Piket</th><th>Status</th><th>Jarak</th><th>Akurasi</th><th>Detail</th></tr></thead>
        <tbody class="divide-y divide-amber-100">@foreach($logs as $log)<tr class="transition hover:bg-amber-50/60">
            <td class="p-3 text-sm text-amber-950">{{ $log->date->format('d/m/Y') }}</td>
            <td class="p-3 text-sm font-medium text-amber-950">{{ $log->user->name }}</td>
            <td class="p-3 text-xs text-amber-700">{{ $log->user->schoolClass?->name }}</td>
            <td class="p-3 text-xs font-semibold text-amber-900">{{ $log->schedule?->shift_label }}</td>
            <td class="p-3"><span class="inline-block rounded-full px-2 py-0.5 text-[.65rem] font-bold uppercase {{ $log->status==='approved' ? 'bg-emerald-100 text-emerald-700' : ($log->status==='pending' ? 'bg-amber-100 text-amber-900' : ($log->status==='rejected' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">{{ $log->status }}</span></td>
            <td class="p-3 text-sm text-amber-950">{{ $log->distance_meters }} m</td>
             <td class="p-3 text-sm text-amber-950">{{ $log->accuracy_meters }} m</td>
             <td class="p-3"><a href="{{ route('reports.show', $log) }}" class="btn btn-secondary text-xs">Lihat detail</a></td>
         </tr>@endforeach</tbody>
    </table>
</div>
<div class="app-pagination">{{ $logs->links() }}</div>
@endsection
