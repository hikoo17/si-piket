@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
    ['classes.index', 'Kelas', 'heroicon-o-users'],
]])
@section('content')
<p class="text-xs font-bold uppercase tracking-[.16em] text-amber-700">Sekolah → Kelas → Siswa → Jadwal</p>
<h1 class="mb-1 mt-1 text-2xl font-bold text-[#6d1a1a]">{{ $class->exists ? 'Edit' : 'Tambah' }} Kelas</h1>
<p class="mb-5 text-sm text-amber-900/65">Setelah kelas disimpan, buka detail kelas untuk menambahkan siswa.</p>
<form class="max-w-xl space-y-4 rounded-xl border border-[#fce4c4] bg-white p-6" method="POST" action="{{ $class->exists ? route('classes.update',$class) : route('classes.store') }}">
    @csrf
    @if($class->exists)@method('PUT')@endif
    <div class="rounded-lg bg-amber-50 p-3 text-sm"><span class="block text-xs font-bold uppercase tracking-wider text-amber-700">Sekolah tetap</span><strong>{{ $school->name }}</strong><span class="block text-amber-900/60">Kelas baru otomatis dibuat di sekolah ini.</span></div>
    <label class="block">Nama kelas
        <input name="name" value="{{ old('name',$class->name) }}" class="mt-1 w-full rounded-lg border border-[#fce4c4] p-2 outline-none focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10" required>
    </label>
    <button class="rounded bg-[#6d1a1a] px-4 py-2 font-semibold text-white">Simpan</button>
</form>
@endsection
