<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>SI-PIKET</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
<nav class="bg-indigo-950 text-white"><div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4"><a href="{{ route('dashboard') }}" class="text-xl font-bold">SI-PIKET</a>@auth<form method="POST" action="{{ route('logout') }}">@csrf<button>Keluar</button></form>@endauth</div></nav>
<main class="mx-auto max-w-7xl px-6 py-8">
@if(session('success'))<div class="mb-4 rounded bg-emerald-100 p-3 text-emerald-800">{{ session('success') }}</div>@endif
@if(session('error'))<div class="mb-4 rounded bg-red-100 p-3 text-red-800">{{ session('error') }}</div>@endif
@if($errors->any())<div class="mb-4 rounded bg-red-100 p-3 text-red-800">{{ $errors->first() }}</div>@endif
@yield('content')
</main></body></html>
