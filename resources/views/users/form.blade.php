@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
    ['users.index', 'Pengguna', 'heroicon-o-users'],
]])
@section('content')
<div class="mb-5"><h1 class="text-2xl font-bold text-slate-900">{{ $user->exists ? 'Edit' : 'Tambah' }} Pengguna</h1><p class="mt-1 text-sm text-slate-500">Lengkapi identitas dan tentukan akses pengguna.</p></div>
<form class="form-card max-w-xl space-y-4 p-6" method="POST" action="{{ $user->exists ? route('users.update',$user) : route('users.store') }}">
    @csrf
    @if($user->exists)@method('PUT')@endif
    @foreach(['name'=>'Nama','email'=>'Email','phone'=>'WhatsApp (format 62...)'] as $field=>$label)
        <label class="block">{{ $label }}
            <input name="{{ $field }}" value="{{ old($field,$user->{$field}) }}" class="mt-1 w-full" @if($field!=='phone')required@endif>
        </label>
    @endforeach
    <label class="block">Role
        <select name="role" class="mt-1 w-full">@foreach(['admin','guru','km','siswa'] as $role)<option @selected(old('role',$user->role)===$role)>{{ $role }}</option>@endforeach</select>
    </label>
    <label class="block">Kelas
        <select name="class_id" class="mt-1 w-full"><option value="">Tanpa kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(old('class_id',$user->class_id)==$class->id)>{{ $class->school->name }} · {{ $class->name }}</option>@endforeach</select>
    </label>
    <label class="block">Password
        <input type="password" name="password" class="mt-1 w-full" @if(!$user->exists)required@endif>
    </label>
    <label class="block">Konfirmasi password
        <input type="password" name="password_confirmation" class="mt-1 w-full">
    </label>
    <div class="flex justify-end gap-3 border-t border-slate-100 pt-4"><a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a><button class="btn btn-primary">Simpan</button></div>
</form>
@endsection
