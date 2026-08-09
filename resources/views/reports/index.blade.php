@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
]])

@section('content')
<div class="space-y-5">
    <!-- Header Section -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Laporan Aktivitas Piket</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau, filter, dan unduh rekapan riwayat piket siswa.</p>
        </div>
        
        <!-- Export Actions -->
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.csv', request()->query()) }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900">
                <x-icon name="heroicon-o-document-text" class="h-4 w-4 text-emerald-600" />
                <span>Export CSV</span>
            </a>
            <button type="button" onclick="exportPdf(this)" data-href="{{ route('reports.pdf', request()->query()) }}" class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900">
                <x-icon name="heroicon-o-arrow-down-tray" class="h-4 w-4 text-rose-600" />
                <span>Export PDF</span>
            </button>
        </div>
    </div>

    <!-- Filter Panel -->
    <div class="rounded-xl border border-slate-200 bg-white p-4">
        <form class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5 lg:items-end">
            <!-- Filter Bulan -->
            <div>
                <label class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Bulan</label>
                <input type="month" name="month" value="{{ request('month') }}" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs font-medium text-slate-800 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>

            <!-- Filter Kelas -->
            <div>
                <label class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Kelas</label>
                <select name="class_id" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs font-medium text-slate-800 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Jenis Piket -->
            <div>
                <label class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Jenis Piket</label>
                <select name="shift" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs font-medium text-slate-800 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <option value="">Semua Jenis</option>
                    <option value="morning" @selected(request('shift') === 'morning')>Piket Pagi</option>
                    <option value="afternoon" @selected(request('shift') === 'afternoon')>Piket Pulang</option>
                </select>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Status</label>
                <select name="status" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs font-medium text-slate-800 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <option value="">Semua Status</option>
                    @foreach(['approved' => 'Disetujui', 'pending' => 'Menunggu', 'rejected' => 'Ditolak', 'absent' => 'Absen'] as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @selected(request('status') == $statusKey)>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action Filter Button -->
            <div class="flex items-center gap-2">
                <button type="submit" class="h-9 flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-amber-500 px-3 text-xs font-bold text-white transition hover:bg-amber-600 focus:outline-none">
                    <x-icon name="heroicon-o-funnel" class="h-4 w-4" />
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['month', 'class_id', 'shift', 'status']))
                    <a href="{{ route('reports.index') }}" class="h-9 w-9 inline-flex shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-slate-100 text-slate-600 transition hover:bg-slate-200" title="Reset Filter">
                        <x-icon name="heroicon-o-x-mark" class="h-4 w-4" />
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Card list (mobile) -->
    @php
        $statusClasses = [
            'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
            'pending'  => 'bg-amber-50 text-amber-700 border-amber-200/80',
            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200/80',
            'absent'   => 'bg-slate-100 text-slate-600 border-slate-200',
        ];
        $statusIcons = [
            'approved' => 'heroicon-o-check-circle',
            'pending'  => 'heroicon-o-clock',
            'rejected' => 'heroicon-o-x-circle',
            'absent'   => 'heroicon-o-minus-circle',
        ];
    @endphp
    <div class="space-y-3 sm:hidden">
        @forelse($logs as $log)
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="truncate text-sm font-bold text-slate-900">{{ $log->user->name ?? '-' }}</div>
                        <div class="mt-0.5 text-[0.7rem] text-slate-500">
                            {{ $log->date ? $log->date->locale('id')->translatedFormat('j F Y') : '-' }} · {{ ($log->photo_captured_at ?? $log->created_at)?->format('H:i') ?? '-' }} WIB
                        </div>
                        <div class="mt-0.5 text-[0.7rem] text-slate-500">{{ $log->user->schoolClass?->name ?? '-' }}</div>
                    </div>
                    <span class="inline-flex shrink-0 items-center gap-1 rounded-full border px-2.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wider {{ $statusClasses[$log->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                        <x-icon name="{{ $statusIcons[$log->status] ?? 'heroicon-o-question-mark-circle' }}" class="h-3.5 w-3.5" />
                        {{ $log->status }}
                    </span>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-600 border-t border-slate-100 pt-3">
                    <span class="inline-flex items-center gap-1 font-semibold text-slate-800">
                        @if(($log->schedule?->shift ?? '') === 'morning')
                            <x-icon name="heroicon-o-sun" class="h-3.5 w-3.5 text-amber-500" />
                        @else
                            <x-icon name="heroicon-o-moon" class="h-3.5 w-3.5 text-indigo-500" />
                        @endif
                        {{ $log->schedule?->shift_label ?? '-' }}
                    </span>
                    <span>Jarak: <strong class="{{ $log->distance_meters > 50 ? 'text-amber-600' : 'text-slate-800' }}">{{ $log->distance_meters ?? 0 }} m</strong></span>
                    @if($log->accuracy_meters)
                        <span>Akurasi: <strong class="text-slate-800">{{ $log->accuracy_meters }} m</strong></span>
                    @endif
                </div>
                <a href="{{ route('reports.show', $log) }}" class="mt-3 inline-flex h-8 w-full items-center justify-center gap-1 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 transition hover:border-amber-300 hover:bg-amber-50/50 hover:text-amber-700">
                    <x-icon name="heroicon-o-eye" class="h-3.5 w-3.5 text-slate-400" />
                    <span>Detail</span>
                </a>
            </div>
        @empty
            <div class="rounded-xl border border-slate-200 bg-white p-10 text-center">
                <div class="flex flex-col items-center justify-center">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-slate-100 text-slate-400">
                        <x-icon name="heroicon-o-clipboard-document-list" class="h-5 w-5" />
                    </span>
                    <h3 class="mt-2 text-xs font-bold text-slate-800">Tidak ada data laporan</h3>
                    <p class="text-[0.7rem] text-slate-500 mt-0.5">Coba sesuaikan kriteria filter Anda.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Table (desktop) -->
    <div class="hidden overflow-hidden rounded-xl border border-slate-200 bg-white sm:block">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80 text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">Tanggal & Waktu</th>
                        <th class="py-3 px-4">Siswa</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Jenis Piket</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Jarak GPS</th>
                        <th class="py-3 px-4">Akurasi</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($logs as $log)
                        <tr class="transition hover:bg-slate-50/60">
                            <td class="py-3 px-4 whitespace-nowrap text-slate-900 font-semibold">
                                <span class="block">{{ $log->date ? $log->date->locale('id')->translatedFormat('j F Y') : '-' }}</span>
                                <span class="mt-0.5 block text-[0.68rem] font-medium text-slate-500">{{ ($log->photo_captured_at ?? $log->created_at)?->format('H:i') ?? '-' }} WIB</span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-900">{{ $log->user->name ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap text-slate-500">
                                {{ $log->user->schoolClass?->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 font-semibold text-slate-800">
                                    @if(($log->schedule?->shift ?? '') === 'morning')
                                        <x-icon name="heroicon-o-sun" class="h-3.5 w-3.5 text-amber-500" />
                                    @else
                                        <x-icon name="heroicon-o-moon" class="h-3.5 w-3.5 text-indigo-500" />
                                    @endif
                                    {{ $log->schedule?->shift_label ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                        'pending'  => 'bg-amber-50 text-amber-700 border-amber-200/80',
                                        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                                        'absent'   => 'bg-slate-100 text-slate-600 border-slate-200',
                                    ];
                                    $statusIcons = [
                                        'approved' => 'heroicon-o-check-circle',
                                        'pending'  => 'heroicon-o-clock',
                                        'rejected' => 'heroicon-o-x-circle',
                                        'absent'   => 'heroicon-o-minus-circle',
                                    ];
                                @endphp
                                <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wider {{ $statusClasses[$log->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                    <x-icon name="{{ $statusIcons[$log->status] ?? 'heroicon-o-question-mark-circle' }}" class="h-3.5 w-3.5" />
                                    {{ $log->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap text-slate-600">
                                {{ $log->distance_meters !== null ? $log->distance_meters . ' m' : '-' }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap text-slate-600">
                                {{ $log->accuracy_meters !== null ? $log->accuracy_meters . ' m' : '-' }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <a href="{{ route('reports.show', $log) }}" class="inline-flex h-7 items-center gap-1 rounded-md border border-slate-200 bg-white px-2.5 text-xs font-semibold text-slate-700 transition hover:border-amber-300 hover:bg-amber-50/50 hover:text-amber-700">
                                    <x-icon name="heroicon-o-eye" class="h-3.5 w-3.5 text-slate-400" />
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-slate-100 text-slate-400">
                                        <x-icon name="heroicon-o-clipboard-document-list" class="h-5 w-5" />
                                    </span>
                                    <h3 class="mt-2 text-xs font-bold text-slate-800">Tidak ada data laporan</h3>
                                    <p class="text-[0.7rem] text-slate-500 mt-0.5">Coba sesuaikan kriteria filter Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($logs->hasPages())
            <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-2.5">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    function exportPdf(button) {
        const originalContent = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<svg class="animate-spin h-4 w-4 text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Mengunduh...</span>';
        button.classList.add('opacity-75', 'cursor-wait');

        setTimeout(() => {
            window.location.href = button.dataset.href;
            button.innerHTML = originalContent;
            button.disabled = false;
            button.classList.remove('opacity-75', 'cursor-wait');
        }, 300);
    }
</script>
@endsection
