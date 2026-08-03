@extends('layouts.app', ['title' => 'Siswa'])
@section('content')
<div class="flex flex-wrap items-end justify-between gap-4">
    <div>
        <p class="text-xs font-bold uppercase tracking-[.16em] text-amber-700">Langkah 3 dari 4 · Data master</p>
        <h1 class="mt-1 text-3xl font-bold">Siswa & KM</h1>
        <p class="mt-2 text-sm text-amber-900/65">Masukkan siswa ke kelas sebelum menyusun jadwal piket.</p>
    </div>
    <a href="{{ route('students.create', ['class_id' => request('class_id')]) }}" class="rounded-xl bg-[#6d1a1a] px-4 py-2.5 font-semibold text-white">Tambah siswa</a>
</div>

<div class="mt-5 grid gap-3 rounded-xl border border-[#f0dfc9] bg-white p-4 sm:grid-cols-[1fr_240px_auto]">
    <form class="contents" method="GET">
        <input name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="rounded-lg border border-[#ead8c1] p-2.5 outline-none focus:border-[#6d1a1a]">
        <select name="class_id" class="rounded-lg border border-[#ead8c1] p-2.5 outline-none focus:border-[#6d1a1a]">
            <option value="">Semua kelas</option>
            @foreach($classes as $class)<option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->school->name }} · {{ $class->name }}</option>@endforeach
        </select>
        <button class="rounded-lg bg-amber-500 px-4 py-2.5 font-semibold text-amber-950">Tampilkan</button>
    </form>
</div>

<div class="mt-5 overflow-auto rounded-xl border border-[#f0dfc9] bg-white">
    <table class="w-full">
        <thead><tr class="border-b border-[#f0dfc9] bg-amber-50 text-left text-xs uppercase tracking-wider text-amber-900/60"><th class="p-3">Siswa</th><th>Jabatan</th><th>Kelas</th><th class="p-3">Aksi</th></tr></thead>
        <tbody class="divide-y divide-[#f0dfc9]">
            @forelse($students as $student)
                <tr><td class="p-3"><strong>{{ $student->name }}</strong><small class="block text-amber-900/55">{{ $student->email }}</small></td><td><span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold uppercase text-amber-900">{{ $student->role }}</span></td><td>{{ $student->schoolClass?->name }}<small class="block text-amber-900/55">{{ $student->schoolClass?->school?->name }}</small></td><td class="p-3"><a class="font-semibold text-[#6d1a1a] underline" href="{{ route('students.edit', $student) }}">Edit</a> <form class="inline" method="POST" action="{{ route('students.destroy', $student) }}" onsubmit="return confirm('Hapus siswa ini beserta jadwal dan riwayat terkait?')">@csrf @method('DELETE')<button class="ml-2 font-semibold text-red-700 underline">Hapus</button></form></td></tr>
            @empty
                <tr><td colspan="4" class="p-8 text-center text-amber-900/55">Belum ada siswa. Tambahkan siswa lalu pilih kelasnya.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $students->links() }}</div>
@endsection
