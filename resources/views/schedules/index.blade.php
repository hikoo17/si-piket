@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
]])
@section('content')
<p class="text-xs font-bold uppercase tracking-[.16em] text-amber-700">Langkah 4 dari 4 · Operasional</p>
<h1 class="mt-1 text-2xl font-bold text-slate-900">Jadwal Piket</h1>
<p class="mb-5 mt-2 text-sm text-slate-500">Pilih siswa yang sudah memiliki kelas. Setelah dijadwalkan, siswa dapat mengirim bukti pada hari tersebut.</p>
<form method="POST" action="{{ route('schedules.store') }}" class="filter-panel mb-6 flex flex-wrap items-end gap-3 p-4">
    @csrf
    <div class="flex-1 min-w-[200px]">
        <label class="mb-1 block text-xs font-semibold text-[#5d4037]">Siswa</label>
        <select name="user_id" class="w-full" required>
            <option value="">Pilih siswa</option>
            @foreach($users as $user)<option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->schoolClass?->name }} · {{ $user->name }}{{ $user->role === 'km' ? ' (KM)' : '' }}</option>@endforeach
        </select>
    </div>
    <div class="w-[160px]">
        <label class="mb-1 block text-xs font-semibold text-[#5d4037]">Hari</label>
        <select name="day_of_week" class="w-full">
            @foreach(['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'] as $key=>$label)<option value="{{ $key }}" @selected(old('day_of_week') === $key)>{{ $label }}</option>@endforeach
        </select>
    </div>
    <button class="btn btn-primary">Tambah</button>
</form>
<div class="table-shell overflow-auto">
    <table class="data-table">
        <thead><tr><th>Siswa</th><th>Kelas</th><th>Hari</th><th>Aksi</th></tr></thead>
        <tbody class="divide-y divide-[#fce4c4]">@foreach($schedules as $schedule)<tr class="transition hover:bg-[#fffdf5]"><td class="p-3 font-medium text-[#4a1c1c]">{{ $schedule->user->name }}</td><td class="text-xs text-[#8d6e63]">{{ $schedule->user->schoolClass?->name }}</td><td><span class="inline-block rounded-md bg-[#6d1a1a] px-2 py-0.5 text-[.65rem] font-bold text-white">{{ $schedule->day_of_week }}</span></td><td class="p-3"><form method="POST" action="{{ route('schedules.destroy',$schedule) }}">@csrf @method('DELETE')<button class="text-xs font-semibold text-[#c62828] underline underline-offset-2">Hapus</button></form></td></tr>@endforeach</tbody>
    </table>
</div>
<div class="app-pagination">{{ $schedules->links() }}</div>
@endsection
