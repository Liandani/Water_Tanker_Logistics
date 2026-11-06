@extends('layouts.admin')

@section('title', 'Monitoring Peta Seluruh Armada')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark-blue mb-1">Monitoring Peta Seluruh Armada Tangki</h3>
        <p class="text-muted small mb-0">Pantau pergerakan posisi GPS armada, status penugasan, dan pengiriman secara real-time</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-light text-secondary border px-3 py-2 small" id="syncStatus">
            <i class="bi bi-broadcast text-primary me-1"></i> Terhubung Live
        </span>
        <button type="button" class="btn btn-primary-blue btn-sm" id="btnRefreshMap">
            <i class="bi bi-arrow-clockwise me-1"></i> Segarkan Peta
        </button>
    </div>
</div>

<!-- Marker Legend -->
<div class="card card-fresh p-3 mb-3">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 small">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <span class="fw-bold text-dark-blue">Legenda Status Armada:</span>
            <span class="d-flex align-items-center gap-1">
                <span class="d-inline-block rounded-circle" style="width: 12px; height: 12px; background-color: #20B486;"></span>
                <strong>Hijau:</strong> Ready (Di Depo / Garasi)
            </span>
            <span class="d-flex align-items-center gap-1">
                <span class="d-inline-block rounded-circle" style="width: 12px; height: 12px; background-color: #1687D9;"></span>
                <strong>Biru:</strong> Sedang Dikirim (Live On Duty)
            </span>
            <span class="d-flex align-items-center gap-1">
                <span class="d-inline-block rounded-circle" style="width: 12px; height: 12px; background-color: #E85D75;"></span>
                <strong>Merah:</strong> Maintenance / Servis
            </span>
            <span class="d-flex align-items-center gap-1">
                <span class="d-inline-block rounded-circle" style="width: 12px; height: 12px; background-color: #F5B942;"></span>
                <strong>Kuning:</strong> Kendala / Pending Review
            </span>
        </div>
        <div class="text-muted">
            <i class="bi bi-building text-dark me-1"></i> Depo Utama Pusat
        </div>
    </div>
</div>

<!-- Map Card -->
<div class="card card-fresh p-2 mb-4">
    <div id="fleetMap" class="map-fresh" style="height: 600px; width: 100%;"></div>
</div>
@endsection

@push('scripts')
<script>
    const depotLat = {{ (float) $tariff->depot_latitude }};
    const depotLng = {{ (float) $tariff->depot_longitude }};
    const monitoringApiUrl = "{{ route('admin.monitoring.data') }}";

    // Setup map
    const map = L.map('fleetMap').setView([depotLat, depotLng], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Depot Marker
    const depotIcon = L.divIcon({
        className: 'custom-depot-pin',
        html: '<div style="background-color: #0B3D62; color: white; border-radius: 8px; padding: 6px 10px; font-weight: bold; font-size: 11px; display: flex; align-items: center; gap: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border: 2px solid white;"><i class="bi bi-building-fill text-aqua"></i> DEPO PUSAT</div>',
        iconSize: [110, 32],
        iconAnchor: [55, 16]
    });
    L.marker([depotLat, depotLng], { icon: depotIcon }).addTo(map).bindPopup('<strong>Depo Utama Tirta Mandiri</strong><br>{{ addslashes($tariff->company_address) }}');

    let vehicleMarkersLayer = L.layerGroup().addTo(map);

    function getMarkerColorHex(color) {
        switch(color) {
            case 'green': return '#20B486';
            case 'blue': return '#1687D9';
            case 'red': return '#E85D75';
            case 'yellow': return '#F5B942';
            default: return '#718096';
        }
    }

    function loadFleetData() {
        document.getElementById('syncStatus').innerHTML = '<i class="bi bi-arrow-repeat spin me-1"></i> Memperbarui...';

        fetch(monitoringApiUrl)
            .then(res => res.json())
            .then(data => {
                vehicleMarkersLayer.clearLayers();

                if (data.vehicles && data.vehicles.length > 0) {
                    data.vehicles.forEach(v => {
                        const colorHex = getMarkerColorHex(v.marker_color);

                        const iconHtml = `
                            <div style="background-color: ${colorHex}; color: white; border-radius: 20px; padding: 4px 10px; font-weight: 700; font-size: 11px; white-space: nowrap; box-shadow: 0 3px 8px rgba(0,0,0,0.3); border: 2px solid white; display: flex; align-items: center; gap: 4px;">
                                <i class="bi bi-truck"></i> ${v.plate_number}
                            </div>
                        `;

                        const vehicleIcon = L.divIcon({
                            className: 'custom-vehicle-marker',
                            html: iconHtml,
                            iconSize: [100, 28],
                            iconAnchor: [50, 14]
                        });

                        const popupContent = `
                            <div style="min-width: 220px; font-family: Inter, sans-serif;">
                                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #EAF7FF; padding-bottom: 6px; margin-bottom: 8px;">
                                    <strong style="color: #0B3D62; font-size: 14px;">${v.plate_number}</strong>
                                    <span style="background-color: ${colorHex}; color: white; font-size: 10px; padding: 2px 8px; border-radius: 10px; font-weight: bold;">${v.status}</span>
                                </div>
                                <div style="font-size: 12px; margin-bottom: 4px;"><strong>Tipe:</strong> ${v.vehicle_type} (${v.capacity.toLocaleString('id-ID')} L)</div>
                                <div style="font-size: 12px; margin-bottom: 4px;"><strong>Driver:</strong> ${v.driver_name}</div>
                                <div style="font-size: 12px; margin-bottom: 4px;"><strong>Order:</strong> ${v.order_number} ${v.customer_name !== '-' ? `(${v.customer_name})` : ''}</div>
                                <div style="font-size: 12px; margin-bottom: 4px;"><strong>Tujuan:</strong> ${v.destination}</div>
                                <div style="font-size: 11px; color: #718096; margin-top: 6px; border-top: 1px dashed #E2E8F0; padding-top: 4px;">Update: ${v.updated_at}</div>
                            </div>
                        `;

                        L.marker([v.latitude, v.longitude], { icon: vehicleIcon })
                            .addTo(vehicleMarkersLayer)
                            .bindPopup(popupContent);
                    });
                }

                const now = new Date().toLocaleTimeString('id-ID');
                document.getElementById('syncStatus').innerHTML = `<i class="bi bi-check-circle-fill text-success me-1"></i> Update: ${now}`;
            })
            .catch(err => {
                console.error('Error fetching fleet data:', err);
                document.getElementById('syncStatus').innerHTML = '<i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Gagal Memuat Data';
            });
    }

    // Initial load
    loadFleetData();

    // Periodic refresh every 20 seconds
    setInterval(loadFleetData, 20000);

    document.getElementById('btnRefreshMap').addEventListener('click', loadFleetData);
</script>
@endpush

