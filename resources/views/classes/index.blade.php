@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
    ['classes.index', 'Kelas', 'heroicon-o-users'],
]])

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <span>Langkah 2 dari 4</span>
                <span>•</span>
                <span>Data Master</span>
            </div>
             <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Kelas</h1>
             <p class="mt-1 text-sm text-slate-500">
                Buat kelas, lalu buka kelas untuk memasukkan siswa dan menentukan KM.
            </p>
        </div>

        <a href="{{ route('classes.create') }}" 
            class="btn btn-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Kelas
        </a>
    </div>

    <!-- Table Section -->
    <div class="table-shell overflow-hidden">
        <div class="overflow-x-auto">
             <table class="data-table">
                 <thead>
                    <tr>
                        <th scope="col" class="px-6 py-3.5">Nama Kelas</th>
                        <th scope="col" class="px-6 py-3.5">Sekolah</th>
                        <th scope="col" class="px-6 py-3.5">Jumlah Siswa</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                 <tbody>
                    @forelse($classes as $class)
                        <tr class="transition-colors hover:bg-gray-50/80">
                            <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                                 <a href="{{ route('classes.show', $class) }}" class="font-semibold text-slate-900 hover:text-indigo-600 transition-colors">
                                    {{ $class->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                {{ $class->school->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                     <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                    {{ $class->student_count }} siswa
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('classes.show', $class) }}" 
                                        class="rounded px-2 py-1 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                                        Buka
                                    </a>
                                    <a href="{{ route('classes.edit', $class) }}" 
                                        class="rounded px-2 py-1 text-indigo-600 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('classes.destroy', $class) }}" class="inline" data-confirm-message="Hapus kelas ini?">
                                        @csrf 
                                        @method('DELETE')
                                         <button type="submit" class="rounded px-2 py-1 text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                             <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-400">
                                Belum ada data kelas yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($classes->hasPages())
             <div class="border-t border-slate-200 bg-slate-50/50 px-6 py-3">
                {{ $classes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
