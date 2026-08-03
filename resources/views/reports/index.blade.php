@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicons-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicons-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicons-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicons-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicons-o-clipboard-document-list'],
]])
@section('content')
<h1 class="mb-5 text-2xl font-bold text-amber-950">Laporan Piket</h1>
<form class="mb-5 flex flex-wrap items-end gap-2 rounded-xl border border-amber-200 bg-white p-4 shadow-sm">
    <div class="flex-1 min-w-[160px]">
        <label class="mb-1 block text-xs font-semibold text-amber-900">Bulan</label>
        <input type="month" name="month" value="{{ request('month') }}" class="w-full rounded-lg border border-amber-200 bg-white p-2.5 text-sm outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
    </div>
    <div class="flex-1 min-w-[160px]">
        <label class="mb-1 block text-xs font-semibold text-amber-900">Kelas</label>
        <select name="class_id" class="w-full rounded-lg border border-amber-200 bg-white p-2.5 text-sm outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-100"><option value="">Semua kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(request('class_id')==$class->id)>{{ $class->name }}</option>@endforeach</select>
    </div>
    <div class="flex-1 min-w-[140px]">
        <label class="mb-1 block text-xs font-semibold text-amber-900">Status</label>
        <select name="status" class="w-full rounded-lg border border-amber-200 bg-white p-2.5 text-sm outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-100"><option value="">Semua status</option>@foreach(['approved'=>'Disetujui','pending'=>'Menunggu','rejected'=>'Ditolak','absent'=>'Absen'] as $statusKey=>$statusLabel)<option value="{{ $statusKey }}" @selected(request('status')==$statusKey)>{{ $statusLabel }}</option>@endforeach</select>
    </div>
    <div class="flex gap-2">
        <button class="rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-amber-950 shadow-sm transition hover:-translate-y-px hover:bg-amber-400">Filter</button>
        <a href="{{ route('reports.csv',request()->query()) }}" class="rounded-lg bg-amber-100 px-4 py-2.5 text-sm font-semibold text-amber-900 shadow-sm transition hover:-translate-y-px hover:bg-amber-200">CSV</a>
        <a href="{{ route('reports.pdf',request()->query()) }}" class="rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-px hover:bg-amber-500">PDF</a>
    </div>
</form>
<div class="overflow-auto rounded-xl border border-amber-200 bg-white shadow-sm">
    <table class="w-full">
        <thead><tr class="border-b border-amber-200 bg-amber-500 text-left text-xs uppercase tracking-wider text-white"><th class="p-3">Tanggal</th><th>Siswa</th><th>Kelas</th><th>Status</th><th>Jarak</th><th>Akurasi</th></tr></thead>
        <tbody class="divide-y divide-amber-100">@foreach($logs as $log)<tr class="transition hover:bg-amber-50/60">
            <td class="p-3 text-sm text-amber-950">{{ $log->date->format('d/m/Y') }}</td>
            <td class="p-3 text-sm font-medium text-amber-950">{{ $log->user->name }}</td>
            <td class="p-3 text-xs text-amber-700">{{ $log->user->schoolClass?->name }}</td>
            <td class="p-3"><span class="inline-block rounded-full px-2 py-0.5 text-[.65rem] font-bold uppercase {{ $log->status==='approved' ? 'bg-emerald-100 text-emerald-700' : ($log->status==='pending' ? 'bg-amber-100 text-amber-900' : ($log->status==='rejected' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">{{ $log->status }}</span></td>
            <td class="p-3 text-sm text-amber-950">{{ $log->distance_meters }} m</td>
            <td class="p-3 text-sm text-amber-950">{{ $log->accuracy_meters }} m</td>
        </tr>@endforeach</tbody>
    </table>
</div>
{{ $logs->links() }}
@endsection
