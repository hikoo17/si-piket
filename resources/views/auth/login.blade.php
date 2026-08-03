<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#eab308">
    <title>Masuk | SI-PIKET</title>
    <link rel="icon" href="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" type="image/png">
    @vite('resources/css/app.css')
</head>
<body class="bg-amber-50 font-sans text-amber-950 antialiased overflow-hidden h-screen">
    <main class="relative h-screen w-screen overflow-hidden bg-amber-50/50 p-2 sm:p-4 lg:p-6 flex items-center justify-center">
        <!-- Glow Ornaments -->
        <div class="pointer-events-none absolute -left-20 -top-20 h-72 w-72 rounded-full bg-yellow-300/30 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -right-20 h-80 w-80 rounded-full bg-amber-400/20 blur-3xl"></div>

        <!-- Main Card Container (Full Screen Fit) -->
        <div class="relative w-full h-full max-w-[1400px] overflow-hidden rounded-2xl sm:rounded-3xl bg-white shadow-2xl shadow-amber-900/10 grid lg:grid-cols-[1.1fr_.9fr]">
            
            <!-- SECTION KIRI: Visual & Branding (Dominan Kuning Warm & Putih) -->
            <section class="relative hidden overflow-hidden bg-gradient-to-br from-amber-400 via-yellow-400 to-amber-500 p-8 xl:p-12 text-amber-950 lg:flex lg:flex-col justify-between">
                <!-- Decorative Circles -->
                <div class="pointer-events-none absolute -right-20 -top-20 h-[380px] w-[380px] rounded-full border-[50px] border-white/20"></div>
                <div class="pointer-events-none absolute -bottom-32 -left-32 h-[450px] w-[450px] rounded-full bg-white/10 ring-40 ring-white/10"></div>
                
                <!-- Logo Header -->
                <a href="/" class="relative z-10 flex w-fit items-center gap-3" aria-label="SI-PIKET Beranda">
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-white shadow-md">
                        <img src="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" alt="Logo SMAN 1 Tasikmalaya" class="h-9 w-auto object-contain">
                    </span>
                    <span>
                        <strong class="block text-lg font-black tracking-wider text-amber-950">SI-PIKET</strong>
                        <small class="block text-[0.65rem] font-bold tracking-widest text-amber-900/80">SISTEM INFORMASI PIKET</small>
                    </span>
                </a>

                <!-- Hero Content -->
                <div class="relative z-10 my-auto py-6">
                    <!-- Mengganti aksen merah menjadi putih -->
                    <span class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/90 backdrop-blur-md px-3.5 py-1.5 text-[0.7rem] font-black tracking-widest text-amber-950 shadow-sm">
                        <span class="h-2 w-2 rounded-full bg-amber-600 animate-pulse"></span>
                        DISIPLIN DIMULAI DARI SINI
                    </span>
                    <h1 class="text-4xl xl:text-5xl font-black leading-tight tracking-tight text-amber-950">
                        Hadir hari ini,<br>
                        <!-- Tekan serasi dengan warna putih -->
                        <span class="bg-white/90 px-2 py-0.5 rounded-lg text-amber-950 font-serif italic shadow-sm">hebat</span> esok nanti.
                    </h1>
                    <p class="mt-4 max-w-md text-sm xl:text-base leading-relaxed text-amber-950/80 font-medium">
                        Kelola presensi, jadwal piket, dan laporan sekolah dalam satu ruang kerja yang cepat, rapi, dan transparan.
                    </p>
                </div>

                <!-- Footer Stats -->
                <div class="relative z-10 grid grid-cols-3 divide-x divide-amber-950/10 border-t border-amber-950/10 pt-4">
                    <div class="pr-3"><strong class="block text-xl font-black text-amber-950">100%</strong><span class="block text-[0.7rem] font-bold text-amber-900/70">Presensi digital</span></div>
                    <div class="px-3"><strong class="block text-xl font-black text-amber-950">24/7</strong><span class="block text-[0.7rem] font-bold text-amber-900/70">Akses sistem</span></div>
                    <div class="pl-3"><strong class="block text-xl font-black text-amber-950">Real-time</strong><span class="block text-[0.7rem] font-bold text-amber-900/70">Data laporan</span></div>
                </div>
            </section>

            <!-- SECTION KANAN: Form Login -->
            <section class="relative flex flex-col justify-between overflow-y-auto p-6 sm:p-10 xl:p-12 bg-white">
                
                <!-- Badge Atas -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5 lg:hidden">
                        <span class="grid h-10 w-10 place-items-center rounded-lg bg-amber-100 ring-1 ring-amber-300">
                            <img src="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" alt="Logo SMAN 1 Tasikmalaya" class="h-8 w-auto object-contain">
                        </span>
                        <span>
                            <strong class="block text-sm font-extrabold text-amber-950">SI-PIKET</strong>
                            <small class="text-[0.6rem] font-semibold text-amber-700">SMAN 1 TASIKMALAYA</small>
                        </span>
                    </div>
                    <div class="ml-auto hidden sm:flex items-center gap-2 text-xs font-bold text-amber-700/80 bg-amber-50 px-3 py-1 rounded-full border border-amber-200/60">
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>Portal resmi sekolah
                    </div>
                </div>

                <!-- Container Utama Form -->
                <div class="my-auto w-full max-w-sm mx-auto py-2">
                    <div class="mb-6">
                        <!-- Mengubah warna teks dari merah ke amber -->
                        <span class="block text-[0.65rem] font-black tracking-widest text-amber-600 uppercase">Selamat Datang</span>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Masuk ke akunmu</h2>
                        <p class="mt-1.5 text-xs sm:text-sm text-slate-500 font-medium">Gunakan akun yang telah diberikan oleh administrator sekolah.</p>
                    </div>

                    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                        @csrf
                        <!-- Input Email -->
                        <div>
                            <label for="email" class="mb-1.5 block text-xs font-bold text-slate-700">Alamat Email</label>
                            <!-- Mengganti ring/border focus dari merah ke amber -->
                            <div class="group flex items-center rounded-xl border bg-amber-50/30 px-3.5 transition focus-within:bg-white focus-within:border-amber-500 focus-within:ring-2 focus-within:ring-amber-100 {{ $errors->has('email') ? 'border-amber-500' : 'border-slate-200' }}">
                                <x-icon name="heroicons-o-envelope" class="mr-2.5 h-4 w-4 shrink-0 text-slate-400 transition group-focus-within:text-amber-500" />
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
                            <!-- Mengganti focus style merah menjadi amber -->
                            <div class="group flex items-center rounded-xl border border-slate-200 bg-amber-50/30 px-3.5 transition focus-within:bg-white focus-within:border-amber-500 focus-within:ring-2 focus-within:ring-amber-100">
                                <x-icon name="heroicons-o-lock-closed" class="mr-2.5 h-4 w-4 shrink-0 text-slate-400 transition group-focus-within:text-amber-500" />
                                <input id="password" class="w-full border-0 bg-transparent py-3 text-xs sm:text-sm text-slate-900 outline-none placeholder:text-slate-400 font-medium" name="password" type="password" required autocomplete="current-password" placeholder="Masukkan kata sandi">
                                <button id="password-toggle" class="ml-1.5 grid h-7 w-7 shrink-0 place-items-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition" type="button" aria-label="Tampilkan kata sandi">
                                    <x-icon name="heroicons-o-eye" class="h-4 w-4" />
                                </button>
                            </div>
                        </div>

                        <!-- Tombol Submit (Mengganti Merah ke Amber/Kuning Warm dengan Ikon Putih) -->
                        <button class="group mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-3 text-xs sm:text-sm font-black text-amber-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400 hover:shadow-amber-500/30 active:scale-[0.99]" type="submit">
                            Masuk ke dashboard 
                            <span class="grid h-5 w-5 place-items-center rounded-full bg-white text-amber-950 font-bold transition group-hover:translate-x-1">→</span>
                        </button>
                    </form>

                    <!-- Divider & Help -->
                    <div class="mt-6 flex items-center gap-3 text-[0.65rem] font-bold text-slate-400 before:h-px before:flex-1 before:bg-slate-100 after:h-px after:flex-1 after:bg-slate-100 uppercase tracking-widest">
                        Bantuan
                    </div>
                    <p class="mt-3 text-center text-xs leading-normal text-slate-500 font-medium">
                        Kesulitan masuk? Hubungi <span class="font-bold text-amber-700">administrator sekolah</span>.
                    </p>
                </div>

                <!-- Footer Copyright -->
                <div class="text-center text-[0.65rem] font-medium text-slate-400 pt-2">
                    &copy; {{ date('Y') }} SMAN 1 Tasikmalaya. All rights reserved.
                </div>
            </section>

        </div>
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