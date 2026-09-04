@extends('layouts.app', ['title' => 'Profil Saya'])

@section('content')
<div class="mx-auto max-w-5xl space-y-6 pb-12">
    <div class="space-y-1.5">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Profil Saya</h1>
        <p class="text-sm text-slate-500">Lihat dan perbarui informasi akun serta keamanan password Anda.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,0.8fr)]">
        <form class="surface-card space-y-6 p-6 sm:p-8" method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            <div class="flex items-center gap-4 border-b border-slate-100 pb-5">
                <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-indigo-100 text-lg font-extrabold text-indigo-700">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                <div><h2 class="text-base font-bold text-slate-900">Informasi Pribadi</h2><p class="text-xs text-slate-500">Data ini digunakan untuk identitas akun Anda.</p></div>
            </div>
            <div class="space-y-4">
                <div class="space-y-1.5"><label for="name">Nama Lengkap</label><input id="name" name="name" value="{{ old('name', $user->name) }}" required>@error('name')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div class="space-y-1.5"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>@error('email')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div class="space-y-1.5"><label for="phone">Nomor WhatsApp <span class="font-normal text-slate-400">(format 62...)</span></label><input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="6281234567890">@error('phone')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div class="grid gap-4 sm:grid-cols-2"><div><span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Role</span><p class="mt-1 text-sm font-semibold text-slate-900">{{ ucwords(str_replace('_', ' ', $user->role)) }}</p></div>@if($user->schoolClass)<div><span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kelas</span><p class="mt-1 text-sm font-semibold text-slate-900">{{ $user->schoolClass->name }}</p></div>@endif</div>
            </div>
            <button class="btn btn-primary w-full sm:w-auto" type="submit"><x-icon name="heroicon-o-check" class="h-4 w-4" />Simpan Profil</button>
        </form>

        <form class="surface-card h-fit space-y-6 p-6 sm:p-8" method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PUT')
            <div class="flex items-center gap-4 border-b border-slate-100 pb-5"><span class="grid h-10 w-10 place-items-center rounded-xl bg-amber-100 text-amber-700"><x-icon name="heroicon-o-lock-closed" class="h-5 w-5" /></span><div><h2 class="text-base font-bold text-slate-900">Ganti Password</h2><p class="text-xs text-slate-500">Gunakan minimal 8 karakter.</p></div></div>
            <div class="space-y-4">
                <div class="space-y-1.5"><label for="current-password">Password Saat Ini</label><input id="current-password" type="password" name="current_password" required>@error('current_password')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div class="space-y-1.5"><label for="new-password">Password Baru</label><input id="new-password" type="password" name="password" required>@error('password')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                <div class="space-y-1.5"><label for="password-confirmation">Konfirmasi Password Baru</label><input id="password-confirmation" type="password" name="password_confirmation" required></div>
            </div>
            <button class="btn btn-primary w-full" type="submit"><x-icon name="heroicon-o-key" class="h-4 w-4" />Perbarui Password</button>
        </form>
    </div>
</div>
@endsection
