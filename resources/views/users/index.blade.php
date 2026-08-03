@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
    ['users.index', 'Pengguna', 'heroicon-o-users'],
]])
@section('content')
<div class="flex items-end justify-between gap-4">
    <div><h1 class="text-2xl font-bold text-slate-900">Staf & Pengguna</h1><p class="mt-1 text-sm text-slate-500">Kelola akun dan hak akses pengguna aplikasi.</p></div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">Tambah Pengguna</a>
</div>
<div class="table-shell mt-5 overflow-auto">
    <table class="data-table">
        <thead><tr><th>Nama</th><th>Role</th><th>Kelas</th><th>Aksi</th></tr></thead>
        <tbody>@foreach($users as $user)<tr><td><strong class="text-slate-900">{{ $user->name }}</strong><small class="block text-slate-500">{{ $user->email }}</small></td><td><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">{{ strtoupper($user->role) }}</span></td><td>{{ $user->schoolClass?->name ?? '-' }}</td><td><a href="{{ route('users.edit',$user) }}" class="font-semibold text-indigo-600">Edit</a> <form class="inline" method="POST" action="{{ route('users.destroy',$user) }}" onsubmit="return confirm('Hapus pengguna ini?')">@csrf @method('DELETE')<button class="ml-3 font-semibold text-rose-600">Hapus</button></form></td></tr>@endforeach</tbody>
    </table>
</div>
<div class="app-pagination">{{ $users->links() }}</div>
@endsection
