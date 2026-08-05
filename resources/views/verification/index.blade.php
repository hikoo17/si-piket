@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
]])

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-[#6d1a1a]">Verifikasi Bukti</h1>
    <p class="mt-1 text-sm text-[#8d6e63]">Hanya bukti yang belum diproses yang ditampilkan. Bukti yang sudah disetujui dapat dilihat di halaman laporan.</p>
</div>

<div class="grid gap-5 md:grid-cols-2">
    @forelse($logs as $log)
        <article class="flex flex-col justify-between rounded-xl border border-[#fce4c4] bg-white p-5 shadow-sm transition hover:shadow-md">
            <div>
                @if($log->photo_path)
                    <img src="{{ Storage::url($log->photo_path) }}" class="mb-4 h-56 w-full rounded-lg object-cover" alt="Bukti {{ $log->user->name }}">
                @endif

                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-[#6d1a1a]">{{ $log->user->name }}</h2>
                        <p class="text-xs text-[#8d6e63]">
                            {{ $log->user->schoolClass?->name }} · {{ $log->date->format('d/m/Y') }} · {{ $log->schedule?->shift_label }}
                        </p>
                    </div>

                    <span class="inline-block rounded-full px-2.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wide {{ $log->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($log->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                        {{ strtoupper($log->status) }}
                    </span>
                </div>

                <p class="mt-3 text-sm text-[#4a1c1c]">
                    Jarak: <span class="font-semibold">{{ $log->distance_meters }} m</span>
                </p>
            </div>

            <div class="mt-4">
                @if($log->status === 'pending')
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <form method="POST" action="{{ route('verification.approve', $log) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-px hover:bg-emerald-700 sm:w-auto">
                                Approve
                            </button>
                        </form>

                        <form method="POST" action="{{ route('verification.reject', $log) }}" class="flex flex-1 gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="text" required maxlength="255" name="rejection_reason" placeholder="Alasan penolakan" class="w-full flex-1 rounded-lg border border-[#fce4c4] bg-white px-3 py-2 text-sm outline-none transition focus:border-[#6d1a1a] focus:ring-1 focus:ring-[#6d1a1a]">
                            <button type="submit" class="rounded-lg bg-[#c62828] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-px hover:bg-[#b71c1c]">
                                Reject
                            </button>
                        </form>
                    </div>
                @elseif($log->rejection_reason)
                    <p class="rounded-lg border border-red-200 bg-red-50 p-2.5 text-xs font-semibold text-[#c62828]">
                        <span class="font-bold">Alasan Penolakan:</span> {{ $log->rejection_reason }}
                    </p>
                @endif
            </div>
        </article>
    @empty
        <div class="col-span-full rounded-xl border border-dashed border-[#fce4c4] bg-white p-8 text-center text-sm text-[#8d6e63]">
            Tidak ada bukti yang menunggu verifikasi.
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $logs->links() }}
</div>
@endsection