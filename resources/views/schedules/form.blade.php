@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
]])

@section('content')
<div class="max-w-2xl space-y-6">
    <a href="{{ route('schedules.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 transition hover:text-slate-900">
        <x-icon name="heroicon-o-arrow-left" class="h-4 w-4" />
        Kembali ke Jadwal Piket
    </a>

    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $schedule->exists ? 'Edit' : 'Tambah' }} Jadwal Piket</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $schedule->exists ? 'Perbarui hari dan jenis piket siswa.' : 'Pilih siswa dan hari. Piket Pagi & Piket Pulang akan dibuat sekaligus.' }}</p>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6">
        <form method="POST" action="{{ $schedule->exists ? route('schedules.update', $schedule) : route('schedules.store') }}" class="space-y-5">
            @csrf
            @if($schedule->exists) @method('PUT') @endif

            <div>
                <label for="user_id" class="mb-1.5 block text-sm font-medium text-slate-700">Siswa <span class="text-rose-500">*</span></label>
                <select id="user_id" name="user_id" required class="w-full" @disabled($schedule->exists)>
                    <option value="">Pilih siswa</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id', $schedule->user_id) == $user->id)>
                            {{ $user->schoolClass?->name }} · {{ $user->name }}{{ $user->role === 'km' ? ' (KM)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('user_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="day_of_week" class="mb-1.5 block text-sm font-medium text-slate-700">Hari</label>
                    <select id="day_of_week" name="day_of_week" class="w-full">
                        @foreach(['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'] as $key => $label)
                            <option value="{{ $key }}" @selected(old('day_of_week', $schedule->day_of_week ?? 'Monday') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('day_of_week') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="rounded-lg border border-amber-200/70 bg-amber-50/50 p-4">
                    <p class="text-sm font-semibold text-amber-900">Piket Pagi & Pulang</p>
                    <p class="mt-0.5 text-xs text-amber-800/90">{{ $schedule->exists ? 'Kedua jenis piket selalu dibuat berpasangan dan tidak dapat diubah satu per satu.' : 'Kedua jenis piket akan dibuat otomatis untuk hari yang dipilih.' }}</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-4">
                <a href="{{ route('schedules.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="heroicon-o-check" class="h-4 w-4" />
                    {{ $schedule->exists ? 'Simpan Perubahan' : 'Simpan Jadwal' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
