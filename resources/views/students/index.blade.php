@extends('layouts.app', ['title' => 'Siswa'])
@section('content')
<div class="space-y-5">
    <!-- Header Section -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Siswa & KM</h1>
            <p class="mt-0.5 text-xs font-medium text-slate-500">Masukkan siswa ke kelas sebelum menyusun jadwal piket.</p>
        </div>
        <a href="{{ route('students.create', ['class_id' => request('class_id')]) }}"
           class="inline-flex h-9 shrink-0 items-center justify-center gap-1.5 rounded-lg bg-indigo-600 px-3.5 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none">
            <x-icon name="heroicon-o-user-plus" class="h-4 w-4" />
            <span>Tambah Siswa</span>
        </a>
    </div>

    <!-- Filter Panel -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('students.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_240px_auto] lg:items-end">
            <div>
                <label for="search" class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Cari</label>
                <input id="search" name="search" value="{{ request('search') }}" placeholder="Nama atau email..." class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs font-medium text-slate-800 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label for="class_id" class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Kelas</label>
                <select id="class_id" name="class_id" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs font-medium text-slate-800 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <option value="">Semua kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->school->name }} · {{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex h-9 flex-1 items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 text-xs font-bold text-white transition hover:bg-slate-800">
                    <x-icon name="heroicon-o-magnifying-glass" class="h-4 w-4" />
                    <span>Tampilkan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Card list (mobile) -->
    <div class="space-y-3 sm:hidden">
        @forelse($students as $student)
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="truncate text-sm font-bold text-slate-900">{{ $student->name }}</div>
                        <div class="truncate text-[0.7rem] text-slate-500">{{ $student->email }}</div>
                    </div>
                    <span class="inline-flex shrink-0 items-center rounded-full border px-2.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wider {{ $student->role === 'km' ? 'bg-amber-50 text-amber-700 border-amber-200/80' : 'bg-emerald-50 text-emerald-700 border-emerald-200/80' }}">
                        {{ $student->role === 'km' ? 'KM' : 'Siswa' }}
                    </span>
                </div>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                    <x-icon name="heroicon-o-academic-cap" class="h-4 w-4 text-slate-400" />
                    <span>{{ $student->schoolClass?->name ?? '-' }}</span>
                    <span class="text-slate-300">·</span>
                    <span>{{ $student->schoolClass?->school?->name ?? '-' }}</span>
                </div>
                <div class="mt-3 flex gap-2 border-t border-slate-100 pt-3">
                    <a href="{{ route('students.edit', $student) }}" class="inline-flex h-8 flex-1 items-center justify-center gap-1 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-indigo-600">
                        <x-icon name="heroicon-o-pencil-square" class="h-3.5 w-3.5 text-slate-400" />
                        <span>Edit</span>
                    </a>
                    <form class="inline-flex flex-1" method="POST" action="{{ route('students.destroy', $student) }}" data-confirm-message="Hapus siswa ini beserta jadwal dan riwayat terkait?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex h-8 w-full items-center justify-center gap-1 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-rose-600 transition hover:border-rose-200 hover:bg-rose-50">
                            <x-icon name="heroicon-o-trash" class="h-3.5 w-3.5" />
                            <span>Hapus</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-slate-200 bg-white p-10 text-center">
                <div class="flex flex-col items-center justify-center">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-slate-100 text-slate-400">
                        <x-icon name="heroicon-o-user-group" class="h-5 w-5" />
                    </span>
                    <h3 class="mt-2 text-xs font-bold text-slate-800">Belum ada siswa</h3>
                    <p class="text-[0.7rem] text-slate-500 mt-0.5">Tambahkan siswa lalu pilih kelasnya.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Table (desktop) -->
    <div class="hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm sm:block">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">Siswa</th>
                        <th class="px-4 py-3">Jabatan</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($students as $student)
                        <tr class="transition hover:bg-slate-50/50">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900">{{ $student->name }}</div>
                                <div class="text-[0.7rem] text-slate-500">{{ $student->email }}</div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wider {{ $student->role === 'km' ? 'bg-amber-50 text-amber-700 border-amber-200/80' : 'bg-emerald-50 text-emerald-700 border-emerald-200/80' }}">
                                    {{ $student->role === 'km' ? 'KM' : 'Siswa' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $student->schoolClass?->name }}<small class="block text-slate-400">{{ $student->schoolClass?->school?->name }}</small></td>
                            <td class="whitespace-nowrap px-4 py-3 text-center">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <a href="{{ route('students.edit', $student) }}" class="inline-flex h-7 items-center gap-1 rounded-md border border-slate-200 bg-white px-2.5 text-xs font-semibold text-slate-700 transition hover:border-amber-300 hover:bg-amber-50/50 hover:text-amber-700">
                                        <x-icon name="heroicon-o-pencil-square" class="h-3.5 w-3.5 text-slate-400" />
                                        <span>Edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('students.destroy', $student) }}" data-confirm-message="Hapus siswa ini beserta jadwal dan riwayat terkait?" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-7 items-center gap-1 rounded-md border border-slate-200 bg-white px-2.5 text-xs font-semibold text-rose-600 transition hover:border-rose-200 hover:bg-rose-50" title="Hapus">
                                            <x-icon name="heroicon-o-trash" class="h-3.5 w-3.5" />
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-slate-100 text-slate-400">
                                        <x-icon name="heroicon-o-user-group" class="h-5 w-5" />
                                    </span>
                                    <h3 class="mt-2 text-xs font-bold text-slate-800">Belum ada siswa</h3>
                                    <p class="text-[0.7rem] text-slate-500 mt-0.5">Tambahkan siswa lalu pilih kelasnya.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Footer -->
    @if($students->hasPages())
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-2.5">
            {{ $students->links() }}
        </div>
    @endif
</div>
@endsection
