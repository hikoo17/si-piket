<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SI-PIKET | Presensi Harian</title>
    <link rel="icon" href="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" type="image/png">
    @vite('resources/css/app.css')
</head>
<body>
    <div class="flex min-h-screen flex-col">
        <header class="border-b border-[#fce4c4] bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" alt="Logo SMAN 1 Tasikmalaya" class="block h-[30px] w-auto">
                    <span>
                        <strong class="block text-[.95rem] font-bold tracking-wide text-[#6d1a1a]">SI-PIKET</strong>
                        <small class="block text-[.6rem] tracking-wider text-[#8c6d6d] uppercase">Presensi Harian</small>
                    </span>
                </a>
                @auth
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded border border-[#6d1a1a] px-4 py-1.5 text-xs font-semibold text-[#6d1a1a] transition hover:bg-[#6d1a1a] hover:text-white">Keluar</button></form>
                @endauth
            </div>
        </header>

        <main class="mx-auto flex w-full max-w-7xl flex-1 flex-col lg:flex-row">
            <div class="flex flex-1 flex-col justify-center p-8 lg:p-12">
                <div class="mb-8 inline-flex items-center gap-2 rounded-full border border-[#fce4c4] bg-white px-4 py-1.5">
                    <span class="h-2 w-2 rounded-full bg-[#fbc02d]"></span>
                    <span class="text-xs font-semibold tracking-wider text-[#6d1a1a] uppercase">Sistem Presensi</span>
                </div>

                <h1 class="mb-4 text-4xl font-bold leading-tight tracking-tight text-[#6d1a1a] lg:text-5xl">
                    Kelola presensi<br>dengan lebih mudah
                </h1>

                <p class="mb-8 max-w-lg text-[#8c6d6d]">
                    Pantau kehadiran siswa, verifikasi bukti piket, dan generate laporan dalam satu platform yang transparan.
                </p>

                <div class="flex flex-wrap gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#6d1a1a] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-px hover:bg-[#5a1515]">
                            <span>Buka Dashboard</span>
                            <x-icon name="heroicon-o-arrow-right" class="h-4 w-4" />
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#6d1a1a] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-px hover:bg-[#5a1515]">
                            <span>Masuk</span>
                            <x-icon name="heroicon-o-arrow-right" class="h-4 w-4" />
                        </a>
                    @endauth
                </div>

                <div class="mt-12 flex gap-8">
                    <div>
                        <p class="text-2xl font-bold text-[#6d1a1a]">100%</p>
                        <p class="text-xs text-[#8d6e63]">Presensi tercatat</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-[#6d1a1a]">24/7</p>
                        <p class="text-xs text-[#8d6e63]">Akses kapan saja</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-[#6d1a1a]">Real-time</p>
                        <p class="text-xs text-[#8d6e63]">Data terkini</p>
                    </div>
                </div>
            </div>

            <div class="hidden lg:flex w-full max-w-md flex-1 items-center justify-center border-l border-[#fce4c4] bg-white p-8">
                <div class="w-full max-w-sm space-y-4">
                    <div class="rounded-xl border border-[#fce4c4] bg-white p-5 shadow-sm">
                        <div class="mb-3 flex items-center justify-between">
                            <span class="text-xs font-semibold text-[#8d6e63]">STATUS HARI INI</span>
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[.65rem] font-bold text-emerald-700">AKTIF</span>
                        </div>
                        <p class="text-3xl font-bold text-[#6d1a1a]">Presensi Dibuka</p>
                        <p class="mt-1 text-xs text-[#8d6e63]">Upload bukti piket sekarang</p>
                    </div>

                    <div class="rounded-xl border border-[#fce4c4] bg-white p-5 shadow-sm">
                        <p class="text-xs font-semibold text-[#8d6e63]">CARA KERJA</p>
                        <ul class="mt-3 space-y-2.5">
                            <li class="flex items-start gap-2.5">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#fbc02d] text-[.65rem] font-extrabold text-white">1</span>
                                <span class="text-sm text-[#4a1c1c]">Ambil foto di lokasi sekolah</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#fbc02d] text-[.65rem] font-extrabold text-white">2</span>
                                <span class="text-sm text-[#4a1c1c]">Sistem verifikasi GPS otomatis</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#fbc02d] text-[.65rem] font-extrabold text-white">3</span>
                                <span class="text-sm text-[#4a1c1c]">Guru/Kesiswaan approve</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
