@extends('layouts.app', ['title' => $class->exists ? 'Edit Manajemen Kelas' : 'Tambah Manajemen Kelas', 'navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
    ['classes.index', 'Manajemen Kelas', 'heroicon-o-rectangle-group'],
]])

@section('content')
<div class="max-w-2xl space-y-6">
    <a href="{{ route('classes.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 transition hover:text-slate-900">
        <x-icon name="heroicon-o-arrow-left" class="h-4 w-4" />
        Kembali ke Kelas
    </a>

    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $class->exists ? 'Edit' : 'Tambah' }} Kelas</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $class->exists ? 'Perbarui nama kelas yang terdaftar.' : 'Masukkan nama kelas baru yang akan didaftarkan.' }}</p>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6">
        <form method="POST" action="{{ $class->exists ? route('classes.update', $class) : route('classes.store') }}" class="space-y-5">
            @csrf
            @if($class->exists) @method('PUT') @endif

            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Kelas <span class="text-rose-500">*</span></label>
                <input id="name" name="name" type="text" value="{{ old('name', $class->name) }}" placeholder="Contoh: XII IPA 1" class="w-full" required autofocus>
                @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-4">
                <a href="{{ route('classes.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="heroicon-o-check" class="h-4 w-4" />
                    {{ $class->exists ? 'Simpan Perubahan' : 'Simpan Kelas' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
