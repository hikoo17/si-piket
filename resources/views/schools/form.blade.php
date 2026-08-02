@extends('layouts.app')

@section('content')
    <h1 class="mb-5 text-3xl font-bold">{{ $school->exists ? 'Edit' : 'Tambah' }} Sekolah</h1>

    <form class="max-w-3xl space-y-5 rounded bg-white p-6" method="POST"
        action="{{ $school->exists ? route('schools.update', $school) : route('schools.store') }}">
        @csrf
        @if ($school->exists)
            @method('PUT')
        @endif

        <label class="block">
            Nama sekolah
            <input id="school-name" class="mt-1 w-full rounded border p-2" name="name" type="text"
                value="{{ old('name', $school->name) }}" required>
        </label>

        <div>
            <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <span class="font-medium">Posisi sekolah</span>
                    <p class="text-sm text-gray-500">Klik peta atau geser penanda ke lokasi sekolah.</p>
                </div>
                <button id="use-current-location" class="rounded border border-indigo-700 px-3 py-2 text-sm text-indigo-700"
                    type="button">Gunakan lokasi saya</button>
            </div>

            <div class="mb-2 flex gap-2">
                <input id="location-search" class="w-full rounded border p-2" type="search"
                    name="address" value="{{ old('address', $school->address) }}"
                    placeholder="Contoh: SMAN 1 Tasikmalaya" autocomplete="off">
                <button id="search-location" class="rounded border border-indigo-700 px-3 py-2 text-indigo-700"
                    type="button">Cari</button>
            </div>
            <div id="location-results" class="mb-2 space-y-1 text-sm"></div>

            <div id="school-location-map" class="h-96 w-full overflow-hidden rounded border"
                data-default-latitude="-7.32709600" data-default-longitude="108.22034900"
                data-location-catalog='@json($locationCatalog)'></div>
            <p id="location-status" class="mt-2 text-sm text-gray-500" aria-live="polite"></p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block">
                Latitude
                <input id="school-latitude" class="mt-1 w-full rounded border p-2" name="latitude" type="number"
                    min="-90" max="90" step="0.00000001" value="{{ old('latitude', $school->latitude) }}" required>
            </label>
            <label class="block">
                Longitude
                <input id="school-longitude" class="mt-1 w-full rounded border p-2" name="longitude" type="number"
                    min="-180" max="180" step="0.00000001" value="{{ old('longitude', $school->longitude) }}" required>
            </label>
        </div>

        <label class="block">
            Radius (meter)
            <input id="school-radius" class="mt-1 w-full rounded border p-2" name="radius_meters" type="number"
                min="10" max="1000" step="1" value="{{ old('radius_meters', $school->radius_meters) }}" required>
        </label>

        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block">
                Jam mulai
                <input class="mt-1 w-full rounded border p-2" name="upload_start_time" type="time"
                    value="{{ old('upload_start_time', $school->upload_start_time) }}" required>
            </label>
            <label class="block">
                Batas upload
                <input class="mt-1 w-full rounded border p-2" name="upload_deadline" type="time"
                    value="{{ old('upload_deadline', $school->upload_deadline) }}" required>
            </label>
        </div>

        <button class="rounded bg-indigo-700 px-4 py-2 text-white">Simpan</button>
    </form>
@endsection
