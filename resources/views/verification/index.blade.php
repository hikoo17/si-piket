@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
]])

@section('content')
<div class="space-y-5">
    <!-- Header Section -->
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Verifikasi Bukti Piket</h1>
            <p class="text-xs text-slate-500 mt-0.5">Tinjau foto dan lokasi sebelum menyetujui atau menolak laporan piket.</p>
        </div>
    </div>

    <!-- Cards Grid Section -->
    <div class="grid gap-4 md:grid-cols-2">
        @forelse($logs as $log)
            <article class="flex flex-col justify-between overflow-hidden rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-slate-300">
                <div class="space-y-3">
                    <!-- Photo Preview -->
                    @if($log->photo_path)
                        <div class="relative overflow-hidden rounded-lg border border-slate-100 bg-slate-50">
                            <img src="{{ Storage::url($log->photo_path) }}" class="h-48 w-full object-cover transition duration-300 hover:scale-105" alt="Bukti {{ $log->user->name }}">
                        </div>
                    @endif

                    <!-- Header Info & Badge -->
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">{{ $log->user->name ?? '-' }}</h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $log->user->schoolClass?->name ?? '-' }} · {{ $log->date ? $log->date->locale('id')->translatedFormat('j F Y') : '-' }} · {{ $log->schedule?->shift_label ?? '-' }}
                            </p>
                        </div>

                        @php
                            $statusClasses = [
                                'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                'pending'  => 'bg-amber-50 text-amber-700 border-amber-200/80',
                                'rejected' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                            ];
                            $statusIcons = [
                                'approved' => 'heroicon-o-check-circle',
                                'pending'  => 'heroicon-o-clock',
                                'rejected' => 'heroicon-o-x-circle',
                            ];
                        @endphp

                        <span class="inline-flex shrink-0 items-center gap-1 rounded-full border px-2.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wider {{ $statusClasses[$log->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                            <x-icon name="{{ $statusIcons[$log->status] ?? 'heroicon-o-question-mark-circle' }}" class="h-3.5 w-3.5" />
                            {{ $log->status }}
                        </span>
                    </div>

                    @if(filled($log->description))
                        <div class="flex items-start gap-2 rounded-lg bg-slate-50 p-2.5 text-xs text-slate-600">
                            <x-icon name="heroicon-o-document-text" class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
                            <p class="whitespace-pre-line leading-relaxed">{{ $log->description }}</p>
                        </div>
                    @endif

                    <!-- Details Stats -->
                    <div class="flex items-center gap-4 text-xs text-slate-600 pt-2 border-t border-slate-100">
                        <div class="flex items-center gap-1.5">
                            <x-icon name="heroicon-o-map-pin" class="h-4 w-4 text-slate-400" />
                            <span>Jarak: <strong class="{{ $log->distance_meters > 50 ? 'text-amber-600' : 'text-slate-800' }}">{{ $log->distance_meters ?? 0 }} m</strong></span>
                        </div>
                        @if($log->accuracy_meters)
                            <div class="flex items-center gap-1.5">
                                <x-icon name="heroicon-o-signal" class="h-4 w-4 text-slate-400" />
                                <span>Akurasi: <strong class="text-slate-800">{{ $log->accuracy_meters }} m</strong></span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Section -->
                <div class="mt-4 pt-3 border-t border-slate-100">
                    @if($log->status === 'pending')
                        <div class="grid grid-cols-2 gap-2">
                            <!-- Reject Button -->
                            <button type="button" 
                                    onclick="openRejectModal('{{ $log->id }}', '{{ addslashes($log->user->name ?? 'Siswa') }}')"
                                    class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 hover:border-rose-200 focus:outline-none">
                                <x-icon name="heroicon-o-x-mark" class="h-4 w-4" />
                                <span>Tolak</span>
                            </button>

                            <!-- Approve Action -->
                            <form method="POST" action="{{ route('verification.approve', $log) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="inline-flex h-9 w-full items-center justify-center gap-1.5 rounded-lg bg-emerald-600 px-3 text-xs font-bold text-white transition hover:bg-emerald-700 shadow-sm focus:outline-none">
                                    <x-icon name="heroicon-o-check" class="h-4 w-4" />
                                    <span>Setujui</span>
                                </button>
                            </form>
                        </div>
                    @elseif($log->rejection_reason)
                        <div class="flex items-start gap-2 rounded-lg border border-rose-200/80 bg-rose-50/60 p-2.5 text-xs text-rose-700">
                            <x-icon name="heroicon-o-exclamation-triangle" class="h-4 w-4 shrink-0 text-rose-500 mt-0.5" />
                            <div>
                                <span class="font-bold">Alasan Penolakan:</span> {{ $log->rejection_reason }}
                            </div>
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-xl border border-slate-200 bg-white p-10 text-center">
                <div class="flex flex-col items-center justify-center">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-slate-100 text-slate-400">
                        <x-icon name="heroicon-o-shield-check" class="h-5 w-5" />
                    </span>
                    <h3 class="mt-2 text-xs font-bold text-slate-800">Tidak ada bukti yang menunggu verifikasi</h3>
                    <p class="text-[0.7rem] text-slate-500 mt-0.5">Semua bukti piket yang masuk telah disetujui atau ditolak.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination Footer -->
    @if($logs->hasPages())
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-2.5">
            {{ $logs->links() }}
        </div>
    @endif
</div>

<!-- Native Overlay Modal Container -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2 text-rose-600">
                <x-icon name="heroicon-o-x-circle" class="h-5 w-5" />
                <h3 class="text-sm font-bold text-slate-900">Tolak Bukti Piket</h3>
            </div>
            <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                <x-icon name="heroicon-o-x-mark" class="h-5 w-5" />
            </button>
        </div>

        <p class="text-xs text-slate-600">
            Anda akan menolak bukti piket dari <strong id="modalUserName" class="text-slate-900"></strong>. Silakan berikan alasan penolakan di bawah ini.
        </p>

        <form id="rejectForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Alasan Penolakan</label>
                <textarea name="rejection_reason" 
                          required 
                          rows="3" 
                          maxlength="255" 
                          placeholder="Contoh: Foto buram, lokasi tidak sesuai, atau area piket belum bersih." 
                          class="w-full rounded-lg border border-slate-200 bg-slate-50/50 p-3 text-xs font-medium text-slate-800 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" 
                        onclick="closeRejectModal()" 
                        class="h-9 rounded-lg border border-slate-200 bg-white px-4 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" 
                        class="h-9 rounded-lg bg-rose-600 px-4 text-xs font-bold text-white transition hover:bg-rose-700 shadow-sm">
                    Konfirmasi Penolakan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(logId, userName) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        const nameEl = document.getElementById('modalUserName');

        // Set action URL & name dynamically
        form.action = `{{ url('verification') }}/${logId}/reject`;
        nameEl.textContent = userName;

        // Show modal (flex layout)
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // Close on backdrop click
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });

    // Close on ESC key press
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRejectModal();
        }
    });
</script>
@endsection
