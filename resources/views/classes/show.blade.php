@extends('layouts.app', ['title' => $class->name])
@section('content')
<div class="flex flex-wrap items-end justify-between gap-4">
    <div><p class="text-xs font-bold uppercase tracking-[.16em] text-amber-700">{{ $class->school->name }}</p><h1 class="mt-1 text-3xl font-bold">Kelas {{ $class->name }}</h1><p class="mt-2 text-sm text-amber-900/65">{{ $class->student_count }} anggota terdaftar. Tambahkan siswa sebelum menyusun jadwal.</p></div>
    <div class="flex gap-2"><a href="{{ route('students.create', ['class_id' => $class->id]) }}" class="rounded-xl bg-[#6d1a1a] px-4 py-2.5 font-semibold text-white">Tambah siswa</a><a href="{{ route('classes.edit', $class) }}" class="rounded-xl border border-[#ead8c1] bg-white px-4 py-2.5 font-semibold">Edit kelas</a></div>
</div>
<div class="mt-6 overflow-auto rounded-xl border border-[#f0dfc9] bg-white">
    <table class="w-full"><thead><tr class="border-b border-[#f0dfc9] bg-amber-50 text-left text-xs uppercase tracking-wider text-amber-900/60"><th class="p-3">Nama</th><th>Jabatan</th><th>Email</th><th class="p-3">Aksi</th></tr></thead><tbody class="divide-y divide-[#f0dfc9]">@forelse($students as $student)<tr><td class="p-3 font-semibold">{{ $student->name }}</td><td class="uppercase">{{ $student->role }}</td><td>{{ $student->email }}</td><td class="p-3"><a class="font-semibold text-[#6d1a1a] underline" href="{{ route('students.edit', $student) }}">Edit</a></td></tr>@empty<tr><td colspan="4" class="p-8 text-center text-amber-900/55">Kelas ini belum mempunyai siswa.</td></tr>@endforelse</tbody></table>
</div>
<div class="mt-4">{{ $students->links() }}</div>
@endsection
