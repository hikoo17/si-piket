@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicons-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicons-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicons-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicons-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicons-o-clipboard-document-list'],
    ['users.index', 'Pengguna', 'heroicons-o-users'],
]])
@section('content')
<div class="flex justify-between">
    <h1 class="text-3xl font-bold">Pengguna</h1>
    <a href="{{ route('users.create') }}" class="rounded bg-[#6d1a1a] px-4 py-2 font-semibold text-white">Tambah</a>
</div>
<div class="mt-5 overflow-auto rounded bg-white">
    <table class="w-full">
        <thead><tr class="border-b border-[#fce4c4] text-left"><th class="p-3">Nama</th><th>Role</th><th>Kelas</th><th>Aksi</th></tr></thead>
        <tbody>@foreach($users as $user)<tr class="border-b border-[#fce4c4]"><td class="p-3">{{ $user->name }}<small class="block text-[#8d6e63]">{{ $user->email }}</small></td><td class="font-semibold text-[#6d1a1a]">{{ strtoupper($user->role) }}</td><td>{{ $user->schoolClass?->name ?? '-' }}</td><td><a href="{{ route('users.edit',$user) }}" class="text-[#6d1a1a] font-semibold underline underline-offset-2">Edit</a> <form class="inline" method="POST" action="{{ route('users.destroy',$user) }}">@csrf @method('DELETE')<button class="text-[#c62828] font-semibold underline underline-offset-2">Hapus</button></form></td></tr>@endforeach</tbody>
    </table>
</div>
{{ $users->links() }}
@endsection
