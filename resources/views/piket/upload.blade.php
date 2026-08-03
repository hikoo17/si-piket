@extends('layouts.app', ['navigation' => [
    ['dashboard', 'Dashboard', 'heroicons-o-squares-2x2'],
    ['schedules.index', 'Jadwal Piket', 'heroicons-o-clock'],
    ['piket.upload.form', 'Ambil Bukti', 'heroicons-o-qr-code'],
    ['verification.index', 'Verifikasi', 'heroicons-o-shield-check'],
    ['reports.index', 'Laporan', 'heroicons-o-clipboard-document-list'],
]])
@section('content')
<div class="mx-auto max-w-2xl">
    <h1 class="mb-2 text-2xl font-bold text-[#6d1a1a]">Ambil Bukti Piket</h1>
    <p class="mb-5 text-sm text-[#8d6e63]">Izinkan akses lokasi presisi dan kamera. Aktifkan GPS serta Wi-Fi agar titik lebih akurat.</p>
    <video id="camera" autoplay playsinline class="w-full rounded-xl bg-black"></video>
    <canvas id="canvas" class="hidden"></canvas>
    <img id="preview" class="mt-4 hidden w-full rounded-xl" alt="Pratinjau bukti">
    <form id="upload-form" method="POST" action="{{ route('piket.upload') }}" class="mt-4 space-y-3">
        @csrf
        <input type="hidden" name="photo" id="photo">
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <input type="hidden" name="accuracy" id="accuracy">
        <div class="flex gap-3">
            <button type="button" id="capture" class="rounded-lg bg-[#6d1a1a] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-px hover:bg-[#5a1515]">Ambil Foto</button>
            <button type="submit" id="submit" disabled class="rounded-lg bg-[#fbc02d] px-4 py-2.5 text-sm font-semibold text-[#4a1c1c] shadow-sm transition hover:-translate-y-px hover:bg-[#f9a825] disabled:opacity-40">Kirim Bukti</button>
        </div>
        <p id="status" class="text-sm text-[#8d6e63]" aria-live="polite"></p>
    </form>
</div>
<script>
const video=document.querySelector('#camera'),canvas=document.querySelector('#canvas'),photo=document.querySelector('#photo'),preview=document.querySelector('#preview'),capture=document.querySelector('#capture'),submit=document.querySelector('#submit'),status=document.querySelector('#status');
let cameraStream;
navigator.mediaDevices?.getUserMedia({video:{facingMode:{ideal:'environment'}},audio:false}).then(stream=>{cameraStream=stream;video.srcObject=stream}).catch(()=>status.textContent='Kamera tidak dapat diakses. Pastikan izin kamera aktif dan halaman menggunakan HTTPS.');
capture.addEventListener('click',()=>{if(!video.videoWidth){status.textContent='Kamera belum siap. Tunggu sebentar lalu coba lagi.';return}const scale=Math.min(1,1600/video.videoWidth);canvas.width=Math.round(video.videoWidth*scale);canvas.height=Math.round(video.videoHeight*scale);canvas.getContext('2d').drawImage(video,0,0,canvas.width,canvas.height);photo.value=canvas.toDataURL('image/jpeg',.72);preview.src=photo.value;preview.classList.remove('hidden');submit.disabled=false;status.textContent='Foto siap. Tekan Kirim Bukti untuk mengambil GPS terbaru.'});
document.querySelector('#upload-form').addEventListener('submit',e=>{if(!photo.value){e.preventDefault();return}e.preventDefault();if(!navigator.geolocation){status.textContent='Browser ini tidak mendukung GPS.';return}submit.disabled=true;capture.disabled=true;let bestPosition=null,finished=false,watchId,timer;status.textContent='Meminta izin dan mencari lokasi...';const finish=()=>{if(finished)return;finished=true;if(watchId!==undefined)navigator.geolocation.clearWatch(watchId);clearTimeout(timer);if(!bestPosition){submit.disabled=false;capture.disabled=false;status.textContent='Lokasi tidak ditemukan. Aktifkan GPS, izinkan lokasi presisi, lalu coba lagi.';return}const {coords}=bestPosition;document.querySelector('#latitude').value=coords.latitude;document.querySelector('#longitude').value=coords.longitude;document.querySelector('#accuracy').value=coords.accuracy;status.textContent=`Lokasi ditemukan (akurasi ±${Math.round(coords.accuracy)} m). Mengirim foto...`;cameraStream?.getTracks().forEach(track=>track.stop());HTMLFormElement.prototype.submit.call(e.target)};watchId=navigator.geolocation.watchPosition(pos=>{if(!bestPosition||pos.coords.accuracy<bestPosition.coords.accuracy)bestPosition=pos;const accuracy=Math.round(bestPosition.coords.accuracy);status.textContent=accuracy<=150?`Lokasi ditemukan (akurasi ±${accuracy} m). Menyiapkan pengiriman...`:`Meningkatkan akurasi lokasi... saat ini ±${accuracy} m`;if(bestPosition.coords.accuracy<=150)finish()},error=>{if(error.code===1){finished=true;clearTimeout(timer);if(watchId!==undefined)navigator.geolocation.clearWatch(watchId);submit.disabled=false;capture.disabled=false;status.textContent='Izin lokasi ditolak. Ubah izin situs menjadi Izinkan dan aktifkan lokasi presisi.'}},{enableHighAccuracy:true,timeout:8000,maximumAge:0});timer=setTimeout(finish,10000)});
</script>
@endsection
