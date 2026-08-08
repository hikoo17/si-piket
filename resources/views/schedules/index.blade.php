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
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Jadwal Piket</h1>
            <p class="mt-1 text-xs font-medium text-slate-500">Filter jadwal yang ingin dilihat, lalu gunakan aksi pada tabel untuk memperbaruinya.</p>
        </div>
        <a href="{{ route('schedules.create') }}" class="inline-flex h-9 items-center justify-center gap-2 rounded-lg bg-amber-500 px-4 text-xs font-bold text-amber-950 shadow-sm transition hover:bg-amber-400 shrink-0">
            <x-icon name="heroicon-o-plus" class="h-4 w-4" />
            <span>Tambah Jadwal</span>
        </a>
    </div>

    <!-- Filter Form (Shadow Soft & Elemen Konsisten h-9) -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('schedules.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5 lg:items-end">
            <div>
                <label for="search" class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Cari Siswa</label>
                <input id="search" type="search" name="search" value="{{ request('search') }}" placeholder="Nama siswa..." class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs font-medium text-slate-800 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label for="class_id" class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Kelas</label>
                <select id="class_id" name="class_id" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs font-medium text-slate-800 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
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
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'class_id', 'day_of_week', 'shift']))
                    <a href="{{ route('schedules.index') }}" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-700" title="Reset filter">
                        <x-icon name="heroicon-o-x-mark" class="h-4 w-4" />
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Section (Minimalis & Shadow Tipis) -->
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">Siswa</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Hari</th>
                        <th class="px-4 py-3">Jenis Piket</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @php
                        $dayMap = [
                            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
                        ];
                    @endphp
                    @forelse($schedules as $group)
                        @php
                            $first = $group->first();
                            $user = $first->user;
                            $morning = $group->firstWhere('shift', 'morning');
                            $afternoon = $group->firstWhere('shift', 'afternoon');
                            $editTarget = $morning ?? $afternoon;
                        @endphp
                        <tr class="transition hover:bg-slate-50/50">
                            <td class="whitespace-nowrap px-4 py-3 font-semibold text-slate-900">
                                {{ $user->name ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500">
                                {{ $user->schoolClass?->name ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-100 px-2 py-0.5 text-[0.68rem] font-bold text-slate-700">
                                    {{ $dayMap[$first->day_of_week] ?? $first->day_of_week }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <div class="flex flex-wrap gap-1.5">
                                    @if($morning)
                                        <span class="inline-flex items-center gap-1 rounded-full border border-amber-200/80 bg-amber-50 px-2.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wider text-amber-700">
                                            <x-icon name="heroicon-o-sun" class="h-3.5 w-3.5 text-amber-500" />
                                            Pagi
                                        </span>
                                    @endif
                                    @if($afternoon)
                                        <span class="inline-flex items-center gap-1 rounded-full border border-indigo-200/80 bg-indigo-50 px-2.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wider text-indigo-700">
                                            <x-icon name="heroicon-o-moon" class="h-3.5 w-3.5 text-indigo-500" />
                                            Pulang
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-center">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <a href="{{ route('schedules.edit', $editTarget) }}" class="inline-flex h-7 items-center gap-1 rounded-md border border-slate-200 bg-white px-2.5 text-xs font-semibold text-slate-700 transition hover:border-amber-300 hover:bg-amber-50/50 hover:text-amber-700">
                                        <x-icon name="heroicon-o-pencil-square" class="h-3.5 w-3.5 text-slate-400" />
                                        <span>Edit</span>
                                    </a>

                                    <!-- Delete Form -->
                                    <form method="POST" action="{{ route('schedules.destroy', $editTarget) }}">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-7 items-center gap-1 rounded-md border border-slate-200 bg-white px-2.5 text-xs font-semibold text-rose-600 transition hover:border-rose-200 hover:bg-rose-50" title="Hapus Jadwal">
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
                                    <p class="mt-0.5 text-[0.7rem] text-slate-500">Ubah filter atau tambahkan jadwal piket baru.</p>
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