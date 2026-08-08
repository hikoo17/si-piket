@extends('layouts.app', ['title' => $student->exists ? 'Edit Siswa' : 'Tambah Siswa'])
@section('content')
<div class="mb-5">
    <h1 class="mt-1 text-2xl font-bold text-slate-900">{{ $student->exists ? 'Edit' : 'Tambah' }} Siswa</h1>
    <p class="mt-2 text-sm text-slate-500">KM tetap tercatat sebagai anggota kelas, dengan akses untuk mengatur jadwal kelasnya.</p>
</div>
<form class="form-card max-w-2xl space-y-4 p-6" method="POST" action="{{ $student->exists ? route('students.update', $student) : route('students.store') }}">
    @csrf
    @if($student->exists)@method('PUT')@endif
    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block sm:col-span-2">Nama lengkap<input name="name" value="{{ old('name', $student->name) }}" class="mt-1 w-full rounded-lg border border-[#ead8c1] p-2.5" required></label>
        <label class="block">Email<input type="email" name="email" value="{{ old('email', $student->email) }}" class="mt-1 w-full rounded-lg border border-[#ead8c1] p-2.5" required></label>
        <label class="block">WhatsApp <small>(format 62...)</small><input name="phone" value="{{ old('phone', $student->phone) }}" class="mt-1 w-full rounded-lg border border-[#ead8c1] p-2.5"></label>
        <label class="block">Kelas<select name="class_id" class="mt-1 w-full rounded-lg border border-[#ead8c1] p-2.5" required><option value="">Pilih kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(old('class_id', $student->class_id) == $class->id)>{{ $class->school->name }} · {{ $class->name }}</option>@endforeach</select></label>
        <label class="block">Jabatan<select name="role" class="mt-1 w-full rounded-lg border border-[#ead8c1] p-2.5"><option value="siswa" @selected(old('role', $student->role) === 'siswa')>Siswa</option><option value="km" @selected(old('role', $student->role) === 'km')>Ketua Murid (KM)</option></select></label>
        <label class="block">Password<input type="password" name="password" class="mt-1 w-full rounded-lg border border-[#ead8c1] p-2.5" @required(!$student->exists)><small class="text-amber-900/55">{{ $student->exists ? 'Kosongkan jika tidak diubah.' : 'Minimal 8 karakter.' }}</small></label>
        <label class="block">Konfirmasi password<input type="password" name="password_confirmation" class="mt-1 w-full rounded-lg border border-[#ead8c1] p-2.5" @required(!$student->exists)></label>
    </div>
    <div class="flex justify-end gap-3 border-t border-slate-100 pt-4"><a href="{{ route('students.index') }}" class="btn btn-secondary">Batal</a><button class="btn btn-primary">Simpan Siswa</button></div>
</form>
@endsection
