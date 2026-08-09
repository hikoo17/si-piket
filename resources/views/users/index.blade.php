@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
    ['users.index', 'Manajemen Pengguna', 'heroicon-o-users'],
]])

@section('content')
<div class="space-y-5">
    <!-- Header Section -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Staf & Pengguna</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola akun, peran, dan hak akses pengguna aplikasi.</p>
        </div>
        <a href="{{ route('users.create') }}" 
           class="inline-flex h-9 shrink-0 items-center justify-center gap-1.5 rounded-lg bg-indigo-600 px-3.5 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none">
            <x-icon name="heroicon-o-user-plus" class="h-4 w-4" />
            <span>Tambah Pengguna</span>
        </a>
    </div>

    <!-- Filter Panel -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('users.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_auto_auto] lg:items-end">
            <div>
                <label for="search" class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Cari</label>
                <input id="search" name="search" value="{{ request('search') }}" placeholder="Nama atau email..." class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs font-medium text-slate-800 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label for="role" class="mb-1 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">Peran</label>
                <select id="role" name="role" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs font-medium text-slate-800 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <option value="">Semua Peran</option>
                    <option value="admin" @selected($roleFilter === 'admin')>Administrator</option>
                    <option value="guru_piket" @selected($roleFilter === 'guru_piket')>Guru Piket</option>
                    <option value="wali_kelas" @selected($roleFilter === 'wali_kelas')>Wali Kelas</option>
                    <option value="km" @selected($roleFilter === 'km')>Ketua Kelas</option>
                    <option value="siswa" @selected($roleFilter === 'siswa')>Siswa</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex h-9 flex-1 items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 text-xs font-bold text-white transition hover:bg-slate-800">
                    <x-icon name="heroicon-o-magnifying-glass" class="h-4 w-4" />
                    <span>Tampilkan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Card list (mobile) -->
    <div class="space-y-3 sm:hidden">
        @forelse($users as $user)
            @php
                $roleClass = match(strtolower($user->role)) {
                    'admin' => 'bg-purple-50 text-purple-700 border-purple-200/80',
                    'guru_piket', 'wali_kelas', 'teacher' => 'bg-blue-50 text-blue-700 border-blue-200/80',
                    'siswa', 'student' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                    default => 'bg-slate-100 text-slate-600 border-slate-200',
                };
            @endphp
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 font-bold text-slate-600 border border-slate-200">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-bold text-slate-900">{{ $user->name }}</div>
                        <div class="truncate text-[0.7rem] text-slate-500">{{ $user->email }}</div>
                    </div>
                    <span class="inline-flex shrink-0 items-center rounded-full border px-2.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wider {{ $roleClass }}">
                        {{ $user->role }}
                    </span>
                </div>
                <div class="mt-3 flex items-center gap-1.5 text-xs text-slate-500">
                    <x-icon name="heroicon-o-academic-cap" class="h-4 w-4 text-slate-400" />
                    <span>{{ $user->schoolClass?->name ?? '-' }}</span>
                </div>
                <div class="mt-3 flex gap-2 border-t border-slate-100 pt-3">
                    <a href="{{ route('users.edit', $user) }}" class="inline-flex h-8 flex-1 items-center justify-center gap-1 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-indigo-600">
                        <x-icon name="heroicon-o-pencil-square" class="h-3.5 w-3.5 text-slate-400" />
                        <span>Edit</span>
                    </a>
                    <button type="button" onclick="openDeleteModal('{{ route('users.destroy', $user) }}', '{{ addslashes($user->name) }}')" class="inline-flex h-8 flex-1 items-center justify-center gap-1 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-rose-600 transition hover:border-rose-200 hover:bg-rose-50">
                        <x-icon name="heroicon-o-trash" class="h-3.5 w-3.5" />
                        <span>Hapus</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-slate-200 bg-white p-10 text-center">
                <div class="flex flex-col items-center justify-center">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-slate-100 text-slate-400">
                        <x-icon name="heroicon-o-users" class="h-5 w-5" />
                    </span>
                    <h3 class="mt-2 text-xs font-bold text-slate-800">Belum ada data pengguna</h3>
                    <p class="text-[0.7rem] text-slate-500 mt-0.5">Tambahkan pengguna baru untuk mulai mengelola akses.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Users Table (desktop) -->
    <div class="hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm sm:block">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="border-b border-slate-200 bg-slate-50/80 text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th scope="col" class="px-4 py-3">Pengguna</th>
                        <th scope="col" class="px-4 py-3">Peran (Role)</th>
                        <th scope="col" class="px-4 py-3">Kelas</th>
                        <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="transition hover:bg-slate-50/60">
                            <!-- User Detail -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 font-bold text-slate-600 border border-slate-200">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $user->name }}</div>
                                        <div class="text-[0.7rem] text-slate-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role Badge -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $roleClass = match(strtolower($user->role)) {
                                        'admin' => 'bg-purple-50 text-purple-700 border-purple-200/80',
                                        'guru_piket', 'wali_kelas', 'teacher' => 'bg-blue-50 text-blue-700 border-blue-200/80',
                                        'siswa', 'student' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                        default => 'bg-slate-100 text-slate-600 border-slate-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[0.65rem] font-bold uppercase tracking-wider {{ $roleClass }}">
                                    {{ $user->role }}
                                </span>
                            </td>

                            <!-- Class -->
                            <td class="px-4 py-3 whitespace-nowrap text-slate-700 font-medium">
                                <div class="flex items-center gap-1.5">
                                    <x-icon name="heroicon-o-academic-cap" class="h-4 w-4 text-slate-400" />
                                    <span>{{ $user->schoolClass?->name ?? '-' }}</span>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-2">
                                    <!-- Edit Link -->
                                    <a href="{{ route('users.edit', $user) }}" 
                                       class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-indigo-600 focus:outline-none">
                                        <x-icon name="heroicon-o-pencil-square" class="h-3.5 w-3.5 text-slate-400" />
                                        <span>Edit</span>
                                    </a>

                                    <!-- Delete Trigger -->
                                    <button type="button" 
                                            onclick="openDeleteModal('{{ route('users.destroy', $user) }}', '{{ addslashes($user->name) }}')"
                                            class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 hover:border-rose-200 focus:outline-none">
                                        <x-icon name="heroicon-o-trash" class="h-3.5 w-3.5" />
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-slate-100 text-slate-400">
                                        <x-icon name="heroicon-o-users" class="h-5 w-5" />
                                    </span>
                                    <h3 class="mt-2 text-xs font-bold text-slate-800">Belum ada data pengguna</h3>
                                    <p class="text-[0.7rem] text-slate-500 mt-0.5">Tambahkan pengguna baru untuk mulai mengelola akses.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Footer -->
    @if($users->hasPages())
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-2.5">
            {{ $users->links() }}
        </div>
    @endif
</div>

<!-- Native Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
    <div class="w-full max-w-sm rounded-xl border border-slate-200 bg-white p-5 shadow-xl space-y-4">
        <div class="flex items-center gap-3 text-rose-600">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-rose-50 text-rose-600">
                <x-icon name="heroicon-o-exclamation-triangle" class="h-5 w-5" />
            </span>
            <div>
                <h3 class="text-sm font-bold text-slate-900">Hapus Pengguna</h3>
                <p class="text-[0.7rem] text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>

        <p class="text-xs text-slate-600">
            Apakah Anda yakin ingin menghapus akun <strong id="deleteUserName" class="text-slate-900"></strong>?
        </p>

        <form id="deleteForm" method="POST" action="" class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
            @csrf
            @method('DELETE')
            
            <button type="button" 
                    onclick="closeDeleteModal()" 
                    class="h-9 rounded-lg border border-slate-200 bg-white px-4 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                Batal
            </button>
            <button type="submit" 
                    class="h-9 rounded-lg bg-rose-600 px-4 text-xs font-bold text-white transition hover:bg-rose-700 shadow-sm">
                Ya, Hapus
            </button>
        </form>
    </div>
</div>

<script>
    function openDeleteModal(deleteUrl, userName) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        const nameEl = document.getElementById('deleteUserName');

        form.action = deleteUrl;
        nameEl.textContent = userName;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // Close on backdrop click & ESC
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeleteModal();
    });
</script>
@endsection