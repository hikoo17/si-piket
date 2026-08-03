@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicons-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicons-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicons-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicons-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicons-o-clipboard-document-list'],
    ['classes.index', 'Kelas', 'heroicons-o-users'],
]])
@section('content')
<div class="flex justify-between">
    <h1 class="text-3xl font-bold">Kelas</h1>
    <a href="{{ route('classes.create') }}" class="rounded bg-[#6d1a1a] px-4 py-2 font-semibold text-white">Tambah</a>
</div>
<div class="mt-5 overflow-auto rounded bg-white">
    <table class="w-full">
        <thead><tr class="border-b border-[#fce4c4] text-left"><th class="p-3">Nama</th><th>Sekolah</th><th>Aksi</th></tr></thead>
        <tbody class="divide-y divide-[#fce4c4]">@foreach($classes as $class)<tr><td class="p-3 font-medium text-[#6d1a1a]">{{ $class->name }}</td><td class="text-sm text-[#8d6e63]">{{ $class->school->name }}</td><td><a class="text-[#6d1a1a] font-semibold underline underline-offset-2" href="{{ route('classes.edit',$class) }}">Edit</a> <form class="inline" method="POST" action="{{ route('classes.destroy',$class) }}">@csrf @method('DELETE')<button class="text-[#c62828] font-semibold underline underline-offset-2">Hapus</button></form></td></tr>@endforeach</tbody>
    </table>
</div>
{{ $classes->links() }}
@endsection
