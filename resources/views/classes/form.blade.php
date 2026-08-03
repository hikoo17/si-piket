@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicons-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicons-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicons-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicons-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicons-o-clipboard-document-list'],
    ['classes.index', 'Kelas', 'heroicons-o-users'],
]])
@section('content')
<h1 class="mb-5 text-2xl font-bold text-[#6d1a1a]">{{ $class->exists ? 'Edit' : 'Tambah' }} Kelas</h1>
<form class="max-w-xl space-y-4 rounded-xl border border-[#fce4c4] bg-white p-6" method="POST" action="{{ $class->exists ? route('classes.update',$class) : route('classes.store') }}">
    @csrf
    @if($class->exists)@method('PUT')@endif
    <label class="block">Sekolah
        <select name="school_id" class="mt-1 w-full rounded-lg border border-[#fce4c4] p-2 outline-none focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10">@foreach($schools as $school)<option value="{{ $school->id }}" @selected(old('school_id',$class->school_id)==$school->id)>{{ $school->name }}</option>@endforeach</select>
    </label>
    <label class="block">Nama kelas
        <input name="name" value="{{ old('name',$class->name) }}" class="mt-1 w-full rounded-lg border border-[#fce4c4] p-2 outline-none focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10" required>
    </label>
    <button class="rounded bg-[#6d1a1a] px-4 py-2 font-semibold text-white">Simpan</button>
</form>
@endsection
