@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicons-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicons-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicons-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicons-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicons-o-clipboard-document-list'],
    ['users.index', 'Pengguna', 'heroicons-o-users'],
]])
@section('content')
<h1 class="mb-5 text-2xl font-bold text-[#6d1a1a]">{{ $user->exists ? 'Edit' : 'Tambah' }} Pengguna</h1>
<form class="max-w-xl space-y-4 rounded-xl border border-[#fce4c4] bg-white p-6" method="POST" action="{{ $user->exists ? route('users.update',$user) : route('users.store') }}">
    @csrf
    @if($user->exists)@method('PUT')@endif
    @foreach(['name'=>'Nama','email'=>'Email','phone'=>'WhatsApp (format 62...)'] as $field=>$label)
        <label class="block">{{ $label }}
            <input name="{{ $field }}" value="{{ old($field,$user->{$field}) }}" class="mt-1 w-full rounded-lg border border-[#fce4c4] p-2 outline-none focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10" @if($field!=='phone')required@endif>
        </label>
    @endforeach
    <label class="block">Role
        <select name="role" class="mt-1 w-full rounded-lg border border-[#fce4c4] p-2 outline-none focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10">@foreach(['admin','guru','km','siswa'] as $role)<option @selected(old('role',$user->role)===$role)>{{ $role }}</option>@endforeach</select>
    </label>
    <label class="block">Kelas
        <select name="class_id" class="mt-1 w-full rounded-lg border border-[#fce4c4] p-2 outline-none focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10"><option value="">Tanpa kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(old('class_id',$user->class_id)==$class->id)>{{ $class->school->name }} · {{ $class->name }}</option>@endforeach</select>
    </label>
    <label class="block">Password
        <input type="password" name="password" class="mt-1 w-full rounded-lg border border-[#fce4c4] p-2 outline-none focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10" @if(!$user->exists)required@endif>
    </label>
    <label class="block">Konfirmasi password
        <input type="password" name="password_confirmation" class="mt-1 w-full rounded-lg border border-[#fce4c4] p-2 outline-none focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10">
    </label>
    <button class="rounded bg-[#6d1a1a] px-4 py-2 font-semibold text-white">Simpan</button>
</form>
@endsection
