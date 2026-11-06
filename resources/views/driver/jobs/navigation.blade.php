@extends('layouts.driver')

@section('title', 'Navigasi GPS ' . $assignment->order->order_number)

@section('content')
<div class="mb-3">
    <a href="{{ route('driver.jobs.show', $assignment->id) }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left"></i> Kembali ke Rincian Tugas
    </a>
    <div class="d-flex justify-content-between align-items-center mt-1">
        <h5 class="fw-bold text-dark-blue mb-0">Navigasi GPS & Rute Armada</h5>
        <span class="badge bg-success small px-2 py-1" id="gpsStatusBadge">
            <i class="bi bi-broadcast me-1"></i> GPS Siap
        </span>
    </div>
</div>

<!-- Navigation Header Card -->
<div class="card driver-card p-3 mb-3 bg-white shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <strong class="text-dark">{{ $assignment->order->customer->company_name }}</strong>
        <span class="badge bg-light-blue text-primary-blue">{{ number_format($assignment->order->volume, 0, ',', '.') }} L</span>
    </div>
    <div class="small text-muted mb-3">
        <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $assignment->order->delivery_address }}
    </div>

    <!-- External Google Maps Navigation Button -->
    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $assignment->order->latitude }},{{ $assignment->order->longitude }}" target="_blank" class="btn btn-danger w-100 fw-bold py-2 shadow-sm mb-2">
        <i class="bi bi-cursor-fill me-1"></i> Buka Petunjuk Arah di Google Maps
    </a>

    <button type="button" class="btn btn-outline-primary btn-sm w-100" id="btnSyncGpsNow">
        <i class="bi bi-arrow-clockwise me-1"></i> Kirim Koordinat Saya Sekarang
    </button>
</div>

<!-- Map Container -->
<div class="card driver-card p-2 bg-white mb-3">
    <div id="driverNavMap" class="map-fresh" style="height: 380px;"></div>
    <div class="d-flex justify-content-between align-items-center p-2 small text-muted">
        <span id="gpsCoordText">Mendeteksi posisi GPS...</span>
        <span id="gpsSyncText" class="text-secondary">-</span>
    </div>
</div>

<!-- Quick Action back to update status -->
<div class="d-grid gap-2 mb-4">
    <a href="{{ route('driver.jobs.show', $assignment->id) }}" class="btn btn-primary-blue fw-bold py-3 fs-6 shadow">
        <i class="bi bi-clipboard2-check-fill me-2"></i> Perbarui Status & Pengisian Air
    </a>
</div>
@endsection

@push('scripts')
<script>
    const destLat = {{ (float) $assignment->order->latitude }};
    const destLng = {{ (float) $assignment->order->longitude }};
    const updateGpsUrl = "{{ route('driver.jobs.gps', $assignment->id) }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let driverLat = {{ (float) ($assignment->driver->current_latitude ?? -6.2088000) }};
    let driverLng = {{ (float) ($assignment->driver->current_longitude ?? 106.8456000) }};

    // Setup map
    const map = L.map('driverNavMap').setView([driverLat, driverLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // Destination Pin
    const targetIcon = L.divIcon({
        className: 'dest-pin',
        html: '<div style="background-color: #E85D75; color: white; border-radius: 50%; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.3);"><i class="bi bi-geo-alt-fill" style="font-size: 16px;"></i></div>',
        iconSize: [34, 34],
        iconAnchor: [17, 34]
    });
    L.marker([destLat, destLng], { icon: targetIcon }).addTo(map).bindPopup('<strong>Tujuan Pengiriman</strong><br>{{ addslashes($assignment->order->delivery_address) }}');

    // Driver Pin
    const driverIcon = L.divIcon({
        className: 'driver-truck-pin',
        html: '<div style="background-color: #1687D9; color: white; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 4px 12px rgba(22,135,217,0.5);"><i class="bi bi-truck" style="font-size: 18px;"></i></div>',
        iconSize: [36, 36],
        iconAnchor: [18, 18]
    });

    let driverMarker = L.marker([driverLat, driverLng], { icon: driverIcon }).addTo(map);
    let routeLine = L.polyline([[driverLat, driverLng], [destLat, destLng]], { color: '#1687D9', weight: 4, dashArray: '6, 6' }).addTo(map);

    // Fit map bounds
    map.fitBounds([[driverLat, driverLng], [destLat, destLng]], { padding: [40, 40] });

    function sendGpsUpdate(lat, lng, speed = null, heading = null) {
        fetch(updateGpsUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                latitude: lat,
                longitude: lng,
                speed: speed,
                heading: heading
            })
        })
        .then(res => res.json())
        .then(data => {
            const timeStr = new Date().toLocaleTimeString('id-ID');
            document.getElementById('gpsSyncText').innerText = 'Tersinkron: ' + timeStr;
            document.getElementById('gpsStatusBadge').className = 'badge bg-success small px-2 py-1';
            document.getElementById('gpsStatusBadge').innerHTML = '<i class="bi bi-broadcast me-1"></i> GPS Aktif';
        })
        .catch(err => {
            console.error('Error syncing GPS:', err);
            document.getElementById('gpsStatusBadge').className = 'badge bg-danger small px-2 py-1';
            document.getElementById('gpsStatusBadge').innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> Sinkron Gagal';
        });
    }

    function onGpsPositionSuccess(pos) {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        const speed = pos.coords.speed;
        const heading = pos.coords.heading;

        driverLat = lat;
        driverLng = lng;

        driverMarker.setLatLng([lat, lng]);
        routeLine.setLatLngs([[lat, lng], [destLat, destLng]]);

        document.getElementById('gpsCoordText').innerText = `Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}`;

        sendGpsUpdate(lat, lng, speed, heading);
    }

    function onGpsPositionError(err) {
        console.warn('GPS Error: ' + err.message);
        document.getElementById('gpsCoordText').innerText = 'Menggunakan estimasi default';
    }

    // Geolocation API live tracking watcher
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(onGpsPositionSuccess, onGpsPositionError, { enableHighAccuracy: true });

        // Update periodically every 20 seconds
        setInterval(function() {
            navigator.geolocation.getCurrentPosition(onGpsPositionSuccess, onGpsPositionError, { enableHighAccuracy: true });
        }, 20000);
    } else {
        document.getElementById('gpsCoordText').innerText = 'Browser tidak mendukung GPS';
    }

    document.getElementById('btnSyncGpsNow').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(onGpsPositionSuccess, onGpsPositionError, { enableHighAccuracy: true });
        }
    });
</script>
@endpush

