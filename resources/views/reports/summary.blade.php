@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-[0.68rem] font-bold uppercase tracking-[.16em] text-amber-600">Rekap presensi</p>
            <h1 class="mt-1 text-xl font-bold tracking-tight text-slate-900">Siswa yang Tidak Melaksanakan Piket</h1>
            <p class="mt-0.5 text-xs text-slate-500">{{ $from->locale('id')->translatedFormat('j F Y') }} sampai {{ $to->locale('id')->translatedFormat('j F Y') }}. Data dihitung dari seluruh jadwal yang seharusnya dijalankan.</p>
        </div>
        <a href="{{ route('reports.index') }}" class="inline-flex h-9 items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-3.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
            <x-icon name="heroicon-o-arrow-left" class="h-4 w-4" /> Laporan Aktivitas
        </a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-4">
        <form class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5 lg:items-end">
            <div>
                <label class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Periode cepat</label>
                <select name="period" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs font-medium text-slate-800">
                    <option value="previous_mondays" @selected(request('period', 'previous_mondays') === 'previous_mondays')>4 Senin sebelumnya</option>
                    <option value="preceding_week" @selected(request('period') === 'preceding_week')>Minggu sebelumnya</option>
                </select>
            </div>
            <div><label class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Dari</label><input type="date" name="from" value="{{ request('from') }}" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs"></div>
            <div><label class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Sampai</label><input type="date" name="to" value="{{ request('to') }}" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs"></div>
            <div><label class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Kelas</label><select name="class_id" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs"><option value="">Semua Kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>@endforeach</select></div>
            <div class="flex gap-2"><select name="shift" class="h-9 min-w-0 flex-1 rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs"><option value="">Semua Shift</option><option value="morning" @selected(request('shift') === 'morning')>Piket Pagi</option><option value="afternoon" @selected(request('shift') === 'afternoon')>Piket Pulang</option></select><button class="h-9 rounded-lg bg-amber-500 px-4 text-xs font-bold text-white hover:bg-amber-600">Tampilkan</button></div>
        </form>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50/70 px-4 py-3 text-xs text-slate-500"><strong class="text-slate-900">{{ $summary->sum('missed') }}</strong> kewajiban piket tidak terpenuhi dari <strong class="text-slate-900">{{ $summary->sum('expected') }}</strong> jadwal.</div>
        <div class="overflow-x-auto"><table class="w-full text-left"><thead><tr class="border-b border-slate-100 text-[0.68rem] font-bold uppercase tracking-wider text-slate-500"><th class="px-4 py-3">Siswa</th><th class="px-4 py-3">Kelas</th><th class="px-4 py-3 text-center">Jadwal</th><th class="px-4 py-3 text-center">Terlaksana</th><th class="px-4 py-3 text-center">Tidak hadir</th><th class="px-4 py-3">Tanggal terlewat</th></tr></thead>
        <tbody class="divide-y divide-slate-100 text-xs">@forelse($summary as $row)<tr class="hover:bg-slate-50/60"><td class="px-4 py-3 font-semibold text-slate-900">{{ $row['user']->name }}</td><td class="px-4 py-3 text-slate-500">{{ $row['user']->schoolClass?->name ?? '-' }}</td><td class="px-4 py-3 text-center text-slate-600">{{ $row['expected'] }}</td><td class="px-4 py-3 text-center text-emerald-700">{{ $row['attended'] }}</td><td class="px-4 py-3 text-center"><span class="rounded-full bg-rose-50 px-2.5 py-1 font-bold text-rose-700">{{ $row['missed'] }}</span></td><td class="px-4 py-3 text-slate-600">{{ $row['dates']->map(fn ($item) => $item['date']->locale('id')->translatedFormat('D, j M').' ('.$item['shift'].')')->implode(', ') }}</td></tr>@empty<tr><td colspan="6" class="px-4 py-12 text-center text-xs text-slate-500">Tidak ada siswa yang tercatat tidak hadir pada periode ini.</td></tr>@endforelse</tbody></table></div>
    </div>
</div>
@endsection
