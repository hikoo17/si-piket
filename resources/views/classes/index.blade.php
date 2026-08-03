@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
    ['classes.index', 'Kelas', 'heroicon-o-users'],
]])
@section('content')
<div class="flex flex-wrap items-end justify-between gap-4">
    <div><p class="text-xs font-bold uppercase tracking-[.16em] text-amber-700">Langkah 2 dari 4 · Data master</p><h1 class="mt-1 text-3xl font-bold">Kelas</h1><p class="mt-2 text-sm text-amber-900/65">Buat kelas, lalu buka kelas untuk memasukkan siswa dan menentukan KM.</p></div>
    <a href="{{ route('classes.create') }}" class="rounded-xl bg-[#6d1a1a] px-4 py-2.5 font-semibold text-white">Tambah kelas</a>
</div>
<div class="mt-5 overflow-auto rounded-xl border border-[#f0dfc9] bg-white">
    <table class="w-full">
        <thead><tr class="border-b border-[#fce4c4] bg-amber-50 text-left"><th class="p-3">Nama</th><th>Sekolah</th><th>Siswa</th><th>Aksi</th></tr></thead>
        <tbody class="divide-y divide-[#fce4c4]">@foreach($classes as $class)<tr><td class="p-3"><a class="font-bold text-[#6d1a1a] underline underline-offset-2" href="{{ route('classes.show',$class) }}">{{ $class->name }}</a></td><td class="text-sm text-[#8d6e63]">{{ $class->school->name }}</td><td><span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-900">{{ $class->student_count }} siswa</span></td><td><a class="text-[#6d1a1a] font-semibold underline underline-offset-2" href="{{ route('classes.show',$class) }}">Buka</a> <a class="ml-2 text-[#6d1a1a] font-semibold underline underline-offset-2" href="{{ route('classes.edit',$class) }}">Edit</a> <form class="inline" method="POST" action="{{ route('classes.destroy',$class) }}" onsubmit="return confirm('Hapus kelas ini?')">@csrf @method('DELETE')<button class="ml-2 text-[#c62828] font-semibold underline underline-offset-2">Hapus</button></form></td></tr>@endforeach</tbody>
    </table>
</div>
{{ $classes->links() }}
@endsection
