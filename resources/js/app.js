import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Toast / Flash Message (SweetAlert2 - Tetap Ada & Aktif)
    const flashMessageElement = document.getElementById('flash-message');
    if (flashMessageElement) {
        const flash = flashMessageElement.dataset;
        const message = flash.success || flash.error || flash.validation;

        if (message) {
            Swal.fire({
                icon: flash.success ? 'success' : 'error',
                title: flash.success ? 'Berhasil' : 'Terjadi Kesalahan',
                text: message,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#d97706',
            });
        }
    }

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || form.dataset.confirmed === 'true') return;

        if (form.hasAttribute('data-confirm-logout')) {
            event.preventDefault();
            Swal.fire({
                icon: 'question',
                title: 'Keluar dari aplikasi?',
                text: 'Anda harus masuk kembali untuk mengakses SI-PIKET.',
                showCancelButton: true,
                confirmButtonText: 'Ya, keluar',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d97706',
                cancelButtonColor: '#64748b',
                reverseButtons: true,
            }).then(({ isConfirmed }) => {
                if (isConfirmed) {
                    form.dataset.confirmed = 'true';
                    form.requestSubmit();
                }
            });
            return;
        }

        const method = form.querySelector('input[name="_method"]')?.value.toUpperCase();
        if (method !== 'DELETE') return;

        event.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Hapus data?',
            text: form.dataset.confirmMessage || 'Data yang dihapus tidak dapat dikembalikan.',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            reverseButtons: true,
        }).then(({ isConfirmed }) => {
            if (isConfirmed) {
                form.dataset.confirmed = 'true';
                form.requestSubmit();
            }
        });
    });

    // 2. Map & Search Logic
    const mapElement = document.getElementById('school-location-map');

    if (mapElement) {
        const latitudeInput = document.getElementById('school-latitude');
        const longitudeInput = document.getElementById('school-longitude');
        const radiusInput = document.getElementById('school-radius');
        const locationButton = document.getElementById('use-current-location');
        const schoolNameInput = document.getElementById('school-name');
        const searchInput = document.getElementById('location-search');
        const searchButton = document.getElementById('search-location');
        const resultsElement = document.getElementById('location-results');
        const statusElement = document.getElementById('location-status');
        const catalogElement = document.getElementById('location-catalog');
        
        let locationCatalog = [];

        try {
            locationCatalog = JSON.parse(catalogElement?.textContent || '[]');
        } catch (error) {
            console.error('Katalog lokasi tidak dapat dibaca.', error);
        }

        const defaultPosition = [
            Number(mapElement.dataset.defaultLatitude) || -7.32709600,
            Number(mapElement.dataset.defaultLongitude) || 108.22034900,
        ];

        const inputPosition = () => {
            if (!latitudeInput.value.trim() || !longitudeInput.value.trim()) return null;

            const latitude = Number(latitudeInput.value);
            const longitude = Number(longitudeInput.value);

            return Number.isFinite(latitude) && Number.isFinite(longitude)
                && latitude >= -90 && latitude <= 90
                && longitude >= -180 && longitude <= 180
                ? [latitude, longitude]
                : null;
        };

        // Mengambil radius aman (jika kosong / NaN maka otomatis 100m)
        const getSafeRadius = () => {
            const rawVal = Number(radiusInput.value);
            return Number.isFinite(rawVal) && rawVal > 0 ? rawVal : 100;
        };

        const initialPosition = inputPosition() ?? defaultPosition;
        
        // Zoom diatur ke 16 agar radius 100m langsung kelihatan dekat
        const map = L.map(mapElement).setView(initialPosition, 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
            crossOrigin: true,
        }).addTo(map);

        const schoolIcon = L.divIcon({
            className: 'custom-school-icon',
            html: '<span role="img" aria-label="Lokasi sekolah" style="display:block;font-size:34px;line-height:38px;filter:drop-shadow(0 2px 2px rgb(0 0 0 / .35));">🏫</span>',
            iconSize: [38, 38],
            iconAnchor: [19, 34],
        });

        const marker = L.marker(initialPosition, { draggable: true, icon: schoolIcon }).addTo(map);

        // Lingkaran Radius: border merah tegas + isi tipis agar terbaca sebagai batas jangkauan.
        // Angka pada peta menunjukkan besaran radius (meter) sesuai input pengguna.
        const radiusCircle = L.circle(initialPosition, {
            color: '#ef4444',       // Warna border (Merah)
            weight: 3,              // Ketebalan border garis
            opacity: 1,             // Opasitas border (kontras)
            fillColor: '#ef4444',   // Warna isian
            fillOpacity: 0.12,      // Isi tipis agar ring merah menonjol
            radius: getSafeRadius(),
        }).addTo(map);

        radiusCircle.bindPopup(`Radius: ${getSafeRadius()} meter`);
        radiusCircle.bindTooltip(`${getSafeRadius()} m`, {
            permanent: true,
            direction: 'top',
            offset: [0, -8],
            className: 'radius-tooltip',
        });
        radiusCircle.bringToFront();

        const syncRadiusLabel = () => {
            const rad = getSafeRadius();
            radiusCircle.setTooltipContent(`${rad} m`);
            radiusCircle.setPopupContent(`Radius: ${rad} meter`);
        };

        const setPosition = (position, recenter = false) => {
            latitudeInput.value = position.lat.toFixed(8);
            longitudeInput.value = position.lng.toFixed(8);
            marker.setLatLng(position);
            radiusCircle.setLatLng(position);

            if (recenter) {
                map.setView(position, Math.max(map.getZoom(), 16));
            }
        };

        map.on('click', (event) => setPosition(event.latlng));
        marker.on('dragend', () => setPosition(marker.getLatLng()));

        const syncMapFromInputs = () => {
            const position = inputPosition();
            if (position) {
                marker.setLatLng(position);
                radiusCircle.setLatLng(position);
                map.panTo(position);
            }
        };

        latitudeInput.addEventListener('change', syncMapFromInputs);
        longitudeInput.addEventListener('change', syncMapFromInputs);

        // Update Radius Real-time
        const updateRadius = () => {
            const rad = getSafeRadius();
            radiusCircle.setRadius(rad);
            syncRadiusLabel();
        };

        radiusInput.addEventListener('input', updateRadius);
        radiusInput.addEventListener('change', updateRadius);

        const showResults = (results) => {
            resultsElement.replaceChildren();
            if (!results.length) {
                resultsElement.textContent = 'Lokasi tidak ditemukan. Coba nama jalan atau wilayah terdekat.';
                return;
            }
            results.forEach((result) => {
                const button = document.createElement('button');
                button.className = 'block w-full rounded border p-2 text-left hover:bg-slate-50';
                button.type = 'button';
                button.textContent = `${result.name} - ${result.address} (${result.source})`;
                button.addEventListener('click', () => {
                    setPosition(L.latLng(result.latitude, result.longitude), true);
                    searchInput.value = result.address;
                    if (!schoolNameInput.value.trim()) schoolNameInput.value = result.name;
                    statusElement.textContent = `${result.name} dipilih. Geser marker jika titik perlu disesuaikan.`;
                    resultsElement.replaceChildren();
                });
                resultsElement.append(button);
            });
        };

        const searchLocation = async () => {
            const query = searchInput.value.trim();
            if (!query) {
                resultsElement.textContent = 'Masukkan nama lokasi atau alamat terlebih dahulu.';
                searchInput.focus();
                return;
            }

            searchButton.disabled = true;
            resultsElement.textContent = 'Mencari lokasi...';
            const normalizedQuery = query.toLowerCase().replace(/\bsma\s*negeri\b/g, 'sman').replace(/[^a-z0-9]/g, '');
            
            const localResults = locationCatalog.filter((place) => {
                const searchableText = `${place.name || ''} ${place.address || ''}`
                    .toLowerCase()
                    .replace(/\bsma\s*negeri\b/g, 'sman')
                    .replace(/[^a-z0-9]/g, '');
                return searchableText.includes(normalizedQuery);
            });

            if (localResults.length) {
                showResults(localResults);
                searchButton.disabled = false;
                return;
            }

            try {
                const photonResponse = await fetch(`https://photon.komoot.io/api/?limit=5&q=${encodeURIComponent(`${query}, Indonesia`)}`);
                if (!photonResponse.ok) throw new Error('Photon failed');
                
                let results = (await photonResponse.json()).features.map((feature) => ({
                    name: feature.properties.name || query,
                    address: [feature.properties.street, feature.properties.city, feature.properties.state, feature.properties.country].filter(Boolean).join(', '),
                    latitude: feature.geometry.coordinates[1],
                    longitude: feature.geometry.coordinates[0],
                    source: 'Photon/OpenStreetMap',
                }));

                showResults(results);
            } catch {
                resultsElement.textContent = 'Gagal mencari lokasi. Periksa koneksi internet Anda.';
            } finally {
                searchButton.disabled = false;
            }
        };

        searchButton.addEventListener('click', searchLocation);
        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') { 
                event.preventDefault(); 
                searchLocation(); 
            }
        });

        // Re-render ukuran peta dan fokuskan tampilan ke lingkaran radius
        // (padding lebih besar + maxZoom agar seluruh ring merah & lingkungan terlihat jelas)
        setTimeout(() => {
            map.invalidateSize();
            if (radiusCircle.getBounds().isValid()) {
                map.fitBounds(radiusCircle.getBounds(), { padding: [50, 50], maxZoom: 17 });
            }
        }, 300);

        locationButton.addEventListener('click', () => {
            if (!navigator.geolocation) {
                statusElement.textContent = 'Browser tidak mendukung geolokasi.';
                return;
            }

            locationButton.disabled = true;
            statusElement.textContent = 'Mengambil lokasi perangkat...';

            navigator.geolocation.getCurrentPosition(
                ({ coords }) => {
                    setPosition(L.latLng(coords.latitude, coords.longitude), true);
                    statusElement.textContent = `Lokasi ditemukan (Akurasi: ${Math.round(coords.accuracy)}m).`;
                    locationButton.disabled = false;
                },
                () => {
                    statusElement.textContent = 'Izin lokasi ditolak atau gagal mengambil koordinat.';
                    locationButton.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        });
    }
});
