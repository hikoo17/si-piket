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
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900 mt-0.5">Jadwal Piket</h1>
            <p class="text-xs text-slate-500 mt-1">Filter jadwal yang ingin dilihat, lalu gunakan aksi edit pada tabel untuk memperbaruinya.</p>
        </div>
        <a href="{{ route('schedules.create') }}" class="btn btn-primary shrink-0">
            <x-icon name="heroicon-o-plus" class="h-4 w-4" />
            Tambah Jadwal
        </a>
    </div>

    <!-- Filter Jadwal -->
    <div class="rounded-xl border border-slate-200 bg-white p-4">
        <form method="GET" action="{{ route('schedules.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5 lg:items-end">
            <div>
                <label for="search" class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Cari Siswa</label>
                <input id="search" type="search" name="search" value="{{ request('search') }}" placeholder="Nama siswa..." class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs font-medium text-slate-800 focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label for="class_id" class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Kelas</label>
                <select id="class_id" name="class_id" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs font-medium text-slate-800 focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <option value="">Semua kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected((string) request('class_id') === (string) $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Hari</label>
                <select name="day_of_week" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs font-medium text-slate-800 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <option value="">Semua hari</option>
                    @foreach(['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'] as $key => $label)
                        <option value="{{ $key }}" @selected(request('day_of_week') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Jenis Piket</label>
                <select name="shift" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs font-medium text-slate-800 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <option value="">Semua jenis</option>
                    <option value="morning" @selected(request('shift') === 'morning')>Piket Pagi</option>
                    <option value="afternoon" @selected(request('shift') === 'afternoon')>Piket Pulang</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex h-9 flex-1 items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 text-xs font-bold text-white transition hover:bg-slate-800">
                    <x-icon name="heroicon-o-magnifying-glass" class="h-4 w-4" />
                    Filter
                </button>
                @if(request()->hasAny(['search', 'class_id', 'day_of_week', 'shift']))
                    <a href="{{ route('schedules.index') }}" class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-50" title="Reset filter">
                        <x-icon name="heroicon-o-x-mark" class="h-4 w-4" />
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80 text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">Siswa</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4">Hari</th>
                        <th class="py-3 px-4">Jenis Piket</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($schedules as $schedule)
                        <tr class="transition hover:bg-slate-50/60">
                            <td class="py-3 px-4 whitespace-nowrap font-semibold text-slate-900">
                                {{ $schedule->user->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap text-slate-500">
                                {{ $schedule->user->schoolClass?->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @php
                                    $dayMap = [
                                        'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                                        'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
                                    ];
                                @endphp
                                <span class="inline-flex items-center rounded-md bg-slate-100 border border-slate-200 px-2 py-0.5 text-[0.68rem] font-bold text-slate-700">
                                    {{ $dayMap[$schedule->day_of_week] ?? $schedule->day_of_week }}
                                </span>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wider {{ $schedule->shift === 'afternoon' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/80' : 'bg-amber-50 text-amber-700 border border-amber-200/80' }}">
                                    @if($schedule->shift === 'morning')
                                        <x-icon name="heroicon-o-sun" class="h-3.5 w-3.5 text-amber-500" />
                                    @else
                                        <x-icon name="heroicon-o-moon" class="h-3.5 w-3.5 text-indigo-500" />
                                    @endif
                                    {{ $schedule->shift_label }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-2">
                                    <a href="{{ route('schedules.edit', $schedule) }}" class="inline-flex h-7 items-center gap-1 rounded-md border border-slate-200 bg-white px-2 text-xs font-semibold text-slate-700 transition hover:border-amber-300 hover:bg-amber-50/50 hover:text-amber-700">
                                        <x-icon name="heroicon-o-pencil-square" class="h-3.5 w-3.5 text-slate-400" />
                                        <span>Edit</span>
                                    </a>

                                    <!-- Delete Form -->
                                    <form method="POST" action="{{ route('schedules.destroy', $schedule) }}" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?');">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-7 items-center gap-1 rounded-md border border-slate-200 bg-white px-2 text-xs font-semibold text-rose-600 transition hover:border-rose-200 hover:bg-rose-50" title="Hapus Jadwal">
                                            <x-icon name="heroicon-o-trash" class="h-3.5 w-3.5" />
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-slate-100 text-slate-400">
                                        <x-icon name="heroicon-o-clock" class="h-5 w-5" />
                                    </span>
                                    <h3 class="mt-2 text-xs font-bold text-slate-800">Belum ada jadwal piket</h3>
                                    <p class="text-[0.7rem] text-slate-500 mt-0.5">Ubah filter atau tambahkan jadwal piket baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($schedules->hasPages())
            <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-2.5">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
