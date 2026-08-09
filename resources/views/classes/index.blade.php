@extends('layouts.app', ['title' => 'Kelas', 'navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
    ['classes.index', 'Kelas', 'heroicon-o-users'],
]])

@section('content')
<div class="space-y-5">
    <!-- Header Section -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Kelas</h1>
            <p class="mt-1 text-xs font-medium text-slate-500">Buat kelas, lalu buka kelas untuk memasukkan siswa dan menentukan KM.</p>
        </div>
        <a href="{{ route('classes.create') }}" class="inline-flex h-9 shrink-0 items-center justify-center gap-2 rounded-lg bg-amber-500 px-4 text-xs font-bold text-amber-950 shadow-sm transition hover:bg-amber-400">
            <x-icon name="heroicon-o-plus" class="h-4 w-4" />
            <span>Tambah Kelas</span>
        </a>
    </div>

    <!-- Card list (mobile) -->
    <div class="space-y-3 sm:hidden">
        @forelse($classes as $class)
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="truncate text-sm font-bold text-slate-900">{{ $class->name }}</div>
                        <div class="mt-0.5 truncate text-[0.7rem] text-slate-500">{{ $class->school->name }}</div>
                    </div>
                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-slate-200 bg-slate-100 px-2.5 py-0.5 text-[0.65rem] font-bold text-slate-700">
                        <x-icon name="heroicon-o-user-group" class="h-3.5 w-3.5 text-slate-400" />
                        {{ $class->student_count }} siswa
                    </span>
                </div>
                <div class="mt-3 flex gap-2 border-t border-slate-100 pt-3">
                    <a href="{{ route('classes.show', $class) }}" class="inline-flex h-8 flex-1 items-center justify-center gap-1 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 transition hover:border-amber-300 hover:bg-amber-50/50 hover:text-amber-700">
                        <x-icon name="heroicon-o-eye" class="h-3.5 w-3.5 text-slate-400" />
                        <span>Buka</span>
                    </a>
                    <a href="{{ route('classes.edit', $class) }}" class="inline-flex h-8 flex-1 items-center justify-center gap-1 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 transition hover:border-amber-300 hover:bg-amber-50/50 hover:text-amber-700">
                        <x-icon name="heroicon-o-pencil-square" class="h-3.5 w-3.5 text-slate-400" />
                        <span>Edit</span>
                    </a>
                    <form method="POST" action="{{ route('classes.destroy', $class) }}" data-confirm-message="Hapus kelas ini?" class="inline-flex flex-1">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="inline-flex h-8 w-full items-center justify-center gap-1 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-rose-600 transition hover:border-rose-200 hover:bg-rose-50" title="Hapus Kelas">
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
                        <x-icon name="heroicon-o-rectangle-group" class="h-5 w-5" />
                    </span>
                    <h3 class="mt-2 text-xs font-bold text-slate-800">Belum ada data kelas</h3>
                    <p class="mt-0.5 text-[0.7rem] text-slate-500">Tambahkan kelas untuk mulai mengelola data siswa.</p>
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
                        <th class="px-4 py-3">Nama Kelas</th>
                        <th class="px-4 py-3">Sekolah</th>
                        <th class="px-4 py-3">Jumlah Siswa</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($classes as $class)
                        <tr class="transition hover:bg-slate-50/50">
                            <td class="whitespace-nowrap px-4 py-3 font-semibold text-slate-900">
                                <a href="{{ route('classes.show', $class) }}" class="transition hover:text-amber-700">
                                    {{ $class->name }}
                                </a>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500">
                                {{ $class->school->name }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-100 px-2.5 py-0.5 text-[0.65rem] font-bold text-slate-700">
                                    <x-icon name="heroicon-o-user-group" class="h-3.5 w-3.5 text-slate-400" />
                                    {{ $class->student_count }} siswa
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-center">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <a href="{{ route('classes.show', $class) }}" class="inline-flex h-7 items-center gap-1 rounded-md border border-slate-200 bg-white px-2.5 text-xs font-semibold text-slate-700 transition hover:border-amber-300 hover:bg-amber-50/50 hover:text-amber-700">
                                        <x-icon name="heroicon-o-eye" class="h-3.5 w-3.5 text-slate-400" />
                                        <span>Buka</span>
                                    </a>
                                    <a href="{{ route('classes.edit', $class) }}" class="inline-flex h-7 items-center gap-1 rounded-md border border-slate-200 bg-white px-2.5 text-xs font-semibold text-slate-700 transition hover:border-amber-300 hover:bg-amber-50/50 hover:text-amber-700">
                                        <x-icon name="heroicon-o-pencil-square" class="h-3.5 w-3.5 text-slate-400" />
                                        <span>Edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('classes.destroy', $class) }}" data-confirm-message="Hapus kelas ini?">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-7 items-center gap-1 rounded-md border border-slate-200 bg-white px-2.5 text-xs font-semibold text-rose-600 transition hover:border-rose-200 hover:bg-rose-50" title="Hapus Kelas">
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
                                        <x-icon name="heroicon-o-rectangle-group" class="h-5 w-5" />
                                    </span>
                                    <h3 class="mt-2 text-xs font-bold text-slate-800">Belum ada data kelas</h3>
                                    <p class="mt-0.5 text-[0.7rem] text-slate-500">Tambahkan kelas untuk mulai mengelola data siswa.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Footer -->
        @if($classes->hasPages())
            <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-2.5">
                {{ $classes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
