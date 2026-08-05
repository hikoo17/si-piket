@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicon-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicon-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicon-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicon-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicon-o-clipboard-document-list'],
]])

@section('content')
<div class="max-w-4xl space-y-5 text-left">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-4 text-left">
        <h1 class="text-xl font-bold tracking-tight text-slate-900 mt-0.5">Ambil Bukti Piket</h1>
        <p class="text-xs text-slate-500 mt-0.5">Pilih jadwal, ambil foto bukti presensi, dan kirimkan lokasi terkini Anda.</p>
    </div>

    @if($schedules->isEmpty())
        <!-- Empty State ketika tidak ada jadwal -->
        <div class="flex items-center gap-3 rounded-xl border border-amber-200/80 bg-amber-50/50 p-4 text-left text-xs font-medium text-amber-900">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-amber-100 text-amber-700">
                <x-icon name="heroicon-o-exclamation-triangle" class="h-4 w-4" />
            </span>
            <div class="text-left">
                <p class="font-bold text-amber-950">Tidak Ada Jadwal Hari Ini</p>
                <p class="text-amber-800/90 text-[0.7rem] mt-0.5">Anda tidak memiliki jadwal piket yang terdaftar untuk hari ini.</p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 md:grid-cols-12 text-left">

            <!-- Left Side: Viewport Kamera & Preview -->
            <div class="md:col-span-7 space-y-3 text-left">
                <div class="relative overflow-hidden rounded-xl bg-slate-950 border border-slate-800">
                    <div class="aspect-[4/3] w-full overflow-hidden relative flex items-center justify-start">
                        <video id="camera" autoplay playsinline class="h-full w-full object-cover"></video>
                        <img id="preview" class="{{ old('photo') ? '' : 'hidden' }} h-full w-full object-cover" src="{{ old('photo') }}" alt="Pratinjau bukti">
                        
                        <!-- Camera Viewfinder Overlay -->
                        <div class="pointer-events-none absolute inset-4 border border-white/20 rounded-lg flex flex-col justify-between p-2">
                            <div class="flex justify-between">
                                <div class="w-3 h-3 border-t-2 border-l-2 border-white/60"></div>
                                <div class="w-3 h-3 border-t-2 border-r-2 border-white/60"></div>
                            </div>
                            <div class="flex justify-between">
                                <div class="w-3 h-3 border-b-2 border-l-2 border-white/60"></div>
                                <div class="w-3 h-3 border-b-2 border-r-2 border-white/60"></div>
                            </div>
                        </div>

                        <!-- Floating Overlay Loading & Status (Di Atas Video) -->
                        <div id="video-overlay-loading" class="hidden absolute inset-0 z-20 flex flex-col items-start justify-end bg-slate-950/60 p-4 backdrop-blur-xs transition">
                            <div class="flex items-center gap-2.5 rounded-lg bg-slate-900/90 border border-white/10 px-3 py-2 text-left shadow-lg">
                                <span id="overlay-spinner" class="h-4 w-4 animate-spin rounded-full border-2 border-slate-400 border-t-amber-500 shrink-0"></span>
                                <p id="overlay-status-text" class="text-xs font-semibold text-white leading-tight">Memproses...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Badge Status Kamera (Kiri Atas) -->
                    <div class="absolute left-3 top-3 z-10">
                        <span id="cam-badge" class="inline-flex items-center gap-1.5 rounded-full bg-slate-900/80 px-2.5 py-1 text-[0.65rem] font-bold text-white backdrop-blur-md border border-white/10">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Kamera Aktif
                        </span>
                    </div>

                    <!-- Floating Switch Camera Button (Kanan Atas) -->
                    <div class="absolute right-3 top-3 z-10">
                        <button type="button" id="switch-camera" title="Ganti Kamera" class="grid h-8 w-8 place-items-center rounded-full bg-slate-900/80 text-white backdrop-blur-md border border-white/10 transition hover:bg-slate-800 active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed">
                            <x-icon name="heroicon-o-arrow-path" class="h-4 w-4" />
                        </button>
                    </div>

                    <canvas id="canvas" class="hidden"></canvas>
                </div>

                <!-- Helper Tip -->
                <p class="text-[0.68rem] text-slate-500 flex items-center gap-1 text-left">
                    <x-icon name="heroicon-o-information-circle" class="h-3.5 w-3.5 text-slate-400 shrink-0" />
                    <span>Pastikan wajah dan area piket terlihat jelas sebelum memotret.</span>
                </p>
            </div>

            <!-- Right Side: Control & Form -->
            <div class="flex flex-col justify-start md:col-span-5 text-left space-y-3">
                <form id="upload-form" method="POST" action="{{ route('piket.upload') }}" class="space-y-3 text-left">
                    @csrf

                    <!-- Select Schedule Card -->
                    <div class="rounded-xl border border-slate-200 bg-white p-3.5 text-left">
                        <label for="schedule_id" class="block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500 mb-1.5 text-left">
                            Pilih Jadwal Piket
                        </label>
                        <select name="schedule_id" id="schedule_id" class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 text-xs font-medium text-slate-800 transition focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500 text-left" required>
                            @foreach($schedules as $schedule)
                                <option value="{{ $schedule->id }}" @selected(old('schedule_id') == $schedule->id)>
                                    {{ $schedule->shift_label }} ({{ $schedule->shift === 'afternoon' ? substr($school->return_upload_start_time, 0, 5).'–'.substr($school->return_upload_deadline, 0, 5) : substr($school->upload_start_time, 0, 5).'–'.substr($school->upload_deadline, 0, 5) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-3.5 text-left">
                        <label for="description" class="mb-1.5 block text-[0.68rem] font-bold uppercase tracking-wider text-slate-500">
                            Deskripsi <span class="font-medium normal-case text-slate-400">(opsional)</span>
                        </label>
                        <textarea name="description" id="description" rows="3" maxlength="500" placeholder="Tambahkan catatan tentang kondisi atau kegiatan piket..." class="w-full resize-none rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs text-slate-800 transition placeholder:text-slate-400 focus:border-amber-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500">{{ old('description') }}</textarea>
                    </div>

                    <input type="hidden" name="photo" id="photo" value="{{ old('photo') }}">
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                    <input type="hidden" name="accuracy" id="accuracy" value="{{ old('accuracy') }}">

                    <!-- Actions Buttons -->
                    <div class="space-y-2 text-left">
                        <!-- Capture Button -->
                        <button type="button" id="capture" class="h-10 w-full inline-flex items-center justify-center gap-2 rounded-lg bg-amber-500 px-4 text-xs font-bold text-white transition hover:bg-amber-600 focus:outline-none active:scale-[0.99]">
                            <x-icon name="heroicon-o-camera" class="h-4 w-4" />
                            <span id="capture-text">{{ old('photo') ? 'Foto Ulang' : 'Ambil Foto' }}</span>
                        </button>

                        <!-- Submit Button -->
                        <button type="submit" id="submit-button" @if(!old('photo')) disabled @endif class="h-10 w-full inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 text-xs font-bold text-white transition hover:bg-emerald-700 focus:outline-none disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                            <x-icon name="heroicon-o-paper-airplane" class="h-4 w-4" />
                            <span>Kirim Bukti Presensi</span>
                        </button>
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
          submit = document.querySelector('#submit-button'),
          camBadge = document.querySelector('#cam-badge'),
          switchCamera = document.querySelector('#switch-camera'),
          videoOverlay = document.querySelector('#video-overlay-loading'),
          overlayStatusText = document.querySelector('#overlay-status-text'),
          overlaySpinner = document.querySelector('#overlay-spinner');

    let cameraStream, cameraDevices = [], cameraIndex = 0, cameraStarting = false;

    function showStatus(msg, isLoading = false) {
        if (!msg) {
            videoOverlay.classList.add('hidden');
            return;
        }

        videoOverlay.classList.remove('hidden');
        overlayStatusText.textContent = msg;

        if (isLoading) {
            overlaySpinner.classList.remove('hidden');
        } else {
            overlaySpinner.classList.add('hidden');
        }
    }

    function cameraErrorMessage(error) {
        if (!window.isSecureContext) {
            return 'Kamera butuh akses HTTPS.';
        }

        if (!navigator.mediaDevices?.getUserMedia) {
            return 'Browser tidak mendukung kamera.';
        }

        if (error?.name === 'NotAllowedError' || error?.name === 'SecurityError') {
            return 'Izin kamera ditolak. Izinkan akses di browser.';
        }

        if (error?.name === 'NotFoundError' || error?.name === 'DevicesNotFoundError') {
            return 'Kamera tidak ditemukan.';
        }

        if (error?.name === 'NotReadableError' || error?.name === 'TrackStartError') {
            return 'Kamera sedang dipakai aplikasi lain.';
        }

        return 'Gagal membuka kamera.';
    }

    async function startCamera(deviceId = null) {
        if (cameraStarting) return;
        cameraStarting = true;
        cameraStream?.getTracks().forEach(track => track.stop());

        try {
            if (!window.isSecureContext || !navigator.mediaDevices?.getUserMedia) {
                throw new DOMException('Camera API unavailable', 'SecurityError');
            }

            const videoConstraints = deviceId
                ? { deviceId: { exact: deviceId }, width: { ideal: 1280 }, height: { ideal: 1280 } }
                : { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 1280 } };

            cameraStream = await navigator.mediaDevices.getUserMedia({ video: videoConstraints, audio: false });
            video.srcObject = cameraStream;
            await video.play();

            cameraDevices = (await navigator.mediaDevices.enumerateDevices()).filter(device => device.kind === 'videoinput');
            const activeDeviceId = cameraStream.getVideoTracks()[0]?.getSettings().deviceId;
            const activeIndex = cameraDevices.findIndex(device => device.deviceId === activeDeviceId);
            if (activeIndex >= 0) cameraIndex = activeIndex;

            switchCamera.disabled = cameraDevices.length < 2;
            camBadge.innerHTML = `<span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span> Kamera ${cameraIndex + 1}`;
            showStatus(null);
        } finally {
            cameraStarting = false;
        }
    }

    startCamera().catch(error => {
        showStatus(cameraErrorMessage(error), false);
        camBadge.innerHTML = `<span class="h-2 w-2 rounded-full bg-red-500"></span> Kamera Off`;
    });

    switchCamera.addEventListener('click', async () => {
        if (cameraDevices.length < 2) {
            showStatus('Hanya ada satu kamera.', false);
            setTimeout(() => showStatus(null), 3000);
            return;
        }

        cameraIndex = (cameraIndex + 1) % cameraDevices.length;
        try {
            showStatus(`Mengalihkan kamera...`, true);
            await startCamera(cameraDevices[cameraIndex].deviceId);
        } catch (error) {
            showStatus(cameraErrorMessage(error), false);
        }
    });

    if (photo.value) {
        video.classList.add('hidden');
        preview.classList.remove('hidden');
    }

    capture.addEventListener('click', async () => {
        if (!video.videoWidth) {
            capture.disabled = true;
            showStatus('Membuka kamera...', true);
            try {
                await startCamera();
            } catch (error) {
                showStatus(cameraErrorMessage(error), false);
                camBadge.innerHTML = `<span class="h-2 w-2 rounded-full bg-red-500"></span> Kamera Off`;
                return;
            } finally {
                capture.disabled = false;
            }

            if (!video.videoWidth) {
                showStatus('Kamera belum siap, coba lagi.', false);
                return;
            }
        }

        const isRetake = !preview.classList.contains('hidden');

        if (isRetake) {
            preview.classList.add('hidden');
            video.classList.remove('hidden');
            captureText.textContent = 'Ambil Foto';
            submit.disabled = true;
            photo.value = '';
            camBadge.innerHTML = `<span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span> Kamera Aktif`;
            showStatus(null);
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
            showStatus(null);
        }
    });

    document.querySelector('#upload-form').addEventListener('submit', e => {
        if (!photo.value) {
            e.preventDefault();
            showStatus('Ambil foto terlebih dahulu.', false);
            return;
        }
        e.preventDefault();

        if (!navigator.geolocation) {
            showStatus('Browser tidak mendukung GPS.', false);
            return;
        }

        submit.disabled = true;
        capture.disabled = true;
        let bestPosition = null, finished = false, watchId, timer;

        showStatus('Mencari lokasi presisi...', true);

        const finish = () => {
            if (finished) return;
            finished = true;
            if (watchId !== undefined) navigator.geolocation.clearWatch(watchId);
            clearTimeout(timer);

            if (!bestPosition) {
                submit.disabled = false;
                capture.disabled = false;
                showStatus('Gagal mendapatkan lokasi. Aktifkan GPS.', false);
                return;
            }

            const { coords } = bestPosition;
            document.querySelector('#latitude').value = coords.latitude;
            document.querySelector('#longitude').value = coords.longitude;
            document.querySelector('#accuracy').value = coords.accuracy;

            showStatus(`Lokasi terkunci (±${Math.round(coords.accuracy)}m). Mengirim data...`, true);
            cameraStream?.getTracks().forEach(track => track.stop());

            if (!photo.value) {
                submit.disabled = false;
                capture.disabled = false;
                showStatus('Ambil foto ulang sebelum mengirim.', false);
                return;
            }

            HTMLFormElement.prototype.submit.call(e.target);
        };

        watchId = navigator.geolocation.watchPosition(
            pos => {
                if (!bestPosition || pos.coords.accuracy < bestPosition.coords.accuracy) {
                    bestPosition = pos;
                }
                const accuracy = Math.round(bestPosition.coords.accuracy);
                showStatus(accuracy <= 150
                    ? `Lokasi presisi didapat (±${accuracy}m). Mengirim...`
                    : `Meningkatkan akurasi GPS... (±${accuracy}m)`, true);

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
                    ? 'Izin lokasi ditolak. Izinkan lokasi di browser.'
                    : 'Gagal mendapatkan lokasi GPS.';
                showStatus(message, false);
            },
            { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
        );

        timer = setTimeout(finish, 10000);
    });
</script>
@endif
@endsection
