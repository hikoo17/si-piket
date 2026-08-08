@extends('layouts.app', ['title' => 'Siswa'])
@section('content')
<div class="flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="mt-1 text-2xl font-bold text-slate-900">Siswa & KM</h1>
        <p class="mt-2 text-sm text-slate-500">Masukkan siswa ke kelas sebelum menyusun jadwal piket.</p>
    </div>
    <a href="{{ route('students.create', ['class_id' => request('class_id')]) }}" class="btn btn-primary">Tambah Siswa</a>
</div>

<div class="filter-panel mt-5 grid gap-3 p-4 sm:grid-cols-[1fr_240px_auto]">
    <form class="contents" method="GET">
        <input name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="rounded-lg border border-[#ead8c1] p-2.5 outline-none focus:border-[#6d1a1a]">
        <select name="class_id" class="rounded-lg border border-[#ead8c1] p-2.5 outline-none focus:border-[#6d1a1a]">
            <option value="">Semua kelas</option>
            @foreach($classes as $class)<option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->school->name }} · {{ $class->name }}</option>@endforeach
        </select>
        <button class="btn btn-secondary">Tampilkan</button>
    </form>
</div>

<div class="table-shell mt-5 overflow-auto">
    <table class="data-table">
        <thead><tr><th>Siswa</th><th>Jabatan</th><th>Kelas</th><th>Aksi</th></tr></thead>
        <tbody class="divide-y divide-[#f0dfc9]">
            @forelse($students as $student)
                <tr><td><strong class="text-slate-900">{{ $student->name }}</strong><small class="block text-slate-500">{{ $student->email }}</small></td><td><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold uppercase text-slate-600">{{ $student->role }}</span></td><td>{{ $student->schoolClass?->name }}<small class="block text-slate-500">{{ $student->schoolClass?->school?->name }}</small></td><td><a class="font-semibold text-indigo-600" href="{{ route('students.edit', $student) }}">Edit</a> <form class="inline" method="POST" action="{{ route('students.destroy', $student) }}" data-confirm-message="Hapus siswa ini beserta jadwal dan riwayat terkait?">@csrf @method('DELETE')<button class="ml-3 font-semibold text-rose-600">Hapus</button></form></td></tr>
            @empty
                <tr><td colspan="4" class="p-8 text-center text-amber-900/55">Belum ada siswa. Tambahkan siswa lalu pilih kelasnya.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="app-pagination">{{ $students->links() }}</div>
@endsection
