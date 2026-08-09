@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
    ['users.index', 'Manajemen Pengguna', 'heroicon-o-users'],
]])
@section('content')
<div class="max-w-2xl space-y-5">
    <div class="mb-4">
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 transition hover:text-slate-700">
            <x-icon name="heroicon-o-arrow-left" class="h-4 w-4" />
            <span>Kembali</span>
        </a>
    </div>
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
                <input name="name" value="{{ old('name', $user->name) }}" placeholder="Nama lengkap" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" required>
                @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="nama@email.com" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" required>
                @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">WhatsApp <span class="font-medium normal-case text-slate-400">(format 62...)</span></label>
                <input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="6281234567890" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                @error('phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Role</label>
                <select id="user-role" name="role" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                    @php
                        $roleLabels = [
                            'admin' => 'Administrator',
                            'wali_kelas' => 'Wali Kelas',
                            'km' => 'Ketua Kelas',
                            'siswa' => 'Siswa',
                        ];
                    @endphp
                    @foreach(['admin','wali_kelas','km','siswa'] as $role)
                        <option value="{{ $role }}" @selected(old('role',$user->role)===$role)>{{ $roleLabels[$role] }}</option>
                    @endforeach
                </select>
                @error('role') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Kelas</label>
                <select id="user-class" name="class_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                    <option value="">Tanpa kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected(old('class_id',$user->class_id)==$class->id)>{{ $class->school->name }} · {{ $class->name }}</option>
                    @endforeach
                </select>
                @error('class_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1.5 relative">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Password</label>
                <input id="user-password" type="password" name="password" placeholder="Minimal 8 karakter" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 pr-10 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10" @required(! $user->exists)>
                <button type="button" onclick="togglePassword('user-password', this)" class="absolute right-3 top-[1.7rem] text-slate-400 hover:text-indigo-600 transition">
                    <x-icon name="heroicon-o-eye" class="h-4 w-4" id="user-icon-eye" />
                    <x-icon name="heroicon-o-eye-slash" class="h-4 w-4 hidden" id="user-icon-eye-slash" />
                </button>
                @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1.5 relative">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Konfirmasi Password</label>
                <input id="user-password_confirmation" type="password" name="password_confirmation" placeholder="Ulangi password" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 pr-10 text-sm text-slate-900 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                <button type="button" onclick="togglePassword('user-password_confirmation', this)" class="absolute right-3 top-[1.7rem] text-slate-400 hover:text-indigo-600 transition">
                    <x-icon name="heroicon-o-eye" class="h-4 w-4" id="user-icon-eye-confirm" />
                    <x-icon name="heroicon-o-eye-slash" class="h-4 w-4 hidden" id="user-icon-eye-slash-confirm" />
                </button>
                @error('password_confirmation') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <script>
            (function() {
                const roleSelect = document.getElementById('user-role');
                const classSelect = document.getElementById('user-class');
                const noClassOption = classSelect.querySelector('option[value=""]');

                function updateClassState() {
                    const role = roleSelect.value;
                    const disabled = role === 'admin';

                    classSelect.disabled = disabled;
                    classSelect.classList.toggle('opacity-60', disabled);
                    classSelect.classList.toggle('bg-slate-100', disabled);

                    if (disabled) {
                        classSelect.value = '';
                    }
                }

                roleSelect.addEventListener('change', updateClassState);
                updateClassState();
            })();

            function togglePassword(inputId, button) {
                const input = document.getElementById(inputId);
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';

                const eyeId = inputId === 'user-password' ? 'user-icon-eye' : 'user-icon-eye-confirm';
                const eyeSlashId = inputId === 'user-password' ? 'user-icon-eye-slash' : 'user-icon-eye-slash-confirm';

                const eyeIcon = document.getElementById(eyeId);
                const eyeSlashIcon = document.getElementById(eyeSlashId);

                if (isPassword) {
                    eyeIcon?.classList.remove('hidden');
                    eyeSlashIcon?.classList.add('hidden');
                } else {
                    eyeIcon?.classList.add('hidden');
                    eyeSlashIcon?.classList.remove('hidden');
                }
            }
        </script>

        <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
            <a href="{{ route('users.index') }}" class="inline-flex h-10 w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 sm:w-auto">Batal</a>
            <button class="inline-flex h-10 w-full items-center justify-center rounded-xl bg-indigo-600 px-6 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 sm:w-auto">Simpan</button>
        </div>
    </form>
</div>
@endsection
