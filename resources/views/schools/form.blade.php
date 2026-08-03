@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'dashboard'],
    ['schedules.index', 'Jadwal Piket', 'clock'],
    ['piket.upload.form', 'Ambil Bukti', 'scan'],
    ['verification.index', 'Verifikasi', 'shield'],
    ['reports.index', 'Laporan', 'report'],
    ['schools.index', 'Sekolah', 'school'],
]])
@section('content')
<h1 class="mb-5 text-2xl font-bold text-[#6d1a1a]">{{ $school->exists ? 'Edit' : 'Tambah' }} Sekolah</h1>
<form class="max-w-3xl space-y-5 rounded-xl border border-[#fce4c4] bg-white p-6" method="POST" action="{{ $school->exists ? route('schools.update', $school) : route('schools.store') }}">
    @csrf
    @if ($school->exists)@method('PUT')@endif

    <label class="block">Nama sekolah
        <input id="school-name" class="mt-1 w-full rounded-lg border border-[#fce4c4] bg-white p-2.5 text-sm outline-none transition focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10" name="name" type="text" value="{{ old('name', $school->name) }}" required>
    </label>

    <div>
        <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
            <div>
                <span class="text-sm font-medium">Posisi sekolah</span>
                <p class="text-xs text-[#8d6e63]">Klik peta atau geser penanda ke lokasi sekolah.</p>
            </div>
            <button id="use-current-location" class="rounded-lg border border-[#6d1a1a] px-3 py-1.5 text-xs font-semibold text-[#6d1a1a] transition hover:bg-[#6d1a1a] hover:text-white" type="button">Gunakan lokasi saya</button>
        </div>

        <div class="mb-2 flex gap-2">
            <input id="location-search" class="w-full rounded-lg border border-[#fce4c4] bg-white p-2.5 text-sm outline-none transition focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10" type="search" name="address" value="{{ old('address', $school->address) }}" placeholder="Contoh: SMAN 1 Tasikmalaya" autocomplete="off">
            <button id="search-location" class="rounded-lg border border-[#6d1a1a] px-3 py-1.5 text-xs font-semibold text-[#6d1a1a] transition hover:bg-[#6d1a1a] hover:text-white" type="button">Cari</button>
        </div>
        <div id="location-results" class="mb-2 space-y-1 text-xs"></div>

        <div id="school-location-map" class="h-80 w-full overflow-hidden rounded-lg border border-[#fce4c4]" data-default-latitude="-7.32709600" data-default-longitude="108.22034900" data-location-catalog='@json($locationCatalog)'></div>
        <p id="location-status" class="mt-2 text-xs text-[#8d6e63]" aria-live="polite"></p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block text-xs font-semibold text-[#5d4037]">Latitude
            <input id="school-latitude" class="mt-1.5 w-full rounded-lg border border-[#fce4c4] bg-white p-2.5 text-sm outline-none transition focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10" name="latitude" type="number" min="-90" max="90" step="0.00000001" value="{{ old('latitude', $school->latitude) }}" required>
        </label>
        <label class="block text-xs font-semibold text-[#5d4037]">Longitude
            <input id="school-longitude" class="mt-1.5 w-full rounded-lg border border-[#fce4c4] bg-white p-2.5 text-sm outline-none transition focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10" name="longitude" type="number" min="-180" max="180" step="0.00000001" value="{{ old('longitude', $school->longitude) }}" required>
        </label>
    </div>

    <label class="block text-xs font-semibold text-[#5d4037]">Radius (meter)
        <input id="school-radius" class="mt-1.5 w-full rounded-lg border border-[#fce4c4] bg-white p-2.5 text-sm outline-none transition focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10" name="radius_meters" type="number" min="10" max="1000" step="1" value="{{ old('radius_meters', $school->radius_meters) }}" required>
    </label>

    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block text-xs font-semibold text-[#5d4037]">Jam mulai
            <input class="mt-1.5 w-full rounded-lg border border-[#fce4c4] bg-white p-2.5 text-sm outline-none transition focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10" name="upload_start_time" type="time" value="{{ old('upload_start_time', $school->upload_start_time ? substr($school->upload_start_time, 0, 5) : '') }}" required>
        </label>
        <label class="block text-xs font-semibold text-[#5d4037]">Batas upload
            <input class="mt-1.5 w-full rounded-lg border border-[#fce4c4] bg-white p-2.5 text-sm outline-none transition focus:border-[#6d1a1a] focus:ring-2 focus:ring-[#6d1a1a]/10" name="upload_deadline" type="time" value="{{ old('upload_deadline', $school->upload_deadline ? substr($school->upload_deadline, 0, 5) : '') }}" required>
        </label>
    </div>

    <button class="rounded bg-[#6d1a1a] px-4 py-2 font-semibold text-white">Simpan</button>
</form>
@endsection
