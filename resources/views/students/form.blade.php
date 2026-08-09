@extends('layouts.app', ['title' => $student->exists ? 'Edit Siswa' : 'Tambah Siswa'])
@section('content')
<div class="max-w-2xl space-y-5">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900">{{ $student->exists ? 'Edit' : 'Tambah' }} Siswa</h1>
        <p class="mt-0.5 text-xs font-medium text-slate-500">KM tetap tercatat sebagai anggota kelas, dengan akses untuk mengatur jadwal kelasnya.</p>
    </div>

    <form class="space-y-4 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8" method="POST" action="{{ $student->exists ? route('students.update', $student) : route('students.store') }}">
        @csrf
        @if($student->exists)@method('PUT')@endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-1.5 sm:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Nama lengkap</label>
                <input name="name" value="{{ old('name', $student->name) }}" placeholder="Nama lengkap" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-amber-500/10" required>
                @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Email</label>
                <input type="email" name="email" value="{{ old('email', $student->email) }}" placeholder="nama@email.com" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-amber-500/10" required>
                @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">WhatsApp <span class="font-medium normal-case text-slate-400">(format 62...)</span></label>
                <input name="phone" value="{{ old('phone', $student->phone) }}" placeholder="6281234567890" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-amber-500/10">
                @error('phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Kelas</label>
                <select name="class_id" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-amber-500/10" required>
                    <option value="">Pilih kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected(old('class_id', $student->class_id) == $class->id)>{{ $class->school->name }} · {{ $class->name }}</option>
                    @endforeach
                </select>
                @error('class_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Jabatan</label>
                <select name="role" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-900 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-amber-500/10">
                    <option value="siswa" @selected(old('role', $student->role) === 'siswa')>Siswa</option>
                    <option value="km" @selected(old('role', $student->role) === 'km')>Ketua Murid (KM)</option>
                </select>
                @error('role') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1.5 relative">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Password</label>
                <input id="student-password" type="password" name="password" placeholder="Minimal 8 karakter" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 pr-10 text-sm text-slate-900 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-amber-500/10" @required(!$student->exists)>
                <button type="button" onclick="togglePassword('student-password', this)" class="absolute right-3 top-[1.7rem] text-slate-400 hover:text-amber-600 transition">
                    <x-icon name="heroicon-o-eye" class="h-4 w-4" id="student-icon-eye" />
                    <x-icon name="heroicon-o-eye-slash" class="h-4 w-4 hidden" id="student-icon-eye-slash" />
                </button>
                @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1.5 relative">
                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600">Konfirmasi password</label>
                <input id="student-password_confirmation" type="password" name="password_confirmation" placeholder="Ulangi password" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 pr-10 text-sm text-slate-900 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-amber-500/10" @required(!$student->exists)>
                <button type="button" onclick="togglePassword('student-password_confirmation', this)" class="absolute right-3 top-[1.7rem] text-slate-400 hover:text-amber-600 transition">
                    <x-icon name="heroicon-o-eye" class="h-4 w-4" id="student-icon-eye-confirm" />
                    <x-icon name="heroicon-o-eye-slash" class="h-4 w-4 hidden" id="student-icon-eye-slash-confirm" />
                </button>
                @error('password_confirmation') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
            <a href="{{ route('students.index') }}" class="inline-flex h-10 w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 sm:w-auto">Batal</a>
            <button class="inline-flex h-10 w-full items-center justify-center rounded-xl bg-indigo-600 px-6 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 sm:w-auto">Simpan Siswa</button>
        </div>
    </form>
</div>

<script>
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';

        const eyeId = inputId === 'student-password' ? 'student-icon-eye' : 'student-icon-eye-confirm';
        const eyeSlashId = inputId === 'student-password' ? 'student-icon-eye-slash' : 'student-icon-eye-slash-confirm';

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
@endsection
