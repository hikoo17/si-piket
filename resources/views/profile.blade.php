@extends('layouts.app', ['title' => 'Profil Saya'])

@section('content')
<div class="mx-auto max-w-5xl space-y-8 pb-12">
    <header class="space-y-2">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-600">Pengaturan Akun</p>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Profil Saya</h1>
        <p class="max-w-2xl text-sm leading-6 text-slate-500">Lihat dan perbarui informasi akun serta keamanan password Anda.</p>
    </header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1.08fr)_minmax(0,0.92fr)]">
        <form class="surface-card overflow-hidden" method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="flex items-center gap-4 bg-yellow-400 px-6 py-5 sm:px-8 sm:py-6">
                <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-yellow-100 text-xl font-extrabold text-yellow-700 shadow-sm ring-4 ring-yellow-300/70">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                <div class="min-w-0">
                    <h2 class="text-base font-bold text-yellow-950">Informasi Pribadi</h2>
                    <p class="mt-1 text-xs leading-5 text-yellow-900/75">Data ini digunakan untuk identitas akun Anda.</p>
                </div>
            </div>

            <div class="space-y-5 p-6 sm:p-8">
                <div class="space-y-2">
                    <label for="name">Nama Lengkap</label>
                    <input id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label for="phone">Nomor WhatsApp <span class="font-normal text-slate-400">(format 62...)</span></label>
                    <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="6281234567890">
                    @error('phone') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-3 border-t border-slate-100 pt-5 sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 px-4 py-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Role</span>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ ucwords(str_replace('_', ' ', $user->role)) }}</p>
                    </div>
                    @if($user->schoolClass)
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Kelas</span>
                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $user->schoolClass->name }}</p>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end border-t border-slate-100 pt-5">
                    <button class="btn btn-primary w-full sm:w-auto" type="submit"><x-icon name="heroicon-o-check" class="h-4 w-4" />Simpan Profil</button>
                </div>
            </div>
        </form>

        <form class="surface-card h-fit overflow-hidden" method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PUT')

            <div class="flex items-center gap-4 border-b border-amber-100 bg-amber-50 px-6 py-5 sm:px-8 sm:py-6">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-yellow-400 text-yellow-950"><x-icon name="heroicon-o-lock-closed" class="h-5 w-5" /></span>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Ganti Password</h2>
                    <p class="mt-1 text-xs text-slate-500">Gunakan minimal 8 karakter.</p>
                </div>
            </div>

            <div class="space-y-5 p-6 sm:p-8">
                <div class="space-y-2">
                    <label for="current-password">Password Saat Ini</label>
                    <input id="current-password" type="password" name="current_password" required>
                    @error('current_password') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label for="new-password">Password Baru</label>
                    <input id="new-password" type="password" name="password" required>
                    @error('password') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label for="password-confirmation">Konfirmasi Password Baru</label>
                    <input id="password-confirmation" type="password" name="password_confirmation" required>
                </div>
                <div class="border-t border-slate-100 pt-5">
                    <button class="btn btn-primary w-full" type="submit"><x-icon name="heroicon-o-key" class="h-4 w-4" />Perbarui Password</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
