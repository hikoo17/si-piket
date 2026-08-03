@props(['title' => 'Dashboard', 'navigation' => []])

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
<body class="m-0 bg-[#f6f2eb] font-sans text-[#321919] antialiased">
@include('partials.icons')

<div class="flex min-h-screen">
    <aside id="app-sidebar" class="fixed inset-y-0 left-0 z-30 flex w-[276px] flex-col overflow-hidden bg-[#551414] px-4 py-5 text-[#fff7ed] shadow-[20px_0_60px_#35101024] transition-transform duration-200 before:absolute before:-right-24 before:-top-20 before:h-64 before:w-64 before:rounded-full before:bg-[#8f2c25]/35 before:blur-3xl max-[1050px]:-translate-x-full" aria-label="Navigasi utama">
        <div class="flex items-center justify-between">
            <a class="relative flex items-center gap-3 px-2 py-1" href="{{ route('dashboard') }}">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white shadow-lg shadow-black/10"><img src="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" alt="Logo SMAN 1 Tasikmalaya" class="block h-8 w-auto"></span>
                <span>
                    <strong class="text-[1.1rem] font-bold">SI-PIKET</strong>
                    <small class="mt-0.5 block text-[.62rem] uppercase tracking-[.16em] text-[#e8b98f]">Ruang Kedisiplinan</small>
                </span>
            </a>
        </div>

        <p class="relative mb-3 ml-3 mt-9 text-[.6rem] font-extrabold tracking-[.2em] text-[#dca67d]">RUANG KERJA</p>
        <nav class="grid gap-1">
            @foreach ($navigation as $item)
                <a class="group relative flex min-h-[48px] items-center gap-3 overflow-hidden rounded-xl px-3 py-3 text-[.8rem] font-semibold text-[#edd6c6] transition hover:bg-white/[.07] hover:text-white {{ request()->routeIs($item[0].'*') ? 'bg-[#fff8ed] text-[#551414] shadow-lg shadow-black/10' : '' }}" href="{{ route($item[0]) }}">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg {{ request()->routeIs($item[0].'*') ? 'bg-[#f3dfc5]' : 'bg-white/[.06] group-hover:bg-white/[.1]' }}"><svg>
                        <use href="#icon-{{ $item[2] ?? 'list' }}"></use>
                    </svg></span>
                    <span>{{ $item[1] }}</span>
                    <i class="ml-auto h-1.5 w-1.5 rounded-full {{ request()->routeIs($item[0].'*') ? 'bg-[#c88b24]' : 'bg-transparent' }}"></i>
                </a>
            @endforeach
        </nav>

        <div class="relative mt-auto mb-4 overflow-hidden rounded-2xl border border-white/10 bg-white/[.06] p-4 text-[#f0d6c0]">
            <span class="mb-3 grid h-9 w-9 place-items-center rounded-xl bg-[#e7ae48] text-[#551414]"><svg>
                <use href="#icon-heart"></use>
            </svg></span>
            <div>
                <strong class="block text-sm text-white">Kebiasaan baik, tiap hari.</strong>
                <span class="mt-1 block text-[.68rem] leading-relaxed text-[#d9bba7]">Catat piket dengan jujur, rapi, dan tepat waktu.</span>
            </div>
        </div>

        @auth
        <div class="relative flex items-center gap-3 border-t border-white/10 px-1 pt-4">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#e7ae48] font-extrabold text-[#551414]">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            <span class="min-w-0">
                <strong class="block truncate text-xs text-white">{{ auth()->user()->name }}</strong>
                <small class="mt-0.5 block capitalize text-[#d5ad91]">{{ auth()->user()->role }}</small>
            </span>
            <form method="POST" action="{{ route('logout') }}" class="ml-auto">
                @csrf
                <button class="grid place-items-center border-0 bg-transparent text-[#e6b98a]" aria-label="Keluar">
                    <svg>
                        <use href="#icon-logout"></use>
                    </svg>
                </button>
            </form>
        </div>
        @endauth
    </aside>

    <main class="ml-[276px] min-w-0 w-[calc(100%-276px)] max-[1050px]:ml-0 max-[1050px]:w-full">
        <header class="sticky top-0 z-10 flex h-[74px] items-center justify-between border-b border-[#e7ddd1] bg-[#f6f2eb]/90 px-[clamp(1.25rem,3.5vw,3.8rem)] backdrop-blur-xl max-[760px]:h-[65px] max-[760px]:px-4">
            <div class="flex items-center gap-5">
                <button id="menu-button" class="hidden border-0 bg-transparent max-[1050px]:grid" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="app-sidebar">
                    <svg>
                        <use href="#icon-menu"></use>
                    </svg>
                </button>
                <div>
                    <span class="block text-[.58rem] font-bold uppercase tracking-[.18em] text-[#a27f6d]">SI-PIKET</span>
                    <strong class="text-sm text-[#321919]">{{ $title }}</strong>
                </div>
            </div>
            <div class="flex items-center gap-5">
                <span class="rounded-full border border-[#ded1c3] bg-white/70 px-4 py-2 text-[.68rem] font-semibold text-[#795f52] max-[760px]:hidden">{{ now()->translatedFormat('l, d F Y') }}</span>
                @auth
                <span class="hidden h-[34px] w-[34px] place-items-center rounded-full bg-[#fbc02d] text-[.75rem] font-extrabold text-white max-[1050px]:grid">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                @endauth
            </div>
        </header>

        <section class="mx-auto min-h-[calc(100vh-74px)] max-w-[1540px] p-[clamp(1.5rem,3.5vw,3.8rem)] max-[760px]:px-4 max-[760px]:py-6">
            @if (session('success'))
                <div class="flex items-center gap-[.7rem] mb-[1.2rem] rounded-[11px] border border-[#ffe0b2] bg-[#fff8e1] p-[1rem] text-[.8rem] font-[750] text-[#5d4037]">
                    <svg>
                        <use href="#icon-check"></use>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="flex items-center gap-[.7rem] mb-[1.2rem] rounded-[11px] border border-[#ffcdd2] bg-[#ffebee] p-[1rem] text-[.8rem] font-[750] text-[#b71c1c]">
                    <svg>
                        <use href="#icon-alert"></use>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('content')
        </section>
    </main>
</div>
<button id="sidebar-scrim" class="fixed inset-0 z-20 hidden border-0 bg-[#4a1c1c6e]" type="button" aria-label="Tutup menu"></button>
<script>
    const sidebar = document.getElementById('app-sidebar');
    const menuButton = document.getElementById('menu-button');
    const scrim = document.getElementById('sidebar-scrim');
    const setSidebar = (open) => {
        sidebar.classList.toggle('translate-x-0', open);
        sidebar.classList.toggle('max-[1050px]:-translate-x-full', !open);
        scrim.classList.toggle('hidden', !open);
        menuButton.setAttribute('aria-expanded', String(open));
    };
    menuButton?.addEventListener('click', () => setSidebar(true));
    scrim?.addEventListener('click', () => setSidebar(false));
</script>
</body>
</html>
