@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'dashboard'],
    ['schedules.index', 'Jadwal Piket', 'clock'],
    ['piket.upload.form', 'Ambil Bukti', 'scan'],
    ['verification.index', 'Verifikasi', 'shield'],
    ['reports.index', 'Laporan', 'report'],
]])
@section('content')
<h1 class="mb-5 text-2xl font-bold text-[#6d1a1a]">Verifikasi Bukti</h1>
<div class="grid gap-5 md:grid-cols-2">
    @forelse($logs as $log)
    <article class="rounded-xl border border-[#fce4c4] bg-white p-5 shadow-sm transition hover:shadow-md">
        @if($log->photo_path)<img src="{{ Storage::url($log->photo_path) }}" class="mb-4 h-56 w-full rounded-lg object-cover" alt="Bukti {{ $log->user->name }}">@endif
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-[#6d1a1a]">{{ $log->user->name }}</h2>
                <p class="text-xs text-[#8d6e63]">{{ $log->user->schoolClass?->name }} · {{ $log->date->format('d/m/Y') }}</p>
            </div>
            <span class="inline-block rounded-full px-2 py-0.5 text-[.65rem] font-bold uppercase {{ $log->status==='approved' ? 'bg-emerald-100 text-emerald-800' : ($log->status==='pending' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">{{ strtoupper($log->status) }}</span>
        </div>
        <p class="mt-2 text-sm text-[#4a1c1c]">Jarak: <span class="font-semibold">{{ $log->distance_meters }} m</span></p>
        @if($log->status==='pending')
        <div class="mt-4 flex flex-wrap gap-2">
            <form method="POST" action="{{ route('verification.approve',$log) }}">@csrf @method('PATCH')<button class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-px hover:bg-emerald-700">Approve</button></form>
            <form method="POST" action="{{ route('verification.reject',$log) }}" class="flex gap-2">@csrf @method('PATCH')<input required maxlength="255" name="rejection_reason" placeholder="Alasan penolakan" class="flex-1 rounded-lg border border-[#fce4c4] bg-white p-2 text-sm outline-none transition focus:border-[#6d1a1a]"><button class="rounded-lg bg-[#c62828] px-3 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-px hover:bg-[#b71c1c]">Reject</button></form>
        </div>
        @elseif($log->rejection_reason)
        <p class="mt-3 rounded-lg border border-red-200 bg-red-50 p-2.5 text-xs font-semibold text-[#c62828]">{{ $log->rejection_reason }}</p>
        @endif
    </article>
    @empty
    <p class="col-span-2 rounded-xl border border-dashed border-[#fce4c4] bg-white p-8 text-center text-sm text-[#8d6e63]">Belum ada bukti piket.</p>
    @endforelse
</div>
{{ $logs->links() }}
@endsection
