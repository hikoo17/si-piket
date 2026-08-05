@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
]])

@section('content')
<div class="mx-auto max-w-4xl px-4 py-6 text-left">
    <!-- Header -->
    <div class="mb-6 border-b border-slate-100 pb-4">
        <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">Ambil Bukti Piket</h1>
        <p class="mt-1 text-sm text-slate-500">Pilih jadwal, ambil foto presensi, dan kirimkan lokasi terkini Anda.</p>
    </div>

    <!-- Alert Notifikasi & Validasi -->
    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <ul class="list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($schedules->isEmpty())
        <div class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50/50 p-4 text-left text-sm text-amber-800">
            <svg class="h-5 w-5 shrink-0 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <span>Kamu tidak memiliki jadwal piket hari ini.</span>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 md:grid-cols-12">

            <!-- Left Side: Viewport Kamera & Preview -->
            <div class="md:col-span-7">
                <div class="relative overflow-hidden rounded-2xl bg-slate-900 shadow-sm ring-1 ring-slate-900/10">
                    <div class="aspect-[4/3] w-full overflow-hidden">
                        <video id="camera" autoplay playsinline class="h-full w-full object-cover"></video>
                        <img id="preview" class="{{ old('photo') ? '' : 'hidden' }} h-full w-full object-cover" src="{{ old('photo') }}" alt="Pratinjau bukti">
                    </div>

                    <!-- Floating Badge Status Kamera -->
                    <div class="absolute left-3 top-3">
                        <span id="cam-badge" class="inline-flex items-center gap-1.5 rounded-full bg-slate-900/60 px-3 py-1 text-xs font-medium text-white backdrop-blur-md">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Kamera Aktif
                        </span>
                    </div>

                    <canvas id="canvas" class="hidden"></canvas>
                </div>
            </div>

            <!-- Right Side: Control & Form -->
            <div class="flex flex-col justify-start md:col-span-5 text-left">
                <form id="upload-form" method="POST" action="{{ route('piket.upload') }}" class="space-y-4">
                    @csrf

                    <!-- Select Schedule Card -->
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <label for="schedule_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Jadwal Hari Ini
                        </label>
                        <select name="schedule_id" id="schedule_id" class="mt-2 w-full rounded-lg border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 transition focus:border-[#6d1a1a] focus:bg-white focus:ring-[#6d1a1a]" required>
                            @foreach($schedules as $schedule)
                                <option value="{{ $schedule->id }}" @selected(old('schedule_id') == $schedule->id)>
                                    {{ $schedule->shift_label }} · {{ $schedule->shift === 'afternoon' ? substr($school->return_upload_start_time, 0, 5).'–'.substr($school->return_upload_deadline, 0, 5) : substr($school->upload_start_time, 0, 5).'–'.substr($school->upload_deadline, 0, 5) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="photo" id="photo" value="{{ old('photo') }}">
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                    <input type="hidden" name="accuracy" id="accuracy" value="{{ old('accuracy') }}">

                    <!-- Actions Buttons -->
                    <div class="space-y-2 pt-1">
                         <button type="button" id="capture" class="inline-flex w-full items-center justify-start gap-3 rounded-xl bg-[#6d1a1a] px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-[#581515] focus:outline-none focus:ring-2 focus:ring-[#6d1a1a]/20 active:scale-[0.99]">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span id="capture-text">{{ old('photo') ? 'Foto Ulang' : 'Ambil Foto' }}</span>
                         </button>

                         <button type="button" id="switch-camera" class="inline-flex w-full items-center justify-start gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400/20">
                             <span>Ganti Kamera Depan/Belakang</span>
                         </button>

                        <button type="submit" id="submit" @if(!old('photo')) disabled @endif class="inline-flex w-full items-center justify-start gap-3 rounded-xl bg-amber-400 px-4 py-3 text-sm font-semibold text-amber-950 shadow-sm transition hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-400/20 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 disabled:shadow-none">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span>Kirim Bukti</span>
                        </button>
                    </div>

                    <!-- Status Alert -->
                    <div id="status-container" class="hidden rounded-xl border border-slate-200 bg-slate-50 p-3.5 text-left text-xs font-medium text-slate-600 transition" aria-live="polite">
                        <p id="status"></p>
                    </div>
                </form>
            </div>

        </div>
    @endif
</div>

@if($schedules->isNotEmpty())
<script>
    const video = document.querySelector('#camera'),
          canvas = document.querySelector('#canvas'),
          photo = document.querySelector('#photo'),
          preview = document.querySelector('#preview'),
          capture = document.querySelector('#capture'),
          captureText = document.querySelector('#capture-text'),
          submit = document.querySelector('#submit'),
          status = document.querySelector('#status'),
          statusContainer = document.querySelector('#status-container'),
          camBadge = document.querySelector('#cam-badge'),
          switchCamera = document.querySelector('#switch-camera');

    let cameraStream, cameraDevices = [], cameraIndex = 0, firstCameraStart = true;

    function showStatus(msg) {
        statusContainer.classList.remove('hidden');
        status.textContent = msg;
    }

    async function startCamera() {
        cameraStream?.getTracks().forEach(track => track.stop());
        if (!navigator.mediaDevices?.getUserMedia) throw new Error('unsupported');
        cameraDevices = (await navigator.mediaDevices.enumerateDevices()).filter(device => device.kind === 'videoinput');
        if (firstCameraStart && cameraDevices.length > 1) {
            const rearIndex = cameraDevices.findIndex(device => /back|rear|environment|belakang/i.test(device.label));
            if (rearIndex >= 0) cameraIndex = rearIndex;
            firstCameraStart = false;
        }
        const device = cameraDevices[cameraIndex];
        let stream;
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: device ? { deviceId: { exact: device.deviceId }, width: { ideal: 1280 }, height: { ideal: 1280 } } : { facingMode: { ideal: 'environment' } }, audio: false });
        } catch (error) {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { exact: 'environment' } }, audio: false });
        }
        cameraStream = stream;
        video.srcObject = stream;
        await video.play();
        camBadge.innerHTML = `<span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span> Kamera ${cameraIndex + 1}`;
    }

    startCamera().catch(() => {
        showStatus('Kamera tidak dapat diakses. Pastikan izin kamera aktif dan menggunakan HTTPS.');
        camBadge.innerHTML = `<span class="h-2 w-2 rounded-full bg-red-500"></span> Kamera Off`;
    });

    switchCamera.addEventListener('click', async () => {
        cameraIndex = cameraDevices.length > 1 ? (cameraIndex + 1) % cameraDevices.length : cameraIndex;
        try {
            await startCamera();
            showStatus(`Kamera ${cameraIndex + 1} aktif.`);
        } catch {
            showStatus('Kamera tidak dapat diganti. Pastikan izin kamera aktif.');
        }
    });

    if (photo.value) {
        video.classList.add('hidden');
        preview.classList.remove('hidden');
    }

    capture.addEventListener('click', () => {
        if (!video.videoWidth) {
            showStatus('Kamera belum siap. Tunggu sebentar lalu coba lagi.');
            return;
        }

        const isRetake = !preview.classList.contains('hidden');

        if (isRetake) {
            preview.classList.add('hidden');
            video.classList.remove('hidden');
            captureText.textContent = 'Ambil Foto';
            submit.disabled = true;
            photo.value = '';
            camBadge.innerHTML = `<span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span> Kamera Aktif`;
            showStatus('Kamera aktif kembali.');
        } else {
            const scale = Math.min(1, 1600 / video.videoWidth);
            canvas.width = Math.round(video.videoWidth * scale);
            canvas.height = Math.round(video.videoHeight * scale);
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

            const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
            photo.value = dataUrl;
            preview.src = dataUrl;

            video.classList.add('hidden');
            preview.classList.remove('hidden');
            submit.disabled = false;
            captureText.textContent = 'Foto Ulang';
            camBadge.innerHTML = `<span class="h-2 w-2 rounded-full bg-blue-400"></span> Hasil Foto`;
            showStatus('Foto berhasil diambil.');
        }
    });

    document.querySelector('#upload-form').addEventListener('submit', e => {
        if (!photo.value) {
            e.preventDefault();
            showStatus('Foto belum tersedia. Ambil foto terlebih dahulu.');
            return;
        }
        e.preventDefault();

        if (!navigator.geolocation) {
            showStatus('Browser ini tidak mendukung GPS.');
            return;
        }

        submit.disabled = true;
        capture.disabled = true;
        let bestPosition = null, finished = false, watchId, timer;

        showStatus('Meminta izin dan mencari lokasi presisi...');

        const finish = () => {
            if (finished) return;
            finished = true;
            if (watchId !== undefined) navigator.geolocation.clearWatch(watchId);
            clearTimeout(timer);

            if (!bestPosition) {
                submit.disabled = false;
                capture.disabled = false;
                showStatus('Lokasi gagal didapatkan. Aktifkan GPS & izinkan lokasi.');
                return;
            }

            const { coords } = bestPosition;
            document.querySelector('#latitude').value = coords.latitude;
            document.querySelector('#longitude').value = coords.longitude;
            document.querySelector('#accuracy').value = coords.accuracy;

            showStatus(`Lokasi dikunci (±${Math.round(coords.accuracy)}m). Mengirim data...`);
            cameraStream?.getTracks().forEach(track => track.stop());

            if (!photo.value) {
                submit.disabled = false;
                capture.disabled = false;
                showStatus('Foto tidak tersedia. Ambil foto ulang sebelum mengirim.');
                return;
            }

            e.target.submit();
        };

        watchId = navigator.geolocation.watchPosition(
            pos => {
                if (!bestPosition || pos.coords.accuracy < bestPosition.coords.accuracy) {
                    bestPosition = pos;
                }
                const accuracy = Math.round(bestPosition.coords.accuracy);
                showStatus(accuracy <= 150
                    ? `Lokasi presisi (±${accuracy}m). Menyiapkan pengiriman...`
                    : `Meningkatkan akurasi lokasi... (saat ini ±${accuracy}m)`);

                if (bestPosition.coords.accuracy <= 150) finish();
            },
            error => {
                if (finished) return;
                finished = true;
                clearTimeout(timer);
                if (watchId !== undefined) navigator.geolocation.clearWatch(watchId);
                submit.disabled = false;
                capture.disabled = false;
                const message = error.code === 1
                    ? 'Izin lokasi ditolak. Harap izinkan akses lokasi di browser.'
                    : 'Lokasi gagal didapatkan. Aktifkan GPS, izinkan lokasi, lalu coba lagi.';
                showStatus(message);
            },
            { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
        );

        timer = setTimeout(finish, 10000);
    });
</script>
@endif
@endsection