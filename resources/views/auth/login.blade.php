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
<body class="bg-slate-900 font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <!-- Card Login Putih (max-w-sm), Border Abu-abu, Shadow Tipis -->
    <main class="w-full max-w-sm bg-white rounded-xl p-6 sm:p-7 shadow-sm border border-slate-200">
        
        <!-- Header Logo & Title (Logo Diperbesar) -->
        <div class="text-center mb-6">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-xl bg-amber-50 border border-amber-200/60 mb-3">
                <img src="{{ asset('Logo_SMAN_1_Tasikmalaya.png') }}" alt="Logo SMAN 1 Tasikmalaya" class="h-11 w-auto object-contain">
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">SI-PIKET</h1>
            <p class="text-xs text-slate-500 mt-1">Masukkan email dan kata sandi Anda</p>
        </div>

        <!-- Form Login -->
        <form id="login-form" method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf

            <!-- Input Email -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                <input id="email" 
                       name="email" 
                       type="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="email" 
                       placeholder="nama@sekolah.id"
                       class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 outline-none transition focus:border-amber-500 focus:ring-1 focus:ring-amber-500 placeholder:text-slate-400">
                @error('email')
                    <p class="mt-1 text-[0.7rem] font-semibold text-red-600" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <!-- Input Password -->
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi</label>
                <div class="relative flex items-center">
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           autocomplete="current-password" 
                           placeholder="••••••••"
                           class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 pr-10 text-xs sm:text-sm text-slate-900 outline-none transition focus:border-amber-500 focus:ring-1 focus:ring-amber-500 placeholder:text-slate-400">
                    
                    <button id="password-toggle" type="button" class="absolute right-3 text-slate-400 hover:text-amber-600 transition" aria-label="Tampilkan kata sandi">
                        <x-icon name="heroicon-o-eye" class="h-4 w-4" />
                    </button>
                </div>
            </div>

            <!-- Lupa Password Link -->
            <div class="text-right">
                <a href="#" class="text-xs font-semibold text-slate-600 hover:text-amber-700 hover:underline">Lupa kata sandi?</a>
            </div>

            <!-- Tombol Submit (Font Medium & Arrow Shift Hover) -->
            <button id="login-submit" type="submit" class="group w-full flex items-center justify-center gap-2 rounded-lg bg-amber-500 hover:bg-amber-600 py-3 text-xs sm:text-sm font-medium text-amber-950 transition active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-70 disabled:active:scale-100">
                <i data-lucide="loader-circle" class="hidden h-4 w-4 animate-spin" aria-hidden="true"></i>
                <span data-submit-label>Masuk</span>
                <span data-submit-arrow class="text-sm transition-transform duration-200 group-hover:translate-x-1">→</span>
            </button>
        </form>

        <!-- Divider Bantuan -->
        <div class="relative my-5 flex items-center justify-center">
            <div class="w-full border-t border-slate-200"></div>
            <span class="absolute bg-white px-2.5 text-[0.65rem] font-semibold uppercase tracking-wider text-slate-400">atau</span>
        </div>

        <!-- Catatan Bantuan & Footer -->
        <div class="text-center text-xs text-slate-500 space-y-2.5">
            <p>Kendala masuk? Hubungi <span class="font-bold text-amber-800 underline">Admin</span></p>
            <p class="text-[0.65rem] text-slate-400 pt-2 border-t border-slate-100">
                &copy; {{ date('Y') }} SMAN 1 Tasikmalaya
            </p>
        </div>

    </main>

    @vite('resources/js/login.js')
</body>
</html>
