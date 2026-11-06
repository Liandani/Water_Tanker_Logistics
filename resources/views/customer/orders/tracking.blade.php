@extends('layouts.customer')

@section('title', 'Live Tracking Pengiriman ' . $order->order_number)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <a href="{{ route('customer.orders.show', $order->id) }}" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left"></i> Kembali ke Detail Pesanan
        </a>
        <h3 class="fw-bold text-dark-blue mt-1 mb-0">Live Tracking Pengiriman Armada</h3>
        <small class="text-muted">Pantau posisi armada truk tangki menuju lokasi Anda secara berkala</small>
    </div>
    <div>
        <span class="badge {{ $order->status === 'Dikirim' ? 'badge-dikirim' : 'badge-pending' }} fs-6 px-3 py-2 rounded-pill">
            <i class="bi {{ $order->status === 'Dikirim' ? 'bi-truck' : 'bi-hourglass-split' }} me-1"></i>
            Status: {{ $order->status }}
        </span>
    </div>
</div>

@if($order->status !== 'Dikirim')
    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4" style="background-color: #FEF9EE; border-left: 4px solid #F5B942 !important;">
        <i class="bi bi-shield-exclamation fs-3 me-3 text-warning"></i>
        <div>
            <strong>Pelacakan GPS Belum Aktif</strong>
            <p class="mb-0 small text-dark mt-1">
                Sesuai kebijakan operasional sistem, pelacakan GPS armada secara live baru akan aktif setelah driver menekan tombol 'Mulai Perjalanan' dan status berubah menjadi <strong>Dikirim</strong>.
            </p>
        </div>
    </div>
@endif

<div class="row g-4">
    <!-- Map Container -->
    <div class="col-lg-8">
        <div class="card card-fresh p-3">
            <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                <span class="small fw-bold text-dark-blue">
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> Peta Posisi Armada Menuju Tujuan
                </span>
                <span class="badge bg-light text-secondary border small" id="lastUpdateBadge">
                    Update: Menghubungkan...
                </span>
            </div>

            <div id="trackingMap" class="map-fresh" style="height: 480px;"></div>

            <div class="d-flex justify-content-between align-items-center mt-3 px-2 small text-muted">
                <div>
                    <span class="badge bg-primary me-1"><i class="bi bi-truck"></i> Truk Tangki</span>
                    <span class="badge bg-danger me-1"><i class="bi bi-geo-alt-fill"></i> Lokasi Anda</span>
                    <span class="badge bg-dark"><i class="bi bi-building"></i> Depo Utama</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnRefreshTracking">
                    <i class="bi bi-arrow-clockwise me-1"></i> Segarkan Posisi
                </button>
            </div>
        </div>
    </div>

    <!-- Status & Driver Panel -->
    <div class="col-lg-4">
        <!-- Driver Card -->
        <div class="card card-fresh p-4 mb-4">
            <h6 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">Informasi Driver Lapangan</h6>
            @if($order->activeAssignment && $order->activeAssignment->driver)
                @php
                    $driverUser = $order->activeAssignment->driver->user;
                    $vehicle = $order->activeAssignment->vehicle;
                @endphp
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-primary-blue text-white d-flex align-items-center justify-content-center fw-bold fs-3 shadow-sm" style="width: 56px; height: 56px;">
                        {{ substr($driverUser->name, 0, 1) }}
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">{{ $driverUser->name }}</h6>
                        <small class="text-muted d-block">Pengemudi Truk Tangki</small>
                        <span class="badge bg-success-subtle text-success border border-success-subtle mt-1">
                            <i class="bi bi-check-circle me-1"></i> Terverifikasi
                        </span>
                    </div>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $driverUser->phone) }}?text=Halo%20Pak%20{{ urlencode($driverUser->name) }},%20saya%20pemesan%20order%20{{ $order->order_number }}" target="_blank" class="btn btn-success fw-bold py-2 shadow-sm">
                        <i class="bi bi-whatsapp me-1"></i> Chat WhatsApp Driver
                    </a>
                    <a href="tel:{{ $driverUser->phone }}" class="btn btn-outline-secondary btn-sm py-1">
                        <i class="bi bi-telephone-fill me-1"></i> Telepon Driver ({{ $driverUser->phone }})
                    </a>
                </div>

                <div class="card bg-light border-0 p-3 small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">No. Plat Truk:</span>
                        <strong class="text-dark">{{ $vehicle->plate_number }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Armada:</span>
                        <span class="text-dark">{{ $vehicle->brand_model ?? $vehicle->vehicle_type }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Volume Muatan:</span>
                        <strong class="text-primary-blue">{{ number_format($order->volume, 0, ',', '.') }} Liter</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Estimasi Jarak:</span>
                        <strong class="text-dark">{{ $order->estimated_distance }} km</strong>
                    </div>
                </div>
            @else
                <div class="text-center py-4 text-muted small">
                    Belum ada armada yang ditugaskan.
                </div>
            @endif
        </div>

        <!-- Destination Address Card -->
        <div class="card card-fresh p-4">
            <h6 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">Tujuan Pengiriman</h6>
            <div class="mb-2">
                <span class="text-muted small d-block">Perusahaan Mitra:</span>
                <strong class="text-dark">{{ $order->customer->company_name }}</strong>
            </div>
            <div class="mb-2">
                <span class="text-muted small d-block">Alamat Lokasi:</span>
                <span class="text-secondary small">{{ $order->delivery_address }}</span>
            </div>
            <div class="mb-2">
                <span class="text-muted small d-block">Penerima Lapangan:</span>
                <strong class="text-dark">{{ $order->recipient_name }}</strong> ({{ $order->recipient_phone }})
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const destLat = {{ (float) $order->latitude }};
    const destLng = {{ (float) $order->longitude }};
    const depotLat = {{ (float) $tariff->depot_latitude }};
    const depotLng = {{ (float) $tariff->depot_longitude }};
    const isLiveActive = {{ $order->status === 'Dikirim' ? 'true' : 'false' }};
    const trackingApiUrl = "{{ route('customer.orders.tracking.api', $order->id) }}";

    // Setup Leaflet map
    const map = L.map('trackingMap').setView([destLat, destLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Destination Pin (Customer)
    const targetIcon = L.divIcon({
        className: 'custom-dest-pin',
        html: '<div style="background-color: #E85D75; color: white; border-radius: 50%; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.3);"><i class="bi bi-geo-alt-fill" style="font-size: 16px;"></i></div>',
        iconSize: [34, 34],
        iconAnchor: [17, 34]
    });
    L.marker([destLat, destLng], { icon: targetIcon }).addTo(map).bindPopup('<strong>Tujuan Pengiriman Anda</strong><br>{{ addslashes($order->delivery_address) }}').openPopup();

    // Depot Pin
    const depotIcon = L.divIcon({
        className: 'custom-depot-pin',
        html: '<div style="background-color: #0B3D62; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"><i class="bi bi-building" style="font-size: 14px;"></i></div>',
        iconSize: [30, 30],
        iconAnchor: [15, 15]
    });
    L.marker([depotLat, depotLng], { icon: depotIcon }).addTo(map).bindPopup('<strong>Depo Utama Tirta Mandiri</strong>');

    // Driver Marker
    let driverMarker = null;
    const driverIcon = L.divIcon({
        className: 'custom-driver-pin',
        html: '<div style="background-color: #1687D9; color: white; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 4px 12px rgba(22,135,217,0.5);"><i class="bi bi-truck" style="font-size: 18px;"></i></div>',
        iconSize: [36, 36],
        iconAnchor: [18, 18]
    });

    let routeLine = null;

    function fetchDriverPosition() {
        if (!isLiveActive) {
            document.getElementById('lastUpdateBadge').innerText = 'Status bukan Dikirim';
            return;
        }

        fetch(trackingApiUrl)
            .then(res => res.json())
            .then(data => {
                if (data.live_tracking_active) {
                    const dLat = data.driver_latitude;
                    const dLng = data.driver_longitude;

                    if (driverMarker) {
                        driverMarker.setLatLng([dLat, dLng]);
                    } else {
                        driverMarker = L.marker([dLat, dLng], { icon: driverIcon }).addTo(map);
                    }

                    driverMarker.bindPopup(`<strong>Truk Tangki ${data.plate_number}</strong><br>Driver: ${data.driver_name}<br>Update: ${data.last_update}`);

                    // Draw line between driver & destination
                    if (routeLine) {
                        map.removeLayer(routeLine);
                    }
                    routeLine = L.polyline([[dLat, dLng], [destLat, destLng]], { color: '#1687D9', weight: 4, dashArray: '8, 8' }).addTo(map);

                    // Fit bounds so both driver and dest are visible
                    const bounds = L.latLngBounds([[dLat, dLng], [destLat, destLng]]);
                    map.fitBounds(bounds, { padding: [50, 50] });

                    document.getElementById('lastUpdateBadge').innerText = 'Update: ' + data.last_update;
                } else {
                    document.getElementById('lastUpdateBadge').innerText = data.message || 'GPS belum aktif';
                }
            })
            .catch(err => {
                console.error('Error fetching tracking:', err);
                document.getElementById('lastUpdateBadge').innerText = 'Gagal memuat GPS';
            });
    }

    if (isLiveActive) {
        fetchDriverPosition();
        // Poll every 15 seconds
        setInterval(fetchDriverPosition, 15000);
    }

    document.getElementById('btnRefreshTracking').addEventListener('click', fetchDriverPosition);
</script>
@endpush

