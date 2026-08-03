@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
    ['classes.index', 'Kelas', 'heroicon-o-users'],
]])

@section('content')
<div class="max-w-2xl space-y-6">
    <!-- Tombol Kembali Paling Atas -->
    <div>
        <a href="{{ route('classes.index') }}" 
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 transition-colors hover:text-slate-900">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali ke Daftar Kelas
        </a>
    </div>

    <!-- Header Section -->
    <div>
         <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            {{ $class->exists ? 'Edit' : 'Tambah' }} Kelas
        </h1>
         <p class="mt-1 text-sm text-slate-500">
            Setelah kelas disimpan, Anda dapat membuka detail kelas untuk memasukkan data siswa.
        </p>
    </div>

    <!-- Form Card -->
     <div class="form-card p-6">
        <form method="POST" action="{{ $class->exists ? route('classes.update', $class) : route('classes.store') }}" class="space-y-5">
            @csrf
            @if($class->exists)
                @method('PUT')
            @endif

            <!-- Info Box Sekolah (Aksen Amber Soft) -->
             <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    <div>
                         <span class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Sekolah Terpilih</span>
                         <p class="mt-0.5 font-semibold text-slate-900">{{ $school->name }}</p>
                         <p class="mt-0.5 text-xs text-slate-500">Kelas baru akan otomatis didaftarkan di bawah sekolah ini.</p>
                    </div>
                </div>
            </div>

            <!-- Input Nama Kelas -->
            <div class="space-y-1.5">
                <label for="name" class="block text-sm font-medium text-gray-700">
                    Nama Kelas <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $class->name) }}" 
                    placeholder="Contoh: XII IPA 1"
                     class="w-full" 
                    required
                >
                @error('name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
             <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-3">
                <a href="{{ route('classes.index') }}" 
                    class="btn btn-secondary">
                    Batal
                </a>
                <button 
                    type="submit" 
                     class="btn btn-primary">
                    {{ $class->exists ? 'Simpan Perubahan' : 'Buat Kelas' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
