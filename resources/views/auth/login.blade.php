<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#b91c1c">
    <title>Masuk | SI-PIKET</title>
    <link rel="icon" href="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" type="image/png">
    @vite('resources/css/app.css')
</head>
<body class="bg-[#fffaf0] text-[#351313]">
    <main class="relative min-h-screen overflow-hidden bg-[#fffaf0] p-3 sm:p-5 lg:p-7">
        <div class="pointer-events-none absolute -left-20 -top-20 h-72 w-72 rounded-full bg-[#facc15]/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -right-20 h-80 w-80 rounded-full bg-[#dc2626]/10 blur-3xl"></div>

        <div class="relative mx-auto grid min-h-[calc(100vh-1.5rem)] max-w-[1440px] overflow-hidden rounded-[28px] bg-white shadow-[0_24px_80px_rgba(127,29,29,.14)] sm:min-h-[calc(100vh-2.5rem)] lg:min-h-[calc(100vh-3.5rem)] lg:grid-cols-[1.08fr_.92fr] lg:rounded-[36px]">
            <section class="relative hidden overflow-hidden bg-[linear-gradient(145deg,#991b1b_0%,#c81e1e_54%,#e11d48_145%)] p-10 text-white lg:flex lg:flex-col xl:p-16">
                <div class="pointer-events-none absolute -right-28 -top-28 h-[430px] w-[430px] rounded-full border-[70px] border-[#facc15]/10"></div>
                <div class="pointer-events-none absolute -bottom-52 -left-40 h-[520px] w-[520px] rounded-full bg-[#7f1d1d]/45 ring-[55px] ring-white/[.035]"></div>
                <div class="pointer-events-none absolute right-[8%] top-[22%] h-36 w-36 bg-[radial-gradient(circle,#fde047_1.5px,transparent_2px)] bg-[length:16px_16px] opacity-35"></div>

                <a href="/" class="relative z-10 flex w-fit items-center gap-3" aria-label="SI-PIKET Beranda">
                    <span class="grid h-14 w-14 place-items-center rounded-2xl bg-white shadow-[0_8px_28px_rgba(69,10,10,.24)]">
                        <img src="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" alt="Logo SMAN 1 Tasikmalaya" class="h-11 w-auto object-contain">
                    </span>
                    <span>
                        <strong class="block text-lg font-extrabold tracking-[.08em]">SI-PIKET</strong>
                        <small class="mt-0.5 block text-[.68rem] font-medium tracking-[.08em] text-red-100">SISTEM INFORMASI PIKET</small>
                    </span>
                </a>

                <div class="relative z-10 my-auto max-w-[650px] py-16">
                    <span class="mb-6 inline-flex items-center gap-2 rounded-full border border-yellow-200/25 bg-yellow-300/10 px-4 py-2 text-[.68rem] font-extrabold tracking-[.18em] text-yellow-200">
                        <span class="h-2 w-2 rounded-full bg-yellow-300 shadow-[0_0_0_5px_rgba(253,224,71,.12)]"></span>
                        DISIPLIN DIMULAI DARI SINI
                    </span>
                    <h1 class="max-w-[620px] text-[clamp(3.2rem,5.5vw,6rem)] font-black leading-[.94] tracking-[-.065em]">
                        Hadir hari ini,
                        <span class="font-serif font-normal italic text-yellow-300">hebat</span> esok nanti.
                    </h1>
                    <p class="mt-7 max-w-[540px] text-[1.02rem] leading-8 text-red-50/80">
                        Kelola presensi, jadwal piket, dan laporan sekolah dalam satu ruang kerja yang cepat, rapi, dan transparan.
                    </p>
                </div>

                <div class="relative z-10 grid grid-cols-3 divide-x divide-white/15 border-t border-white/15 pt-6">
                    <div class="pr-5"><strong class="block text-2xl font-extrabold text-yellow-300">100%</strong><span class="mt-1 block text-xs text-red-100/75">Presensi digital</span></div>
                    <div class="px-5"><strong class="block text-2xl font-extrabold text-yellow-300">24/7</strong><span class="mt-1 block text-xs text-red-100/75">Akses sistem</span></div>
                    <div class="pl-5"><strong class="block text-2xl font-extrabold text-yellow-300">Real-time</strong><span class="mt-1 block text-xs text-red-100/75">Data laporan</span></div>
                </div>
            </section>

            <section class="relative flex items-center justify-center px-6 py-10 sm:px-12 lg:px-14 xl:px-20">
                <div class="absolute right-8 top-8 hidden items-center gap-2 text-xs font-semibold text-[#9f6767] sm:flex">
                    <span class="h-2 w-2 rounded-full bg-yellow-400"></span>Portal resmi sekolah
                </div>

                <div class="w-full max-w-[440px]">
                    <div class="mb-10 flex items-center gap-3 lg:hidden">
                        <span class="grid h-12 w-12 place-items-center rounded-xl bg-red-50 ring-1 ring-red-100">
                            <img src="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" alt="Logo SMAN 1 Tasikmalaya" class="h-10 w-auto object-contain">
                        </span>
                        <span><strong class="block font-extrabold tracking-[.06em] text-red-800">SI-PIKET</strong><small class="text-[.65rem] font-medium tracking-[.06em] text-[#a66a6a]">SISTEM INFORMASI PIKET</small></span>
                    </div>

                    <div class="mb-8">
                        <span class="mb-4 block text-[.7rem] font-black tracking-[.2em] text-red-700">SELAMAT DATANG</span>
                        <h2 class="text-4xl font-black tracking-[-.045em] text-[#3f1515] sm:text-[2.8rem]">Masuk ke akunmu</h2>
                        <p class="mt-3 leading-7 text-[#946767]">Gunakan akun yang telah diberikan oleh administrator sekolah.</p>
                    </div>

                    <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label for="email" class="mb-2 block text-sm font-bold text-[#522020]">Alamat email</label>
                            <div class="group flex min-h-14 items-center rounded-2xl border bg-white px-4 transition focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-100 {{ $errors->has('email') ? 'border-red-500' : 'border-[#eadada]' }}">
                                <svg class="mr-3 h-5 w-5 shrink-0 text-[#c28f8f] transition group-focus-within:text-red-600" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2Z"/><path d="m22 6-10 7L2 6"/></svg>
                                <input id="email" class="min-w-0 flex-1 border-0 bg-transparent py-4 text-sm text-[#3f1515] outline-none placeholder:text-[#c6a3a3]" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="nama@sekolah.id">
                            </div>
                            @error('email')
                                <p class="mt-2 flex items-center gap-1.5 text-xs font-medium text-red-600" role="alert"><span class="grid h-4 w-4 place-items-center rounded-full bg-red-100 text-[.65rem] font-black">!</span>{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="mb-2 flex items-center justify-between"><label for="password" class="text-sm font-bold text-[#522020]">Kata sandi</label><span class="text-xs font-semibold text-[#b17c7c]">Pastikan data benar</span></div>
                            <div class="group flex min-h-14 items-center rounded-2xl border border-[#eadada] bg-white px-4 transition focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-100">
                                <svg class="mr-3 h-5 w-5 shrink-0 text-[#c28f8f] transition group-focus-within:text-red-600" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                <input id="password" class="min-w-0 flex-1 border-0 bg-transparent py-4 text-sm text-[#3f1515] outline-none placeholder:text-[#c6a3a3]" name="password" type="password" required autocomplete="current-password" placeholder="Masukkan kata sandi">
                                <button id="password-toggle" class="ml-2 grid h-9 w-9 shrink-0 place-items-center rounded-lg text-[#b17c7c] transition hover:bg-red-50 hover:text-red-700" type="button" aria-label="Tampilkan kata sandi" aria-pressed="false">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>

                        <button class="group mt-2 flex min-h-14 w-full items-center justify-center gap-3 rounded-2xl border-0 bg-red-700 px-5 py-4 text-sm font-extrabold text-white shadow-[0_14px_30px_rgba(185,28,28,.24)] transition hover:-translate-y-0.5 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-200" type="submit">
                            Masuk ke dashboard <span class="grid h-7 w-7 place-items-center rounded-full bg-yellow-300 text-base text-red-900 transition group-hover:translate-x-1">→</span>
                        </button>
                    </form>

                    <div class="mt-9 flex items-center gap-4 text-[.7rem] font-semibold text-[#b28b8b] before:h-px before:flex-1 before:bg-[#f0dfdf] after:h-px after:flex-1 after:bg-[#f0dfdf]">BANTUAN</div>
                    <p class="mt-5 text-center text-xs leading-5 text-[#9e7272]">Kesulitan masuk? Hubungi <span class="font-bold text-red-700">administrator sekolah</span>.</p>
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