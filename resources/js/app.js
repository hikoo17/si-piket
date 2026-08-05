import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import Swal from 'sweetalert2';

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
        Number(mapElement.dataset.defaultLatitude),
        Number(mapElement.dataset.defaultLongitude),
    ];

    const inputPosition = () => {
        if (latitudeInput.value.trim() === '' || longitudeInput.value.trim() === '') {
            return null;
        }

        const latitude = Number(latitudeInput.value);
        const longitude = Number(longitudeInput.value);

        return Number.isFinite(latitude) && Number.isFinite(longitude)
            && latitude >= -90 && latitude <= 90
            && longitude >= -180 && longitude <= 180
            ? [latitude, longitude]
            : null;
    };

    const initialPosition = inputPosition() ?? defaultPosition;
    const map = L.map(mapElement).setView(initialPosition, inputPosition() ? 17 : 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
        crossOrigin: true,
    }).addTo(map);

    const schoolIcon = L.divIcon({
        className: '',
        html: '<span role="img" aria-label="Lokasi sekolah" style="display:block;font-size:34px;line-height:38px;filter:drop-shadow(0 2px 2px rgb(0 0 0 / .35));">🏫</span>',
        iconSize: [38, 38],
        iconAnchor: [19, 34],
    });
    const marker = L.marker(initialPosition, { draggable: true, icon: schoolIcon }).addTo(map);
    const radiusCircle = L.circle(initialPosition, {
        color: '#b91c1c',
        weight: 2,
        fillColor: '#dc2626',
        fillOpacity: 0.18,
        radius: Number(radiusInput.value) || 100,
    }).addTo(map);
    radiusCircle.bindPopup(`Radius: ${Number(radiusInput.value) || 100} meter`);

    const setPosition = (position, recenter = false) => {
        latitudeInput.value = position.lat.toFixed(8);
        longitudeInput.value = position.lng.toFixed(8);
        marker.setLatLng(position);
        radiusCircle.setLatLng(position);

        if (recenter) {
            map.setView(position, Math.max(map.getZoom(), 17));
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
    radiusInput.addEventListener('input', () => {
        const radius = Number(radiusInput.value);
        if (Number.isFinite(radius) && radius > 0) {
            radiusCircle.setRadius(radius);
            radiusCircle.setPopupContent(`Radius: ${radius} meter`);
        }
    });

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
            const photonResponse = await fetch(`https://photon.komoot.io/api/?limit=5&q=${encodeURIComponent(`${query}, Indonesia`)}`, {
                headers: { Accept: 'application/json' },
            });
            if (!photonResponse.ok) throw new Error('Photon failed');
            let results = (await photonResponse.json()).features.map((feature) => ({
                name: feature.properties.name || query,
                address: [feature.properties.street, feature.properties.city, feature.properties.state, feature.properties.country].filter(Boolean).join(', '),
                latitude: feature.geometry.coordinates[1],
                longitude: feature.geometry.coordinates[0],
                source: 'Photon/OpenStreetMap',
            }));
            if (!results.length) {
                const nominatimResponse = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=5&countrycodes=id&q=${encodeURIComponent(query)}`, { headers: { Accept: 'application/json' } });
                if (!nominatimResponse.ok) throw new Error('Nominatim failed');
                results = (await nominatimResponse.json()).map((result) => ({
                    name: result.name || result.display_name.split(',')[0],
                    address: result.display_name,
                    latitude: Number(result.lat),
                    longitude: Number(result.lon),
                    source: 'Nominatim/OpenStreetMap',
                }));
            }
            showResults(results);
        } catch {
            try {
                const nominatimResponse = await fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=5&countrycodes=id&q=${encodeURIComponent(query)}`, { headers: { Accept: 'application/json' } });
                if (!nominatimResponse.ok) throw new Error('Nominatim failed');
                const results = (await nominatimResponse.json()).map((result) => ({
                    name: result.name || result.display_name.split(',')[0],
                    address: result.display_name,
                    latitude: Number(result.lat),
                    longitude: Number(result.lon),
                    source: 'Nominatim/OpenStreetMap',
                }));
                showResults(results);
            } catch {
                resultsElement.textContent = 'Pencarian gratis gagal. Periksa koneksi internet atau pilih titik langsung di peta.';
            }
        } finally {
            searchButton.disabled = false;
        }
    };
    searchButton.addEventListener('click', searchLocation);
    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') { event.preventDefault(); searchLocation(); }
    });

    window.setTimeout(() => map.invalidateSize(), 0);

    locationButton.addEventListener('click', () => {
        if (!navigator.geolocation) {
            statusElement.textContent = 'Browser ini tidak mendukung pengambilan lokasi.';
            return;
        }

        locationButton.disabled = true;
        statusElement.textContent = 'Mengambil lokasi perangkat...';

        navigator.geolocation.getCurrentPosition(
            ({ coords }) => {
                setPosition(L.latLng(coords.latitude, coords.longitude), true);
                statusElement.textContent = `Lokasi ditemukan dengan akurasi sekitar ${Math.round(coords.accuracy)} meter.`;
                locationButton.disabled = false;
            },
            () => {
                statusElement.textContent = 'Lokasi tidak dapat diambil. Izinkan akses lokasi atau pilih titik pada peta.';
                locationButton.disabled = false;
            },
            { enableHighAccuracy: true, timeout: 10000 },
        );
    });
}
