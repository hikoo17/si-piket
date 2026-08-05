@props(['title' => 'Dashboard'])

<style>
    /* 1. Scrollbar halus untuk navigasi sidebar */
    nav::-webkit-scrollbar {
        width: 6px;
    }
    nav::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 9999px;
    }
    nav::-webkit-scrollbar-thumb {
        background: rgba(120, 53, 15, 0.25); /* Warna amber-900 transparan */
        border-radius: 9999px;
    }
    nav::-webkit-scrollbar-thumb:hover {
        background: rgba(120, 53, 15, 0.45);
    }

    /* 2. Scrollbar global halaman Web (Opsional) */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-track {
        background: #f8fafc; /* bg-slate-50 */
    }
    ::-webkit-scrollbar-thumb {
        background: #f59e0b; /* amber-500 */
        border-radius: 9999px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #d97706; /* amber-600 */
    }
</style>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | SI-PIKET</title>
    <link rel="icon" href="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" type="image/png">
    @vite('resources/css/app.css')
</head>
<body class="m-0 bg-slate-50 font-sans text-slate-900 antialiased">
@php
    $iconMap = [
        'dashboard' => 'heroicon-o-squares-2x2',
        'clock' => 'heroicon-o-clock',
        'scan' => 'heroicon-o-qr-code',
        'shield' => 'heroicon-o-shield-check',
        'report' => 'heroicon-o-clipboard-document-list',
        'users' => 'heroicon-o-users',
        'list' => 'heroicon-o-queue-list',
        'grid' => 'heroicon-o-squares-2x2',
        'school' => 'heroicon-o-academic-cap',
        'chart' => 'heroicon-o-chart-bar',
        'heart' => 'heroicon-o-heart',
        'menu' => 'heroicon-o-bars-3',
        'logout' => 'heroicon-o-arrow-right-on-rectangle',
        'check' => 'heroicon-o-check',
        'alert' => 'heroicon-o-exclamation-circle',
        'mail' => 'heroicon-o-envelope',
        'eye' => 'heroicon-o-eye',
        'lock' => 'heroicon-o-lock-closed',
        'arrow-right' => 'heroicon-o-arrow-right',
        'close' => 'heroicon-o-x-mark',
        'success' => 'heroicon-o-check-circle',
        'warning' => 'heroicon-o-exclamation-triangle',
        'info' => 'heroicon-o-information-circle',
        'download' => 'heroicon-o-arrow-down-tray',
        'filter' => 'heroicon-o-funnel',
        'plus' => 'heroicon-o-plus',
        'note' => 'heroicon-o-document-text',
        'file' => 'heroicon-o-document',
        'bell' => 'heroicon-o-bell',
        'user' => 'heroicon-o-user',
        'letter' => 'heroicon-o-envelope',
        'tracking' => 'heroicon-o-map-pin',
    ];
@endphp

<div class="flex min-h-screen">
    <!-- SIDEBAR KUNING AMBER (Persis tema awal + spacing lega) -->
    <aside id="app-sidebar" class="fixed inset-y-0 left-0 z-30 flex w-72 flex-col justify-between overflow-hidden bg-gradient-to-b from-amber-500 to-yellow-500 px-4 py-6 text-amber-950 shadow-[10px_0_30px_rgba(180,83,9,0.12)] transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full" aria-label="Navigasi utama">
        
        <div class="relative z-10 space-y-6">
            <!-- Brand / Logo -->
            <div class="flex items-center justify-between px-2">
                <a class="flex items-center gap-3.5" href="{{ route('dashboard') }}">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-white shadow-md shadow-amber-900/10">
                        <img src="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" alt="Logo SMAN 1 Tasikmalaya" class="h-7 w-auto">
                    </span>
                    <div>
                        <strong class="block text-base font-bold tracking-tight text-amber-950">SI-PIKET</strong>
                        <small class="block text-[10px] font-extrabold uppercase tracking-widest text-amber-900/70">SMAN 1 Tasikmalaya</small>
                    </div>
                </a>
            </div>

            <!-- Navigation Menu -->
            <div class="space-y-1.5">
                <p class="px-3 text-[10px] font-extrabold uppercase tracking-widest text-amber-900/60">Menu Utama</p>
                
                @php
                    $navigation = [
                        ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2', ['admin', 'guru', 'km', 'siswa']],
                        ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock', ['admin', 'km']],
                        ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-camera', ['km', 'siswa']],
                        ['verification.index', 'Verifikasi', 'heroicon-o-shield-check', ['admin', 'guru', 'km']],
                        ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list', ['admin', 'guru', 'km']],
                        ['users.index', 'Pengguna', 'heroicon-o-users', ['admin']],
                        ['schools.index', 'Sekolah', 'heroicon-o-academic-cap', ['admin']],
                        ['classes.index', 'Kelas', 'heroicon-o-rectangle-group', ['admin']],
                    ];
                @endphp

                <nav class="space-y-1 overflow-y-auto max-h-[calc(100vh-320px)] pr-1">
                    @foreach ($navigation as $item)
                        @continue(! in_array(auth()->user()->role, $item[3], true))
                        @php $active = request()->routeIs($item[0].'*'); @endphp
                        
                        <a class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition-all {{ $active ? 'bg-white text-amber-950 shadow-md shadow-amber-900/10' : 'text-amber-950/80 hover:bg-white/20 hover:text-amber-950' }}" href="{{ route($item[0]) }}">
                            <div class="flex items-center gap-3">
                                <span class="grid h-7 w-7 place-items-center rounded-lg {{ $active ? 'bg-amber-100 text-amber-800' : 'bg-white/10 group-hover:bg-white/20' }}">
                                    <x-icon name="{{ $item[2] ?? 'heroicon-o-queue-list' }}" class="h-4 w-4" />
                                </span>
                                <span>{{ $item[1] }}</span>
                            </div>
                            @if($active)
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-600"></span>
                            @endif
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- Bottom Area / User Profile -->
        <div class="relative z-10 space-y-4 pt-4 border-t border-amber-900/10">
            <!-- Small Note Card -->
            <div class="rounded-xl border border-amber-900/10 bg-white/20 p-3.5 text-amber-950">
                <div class="flex items-center gap-2 text-amber-900 mb-1">
                    <x-icon name="heroicon-o-heart" class="h-4 w-4" />
                    <span class="text-[11px] font-bold">Catatan Kedisiplinan</span>
                </div>
                <p class="text-[11px] leading-relaxed text-amber-950/70">Kirimkan bukti piket secara jujur dan tepat waktu dari lokasi sekolah.</p>
            </div>

            <!-- Profile Info & Logout Button -->
            @auth
            <div class="flex items-center justify-between gap-3 px-1">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-white/80 text-sm font-extrabold text-amber-900 shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <strong class="block truncate text-xs font-bold text-amber-950">{{ auth()->user()->name }}</strong>
                        <span class="block text-[10px] uppercase tracking-wider font-semibold text-amber-900/70">{{ auth()->user()->role }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" data-confirm-logout>
                    @csrf
                    <button class="grid h-8 w-8 place-items-center rounded-lg text-amber-900 transition hover:bg-white/20" aria-label="Keluar" title="Keluar">
                        <x-icon name="heroicon-o-arrow-right-on-rectangle" class="h-4 w-4" />
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="lg:ml-72 min-w-0 flex-1">
        <!-- HEADER NAVBAR -->
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-200 bg-white/90 px-4 backdrop-blur-md sm:px-8">
            <div class="flex items-center gap-4">
                <button id="menu-button" class="lg:hidden p-2 rounded-lg text-amber-900 hover:bg-amber-100" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="app-sidebar">
                    <x-icon name="heroicon-o-bars-3" class="h-5 w-5" />
                </button>
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">SI-PIKET SYSTEM</span>
                    <strong class="text-sm font-bold text-slate-900">{{ $title }}</strong>
                </div>
            </div>

            <div class="flex items-center gap-4">
                    <span class="hidden rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 sm:inline-block">
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
                @auth
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-slate-900 text-xs font-bold text-white lg:hidden">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
                @endauth
            </div>
        </header>

        <!-- APP CONTENT -->
        <section class="app-content mx-auto min-h-[calc(100vh-4rem)] max-w-7xl p-4 sm:p-6 lg:p-8">
            @yield('content')
        </section>
    </main>
</div>

@vite('resources/js/app.js')

<div
    id="flash-message"
    class="hidden"
    data-success="{{ session('success') }}"
    data-error="{{ session('error') }}"
    data-validation="{{ $errors->any() ? implode('\n', $errors->all()) : '' }}"
></div>

<!-- Mobile Overlay Screen -->
<button id="sidebar-scrim" class="fixed inset-0 z-20 hidden border-0 bg-amber-950/40 backdrop-blur-xs lg:hidden" type="button" aria-label="Tutup menu"></button>

<script>
    const sidebar = document.getElementById('app-sidebar');
    const menuButton = document.getElementById('menu-button');
    const scrim = document.getElementById('sidebar-scrim');

    const setSidebar = (open) => {
        sidebar.classList.toggle('translate-x-0', open);
        sidebar.classList.toggle('-translate-x-full', !open);
        scrim.classList.toggle('hidden', !open);
        menuButton?.setAttribute('aria-expanded', String(open));
    };

    menuButton?.addEventListener('click', () => setSidebar(true));
    scrim?.addEventListener('click', () => setSidebar(false));
</script>
</body>
</html>
