@extends('layouts.app')
@section('content')
<h1 class="mb-6 text-3xl font-bold">Dashboard</h1>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">@foreach(['Jadwal Hari Ini'=>$scheduleCount,'Menunggu Verifikasi'=>$pendingCount,'Sudah Disetujui'=>$approvedCount,'Total Siswa/KM'=>$studentCount] as $label=>$value)<div class="rounded-xl bg-white p-5 shadow"><p class="text-sm text-slate-500">{{ $label }}</p><p class="mt-2 text-3xl font-bold">{{ $value }}</p></div>@endforeach</div>
<div class="mt-8 flex flex-wrap gap-3">
@if(auth()->user()->role==='admin')<a class="rounded bg-indigo-700 px-4 py-2 text-white" href="{{ route('schools.index') }}">Kelola Sekolah</a><a class="rounded bg-indigo-700 px-4 py-2 text-white" href="{{ route('classes.index') }}">Kelola Kelas</a><a class="rounded bg-indigo-700 px-4 py-2 text-white" href="{{ route('users.index') }}">Kelola Pengguna</a>@endif
@if(in_array(auth()->user()->role,['siswa','km']))<a class="rounded bg-emerald-700 px-4 py-2 text-white" href="{{ route('piket.upload.form') }}">Ambil Bukti Piket</a>@endif
@if(in_array(auth()->user()->role,['admin','km']))<a class="rounded bg-amber-600 px-4 py-2 text-white" href="{{ route('schedules.index') }}">Atur Jadwal</a>@endif
@if(in_array(auth()->user()->role,['admin','guru','km']))<a class="rounded bg-sky-700 px-4 py-2 text-white" href="{{ route('verification.index') }}">Verifikasi</a>@endif
</div>@endsection
