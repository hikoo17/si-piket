import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

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
    const locationCatalog = JSON.parse(mapElement.dataset.locationCatalog || '[]');
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
    }).addTo(map);

    const schoolIcon = L.divIcon({
        className: '',
        html: '<span role="img" aria-label="Lokasi sekolah" style="display:block;font-size:34px;line-height:38px;filter:drop-shadow(0 2px 2px rgb(0 0 0 / .35));">🏫</span>',
        iconSize: [38, 38],
        iconAnchor: [19, 34],
    });
    const marker = L.marker(initialPosition, { draggable: true, icon: schoolIcon }).addTo(map);
    const radiusCircle = L.circle(initialPosition, {
        color: '#4338ca',
        fillColor: '#6366f1',
        fillOpacity: 0.15,
        radius: Number(radiusInput.value) || 100,
    }).addTo(map);

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
        if (!query) return;
        searchButton.disabled = true;
        resultsElement.textContent = 'Mencari lokasi...';
        const normalizedQuery = query.toLowerCase().replace(/\bsma\s*negeri\b/g, 'sman').replace(/[^a-z0-9]/g, '');
        const localResults = locationCatalog.filter((place) => {
            const normalizedName = place.name.toLowerCase().replace(/\bsma\s*negeri\b/g, 'sman').replace(/[^a-z0-9]/g, '');
            return normalizedName.includes(normalizedQuery) || normalizedQuery.includes(normalizedName);
        });
        if (localResults.length) {
            showResults(localResults);
            searchButton.disabled = false;
            return;
        }
        try {
            const photonResponse = await fetch(`https://photon.komoot.io/api/?limit=5&q=${encodeURIComponent(query)}`);
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
                results = (await nominatimResponse.json()).map((result) => ({
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
