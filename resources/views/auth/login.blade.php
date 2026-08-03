<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#f59e0b">
    <title>Masuk | SI-PIKET</title>
    <link rel="icon" href="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" type="image/png">
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-900 font-sans text-amber-950 antialiased overflow-hidden h-screen w-screen">
    <main class="relative h-screen w-screen overflow-hidden grid lg:grid-cols-[1.1fr_.9fr]">

        <!-- SECTION KIRI: Visual & Branding (Desktop View Only) -->
        <section class="relative hidden overflow-hidden bg-gradient-to-br from-amber-400 via-yellow-400 to-amber-500 p-8 xl:p-14 text-amber-950 lg:flex lg:flex-col justify-between">
            <!-- Decorative Circles & Glow -->
            <div class="pointer-events-none absolute -right-20 -top-20 h-[400px] w-[400px] rounded-full border-[50px] border-white/20"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-32 h-[500px] w-[500px] rounded-full bg-white/10 ring-40 ring-white/10"></div>

            <!-- Logo Header -->
            <a href="/" class="relative z-10 flex w-fit items-center gap-3.5" aria-label="SI-PIKET Beranda">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white shadow-xl shadow-amber-950/10">
                    <img src="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" alt="Logo SMAN 1 Tasikmalaya" class="h-9 w-auto object-contain">
                </span>
                <span>
                    <strong class="block text-xl font-black tracking-wider text-amber-950 leading-tight">SI-PIKET</strong>
                    <small class="block text-[0.65rem] font-extrabold tracking-widest text-amber-900/80">SISTEM INFORMASI PIKET</small>
                </span>
            </a>

            <!-- Hero Content -->
            <div class="relative z-10 my-auto py-6">
                <h1 class="text-4xl xl:text-5xl font-black leading-tight tracking-tight text-amber-950">
                    Hadir hari ini,<br>
                    <span class="bg-white/90 px-3 py-0.5 rounded-xl text-amber-950 font-serif italic shadow-sm">hebat</span> esok nanti.
                </h1>
                <p class="mt-4 max-w-md text-sm xl:text-base leading-relaxed text-amber-950/80 font-medium">
                    Kelola presensi, jadwal piket, dan laporan sekolah dalam satu ruang kerja yang cepat, rapi, dan transparan.
                </p>
            </div>

            <!-- Footer Stats -->
            <div class="relative z-10 grid grid-cols-3 divide-x divide-amber-950/10 border-t border-amber-950/10 pt-5">
                <div class="pr-3"><strong class="block text-2xl font-black text-amber-950">100%</strong><span class="block text-[0.7rem] font-bold text-amber-900/80">Presensi digital</span></div>
                <div class="px-3"><strong class="block text-2xl font-black text-amber-950">24/7</strong><span class="block text-[0.7rem] font-bold text-amber-900/80">Akses sistem</span></div>
                <div class="pl-3"><strong class="block text-2xl font-black text-amber-950">Real-time</strong><span class="block text-[0.7rem] font-bold text-amber-900/80">Data laporan</span></div>
            </div>
        </section>

        <!-- SECTION KANAN: Form Login (Full Height & Enhanced Mobile Aesthetics) -->
        <section class="relative flex flex-col justify-between overflow-y-auto bg-gradient-to-b from-amber-50 via-orange-50/40 to-white p-6 sm:p-10 xl:p-14">
            
            <!-- Mobile Background Ornaments (Aksen Warna di Mobile) -->
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-amber-300/40 blur-3xl lg:hidden"></div>
            <div class="pointer-events-none absolute -bottom-20 -left-16 h-72 w-72 rounded-full bg-orange-300/30 blur-3xl lg:hidden"></div>

            <!-- Header Badge & Mobile Logo -->
            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center gap-3 lg:hidden">
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-white shadow-md shadow-amber-900/10 ring-1 ring-amber-200">
                        <img src="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" alt="Logo SMAN 1 Tasikmalaya" class="h-8 w-auto object-contain">
                    </span>
                    <span>
                        <strong class="block text-base font-black text-amber-950 leading-none">SI-PIKET</strong>
                        <small class="text-[0.65rem] font-bold tracking-wider text-amber-800">SMAN 1 TASIKMALAYA</small>
                    </span>
                </div>
            </div>

            <!-- Container Utama Form -->
            <div class="relative z-10 my-auto w-full max-w-sm mx-auto py-6">
                <div class="mb-7">
                    <span class="inline-block rounded-md bg-amber-100 px-2.5 py-1 text-[0.65rem] font-black tracking-widest text-amber-800 uppercase mb-2">Selamat Datang</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Masuk ke akunmu</h2>
                    <p class="mt-1.5 text-xs sm:text-sm text-slate-600 font-medium leading-relaxed">Gunakan akun yang telah diberikan oleh administrator sekolah.</p>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                    @csrf
                    <!-- Input Email -->
                    <div>
                        <label for="email" class="mb-1.5 block text-xs font-bold text-slate-700">Alamat Email</label>
                        <div class="group flex items-center rounded-xl border border-amber-200/80 bg-white/80 backdrop-blur-sm px-3.5 shadow-sm transition focus-within:bg-white focus-within:border-amber-500 focus-within:ring-4 focus-within:ring-amber-500/15 {{ $errors->has('email') ? 'border-amber-500 ring-2 ring-amber-500/20' : '' }}">
                            <x-icon name="heroicon-o-envelope" class="mr-2.5 h-4 w-4 shrink-0 text-slate-400 transition group-focus-within:text-amber-500" />
                            <input id="email" class="w-full border-0 bg-transparent py-3 text-xs sm:text-sm text-slate-900 outline-none placeholder:text-slate-400 font-medium" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="nama@sekolah.id">
                        </div>
                        @error('email')
                            <p class="mt-1.5 flex items-center gap-1 text-[0.7rem] font-semibold text-amber-700" role="alert"><span class="grid h-3.5 w-3.5 place-items-center rounded-full bg-amber-100 text-[0.6rem]">!</span>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Input Password -->
                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label for="password" class="text-xs font-bold text-slate-700">Kata Sandi</label>
                            <span class="text-[0.65rem] font-semibold text-slate-400">Pastikan data benar</span>
                        </div>
                        <div class="group flex items-center rounded-xl border border-amber-200/80 bg-white/80 backdrop-blur-sm px-3.5 shadow-sm transition focus-within:bg-white focus-within:border-amber-500 focus-within:ring-4 focus-within:ring-amber-500/15">
                            <x-icon name="heroicon-o-lock-closed" class="mr-2.5 h-4 w-4 shrink-0 text-slate-400 transition group-focus-within:text-amber-500" />
                            <input id="password" class="w-full border-0 bg-transparent py-3 text-xs sm:text-sm text-slate-900 outline-none placeholder:text-slate-400 font-medium" name="password" type="password" required autocomplete="current-password" placeholder="Masukkan kata sandi">
                            <button id="password-toggle" class="ml-1.5 grid h-7 w-7 shrink-0 place-items-center rounded-lg text-slate-400 hover:bg-amber-100 hover:text-amber-800 transition" type="button" aria-label="Tampilkan kata sandi">
                                <x-icon name="heroicon-o-eye" class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <button class="group mt-3 flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-3.5 text-xs sm:text-sm font-black text-amber-950 shadow-lg shadow-amber-500/30 transition hover:from-amber-400 hover:to-amber-500 active:scale-[0.99]" type="submit">
                        Masuk ke dashboard
                        <span class="grid h-5 w-5 place-items-center rounded-full bg-amber-950/10 text-amber-950 font-bold transition group-hover:translate-x-1">→</span>
                    </button>
                </form>

                <!-- Divider & Help -->
                <div class="mt-8 flex items-center gap-3 text-[0.65rem] font-bold text-slate-400 before:h-px before:flex-1 before:bg-amber-200/60 after:h-px after:flex-1 after:bg-amber-200/60 uppercase tracking-widest">
                    Bantuan
                </div>
                <p class="mt-3 text-center text-xs leading-normal text-slate-600 font-medium">
                    Kesulitan masuk? Hubungi <span class="font-bold text-amber-800 underline decoration-amber-300 underline-offset-2">administrator sekolah</span>.
                </p>
            </div>

            <!-- Footer Copyright -->
            <div class="relative z-10 text-center text-[0.65rem] font-bold text-slate-400 pt-2">
                &copy; {{ date('Y') }} SMAN 1 Tasikmalaya. All rights reserved.
            </div>
        </section>

    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('password-toggle');
        passwordToggle.addEventListener('click', () => {
            const isVisible = passwordInput.type === 'text';
            passwordInput.type = isVisible ? 'password' : 'text';
            passwordToggle.setAttribute('aria-pressed', String(!isVisible));
            passwordToggle.setAttribute('aria-label', isVisible ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi');
        });
    </script>
</body>
</html>
