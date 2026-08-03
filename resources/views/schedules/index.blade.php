@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'dashboard'],
    ['schedules.index', 'Jadwal Piket', 'clock'],
    ['piket.upload.form', 'Ambil Bukti', 'scan'],
    ['verification.index', 'Verifikasi', 'shield'],
    ['reports.index', 'Laporan', 'report'],
]])
@section('content')
<h1 class="mb-5 text-2xl font-bold text-[#6d1a1a]">Jadwal Piket</h1>
<form method="POST" action="{{ route('schedules.store') }}" class="mb-6 flex flex-wrap items-end gap-3 rounded-xl border border-[#fce4c4] bg-white p-4">
    @csrf
    <div class="flex-1 min-w-[200px]">
        <label class="mb-1 block text-xs font-semibold text-[#5d4037]">Siswa</label>
        <select name="user_id" class="w-full rounded-lg border border-[#fce4c4] bg-white p-2.5 text-sm outline-none transition focus:border-[#6d1a1a]" required>
            <option value="">Pilih siswa</option>
            @foreach($users as $user)<option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }} · {{ $user->schoolClass?->name }}</option>@endforeach
        </select>
    </div>
    <div class="w-[160px]">
        <label class="mb-1 block text-xs font-semibold text-[#5d4037]">Hari</label>
        <select name="day_of_week" class="w-full rounded-lg border border-[#fce4c4] bg-white p-2.5 text-sm outline-none transition focus:border-[#6d1a1a]">
            @foreach(['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'] as $key=>$label)<option value="{{ $key }}" @selected(old('day_of_week') === $key)>{{ $label }}</option>@endforeach
        </select>
    </div>
    <button class="rounded bg-[#6d1a1a] px-4 py-2.5 text-sm font-semibold text-white">Tambah</button>
</form>
<div class="overflow-auto rounded-xl border border-[#fce4c4] bg-white">
    <table class="w-full">
        <thead><tr class="border-b border-[#fce4c4] bg-[#fffdf5] text-left text-xs uppercase tracking-wider text-[#8d6e63]"><th class="p-3">Siswa</th><th>Kelas</th><th>Hari</th><th class="p-3">Aksi</th></tr></thead>
        <tbody class="divide-y divide-[#fce4c4]">@foreach($schedules as $schedule)<tr class="transition hover:bg-[#fffdf5]"><td class="p-3 font-medium text-[#4a1c1c]">{{ $schedule->user->name }}</td><td class="text-xs text-[#8d6e63]">{{ $schedule->user->schoolClass?->name }}</td><td><span class="inline-block rounded-md bg-[#6d1a1a] px-2 py-0.5 text-[.65rem] font-bold text-white">{{ $schedule->day_of_week }}</span></td><td class="p-3"><form method="POST" action="{{ route('schedules.destroy',$schedule) }}">@csrf @method('DELETE')<button class="text-xs font-semibold text-[#c62828] underline underline-offset-2">Hapus</button></form></td></tr>@endforeach</tbody>
    </table>
</div>
{{ $schedules->links() }}
@endsection
