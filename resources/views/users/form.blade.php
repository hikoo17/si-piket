@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
    ['users.index', 'Pengguna', 'heroicon-o-users'],
]])
@section('content')
<div class="mx-auto max-w-2xl space-y-5">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">{{ $user->exists ? 'Edit' : 'Tambah' }} Pengguna</h1>
        <p class="mt-0.5 text-xs font-medium text-slate-500">Lengkapi identitas dan tentukan akses pengguna.</p>
    </div>

    <form class="space-y-4 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8" method="POST" action="{{ $user->exists ? route('users.update',$user) : route('users.store') }}">
        @csrf
        @if($user->exists)@method('PUT')@endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-1.5 sm:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Nama</label>
                <input name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" required>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" required>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">WhatsApp <span class="font-medium normal-case text-slate-400">(format 62...)</span></label>
                <input name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Role</label>
                <select name="role" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                    @foreach(['admin','guru','km','siswa'] as $role)
                        <option @selected(old('role',$user->role)===$role)>{{ $role }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Kelas</label>
                <select name="class_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                    <option value="">Tanpa kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected(old('class_id',$user->class_id)==$class->id)>{{ $class->school->name }} · {{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Password</label>
                <input type="password" name="password" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" @required(! $user->exists)>
                <p class="text-[0.7rem] text-slate-500">{{ $user->exists ? 'Kosongkan jika tidak diubah.' : 'Minimal 8 karakter.' }}</p>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
            </div>
        </div>

        <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
            <a href="{{ route('users.index') }}" class="inline-flex h-10 w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 sm:w-auto">Batal</a>
            <button class="inline-flex h-10 w-full items-center justify-center rounded-xl bg-indigo-600 px-6 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 sm:w-auto">Simpan</button>
        </div>
    </form>
</div>
@endsection
