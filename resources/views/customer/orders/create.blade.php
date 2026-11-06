@extends('layouts.customer')

@section('title', 'Pemesanan Air Tangki Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('customer.dashboard') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
                <h3 class="fw-bold text-dark-blue mt-1 mb-0">Form Pemesanan Air Armada Truk Tangki</h3>
            </div>
            <span class="badge bg-light-blue text-primary-blue px-3 py-2 border rounded-pill">
                <i class="bi bi-info-circle me-1"></i> Penentuan Armada Otomatis
            </span>
        </div>

        <form action="{{ route('customer.orders.store') }}" method="POST" id="orderForm">
            @csrf

            <div class="row g-4">
                <!-- Left Column: Order Inputs -->
                <div class="col-lg-7">
                    <div class="card card-fresh p-4 mb-4">
                        <h5 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">
                            <i class="bi bi-1-circle-fill text-primary-blue me-2"></i> Data Pemesan & Pengiriman
                        </h5>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Nama Perusahaan / Mitra</label>
                            <input type="text" class="form-control bg-light" value="{{ $customer->company_name }}" readonly>
                            <small class="text-muted" style="font-size: 0.72rem;">Diambil otomatis dari profil mitra Anda.</small>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="recipient_name" class="form-label fw-semibold small text-dark">Nama Penerima di Lokasi</label>
                                <input type="text" class="form-control" id="recipient_name" name="recipient_name" value="{{ old('recipient_name', $customer->contact_person) }}" placeholder="Nama PIC lapangan">
                            </div>
                            <div class="col-md-6">
                                <label for="recipient_phone" class="form-label fw-semibold small text-dark">No. Kontak Penerima (WhatsApp)</label>
                                <input type="text" class="form-control" id="recipient_phone" name="recipient_phone" value="{{ old('recipient_phone', auth()->user()->phone) }}" placeholder="0812xxxxxxxx">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="delivery_date" class="form-label fw-semibold small text-dark">Tanggal Pengiriman <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" id="delivery_date" name="delivery_date" min="{{ date('Y-m-d') }}" value="{{ old('delivery_date', date('Y-m-d')) }}" required>
                            @error('delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="delivery_address" class="form-label fw-semibold small text-dark">Alamat Lengkap Pengiriman <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('delivery_address') is-invalid @enderror" id="delivery_address" name="delivery_address" rows="3" required placeholder="Contoh: Jl. Hayam Wuruk No. 125, Gambir, Jakarta Pusat (Pintu Loading Belakang)">{{ old('delivery_address', $customer->address) }}</textarea>
                            @error('delivery_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label for="notes" class="form-label fw-semibold small text-dark">Catatan Khusus untuk Driver (Opsional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Contoh: Kapasitas ground tank 20.000 L, harap bawa selang minimal 15 meter.">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <!-- Volume Selection Card -->
                    <div class="card card-fresh p-4">
                        <h5 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">
                            <i class="bi bi-2-circle-fill text-primary-blue me-2"></i> Volume Air Tangki
                        </h5>

                        <div class="mb-3">
                            <label for="volumeInput" class="form-label fw-bold text-dark">
                                Jumlah Air yang Dipesan (Liter) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light-blue text-primary-blue fw-bold border-end-0">
                                    <i class="bi bi-water"></i>
                                </span>
                                <input type="number" class="form-control fw-bold fs-4 text-primary-blue @error('volume') is-invalid @enderror" id="volumeInput" name="volume" value="{{ old('volume', 5000) }}" min="500" step="500" required placeholder="Ketik volume liter...">
                                <span class="input-group-text bg-light fw-bold text-muted">LITER</span>
                            </div>
                            <small class="text-muted d-block mt-1">
                                Ketik nominal liter yang Anda butuhkan (bebas mengetik nominal berapa saja). Sistem otomatis menentukan ukuran tangki armada terkecil yang mampu menampungnya.
                            </small>
                            @error('volume')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Quick Volume Presets -->
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-semibold mb-2">Preset Cepat:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="setVolume(5000)">5.000 L</button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="setVolume(7000)">7.000 L</button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="setVolume(8000)">8.000 L</button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="setVolume(10000)">10.000 L</button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="setVolume(16000)">16.000 L</button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="setVolume(18000)">18.000 L</button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="setVolume(20000)">20.000 L</button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="setVolume(25000)">25.000 L (Multi Trip)</button>
                            </div>
                        </div>

                        <!-- Dynamic Fleet Recommendation Badge -->
                        <div class="p-3 rounded-3 mt-3" style="background-color: #EAF7FF; border: 1px solid #BAE6FD;" id="recommendationBox">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary-blue text-white d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                                    <i class="bi bi-truck fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-uppercase fw-bold text-primary-blue" style="letter-spacing: 0.5px; font-size: 0.72rem;">Rekomendasi Armada Otomatis</small>
                                    <h5 class="fw-bold text-dark-blue mb-0" id="fleetCategoryText">Tangki 7.000 L</h5>
                                    <small class="text-muted" id="fleetDetailText">Armada ideal untuk kapasitas s/d 7.000 Liter (1 Trip)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Interactive Map & Price Summary -->
                <div class="col-lg-5">
                    <!-- Map Card -->
                    <div class="card card-fresh p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <h5 class="fw-bold text-dark-blue mb-0">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> Titik Lokasi Peta
                            </h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnMyLocation">
                                <i class="bi bi-crosshair me-1"></i> Lokasi Saya
                            </button>
                        </div>
                        <p class="small text-muted mb-2">Klik pada peta atau geser penanda untuk menentukan titik pengiriman air secara tepat.</p>

                        <!-- Leaflet Map Container -->
                        <div id="orderMap" class="map-fresh mb-3" style="height: 260px;"></div>

                        <div class="row g-2">
                            <div class="col-6">
                                <label class="small text-muted fw-semibold">Latitude</label>
                                <input type="text" class="form-control form-control-sm bg-light" id="latitude" name="latitude" value="{{ old('latitude', $customer->latitude ?? -6.200000) }}" readonly required>
                            </div>
                            <div class="col-6">
                                <label class="small text-muted fw-semibold">Longitude</label>
                                <input type="text" class="form-control form-control-sm bg-light" id="longitude" name="longitude" value="{{ old('longitude', $customer->longitude ?? 106.816666) }}" readonly required>
                            </div>
                        </div>
                        <small class="text-success mt-1 d-block" id="distanceDisplay">
                            <i class="bi bi-signpost-2 me-1"></i> Estimasi Jarak dari Depo: <strong>0 km</strong>
                        </small>
                    </div>

                    <!-- Price Calculation Card -->
                    <div class="card card-fresh p-4 border-primary">
                        <h5 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">
                            <i class="bi bi-calculator-fill text-primary-blue me-2"></i> Rincian & Estimasi Harga
                        </h5>

                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Harga Air Bersih:</span>
                            <span class="fw-semibold text-dark" id="displayWaterPrice">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Biaya Kirim Armada:</span>
                            <span class="fw-semibold text-dark" id="displayDeliveryPrice">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Tarif Air per Liter:</span>
                            <span class="text-secondary">Rp {{ number_format($tariff->water_price_per_liter, 0, ',', '.') }}/L</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 small">
                            <span class="text-muted">Tarif Kirim per KM:</span>
                            <span class="text-secondary">Rp {{ number_format($tariff->delivery_price_per_km, 0, ',', '.') }}/KM</span>
                        </div>

                        <hr class="my-2">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <span class="fw-bold text-dark-blue">Total Estimasi:</span>
                                <small class="d-block text-muted" style="font-size: 0.72rem;">Termasuk armada & air</small>
                            </div>
                            <h3 class="fw-bold text-primary-blue mb-0" id="displayTotalPrice">Rp 0</h3>
                        </div>

                        <button type="submit" class="btn btn-primary-blue w-100 py-3 fw-bold fs-5 shadow">
                            <i class="bi bi-send-check-fill me-2"></i> Kirim Pesanan Air
                        </button>
                        <small class="text-muted text-center d-block mt-2" style="font-size: 0.72rem;">
                            Pesanan akan ditinjau Admin dan Surat Izin Jalan langsung diterbitkan saat disetujui.
                        </small>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const depotLat = {{ (float) $tariff->depot_latitude }};
    const depotLng = {{ (float) $tariff->depot_longitude }};
    const waterRate = {{ (float) $tariff->water_price_per_liter }};
    const deliveryRate = {{ (float) $tariff->delivery_price_per_km }};
    const baseFee = {{ (float) $tariff->base_fee }};

    let initialLat = parseFloat(document.getElementById('latitude').value) || -6.200000;
    let initialLng = parseFloat(document.getElementById('longitude').value) || 106.816666;

    // Initialize Leaflet Map
    const map = L.map('orderMap').setView([initialLat, initialLng], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Depot Marker
    const depotIcon = L.divIcon({
        className: 'custom-depot-marker',
        html: '<div style="background-color: #0B3D62; color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"><i class="bi bi-building-fill" style="font-size: 14px;"></i></div>',
        iconSize: [28, 28],
        iconAnchor: [14, 14]
    });
    L.marker([depotLat, depotLng], { icon: depotIcon }).addTo(map).bindPopup('<strong>Depo Utama Tirta Mandiri</strong>');

    // Customer Target Pin
    const targetIcon = L.divIcon({
        className: 'custom-target-marker',
        html: '<div style="background-color: #E85D75; color: white; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 3px 8px rgba(0,0,0,0.3);"><i class="bi bi-geo-alt-fill" style="font-size: 16px;"></i></div>',
        iconSize: [32, 32],
        iconAnchor: [16, 32]
    });

    let marker = L.marker([initialLat, initialLng], { draggable: true, icon: targetIcon }).addTo(map);

    function updateCoordinates(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(7);
        document.getElementById('longitude').value = lng.toFixed(7);
        calculatePrice();
    }

    marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        updateCoordinates(pos.lat, pos.lng);
    });

    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });

    // Button My Location (Geolocation API)
    document.getElementById('btnMyLocation').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                map.setView([lat, lng], 14);
                marker.setLatLng([lat, lng]);
                updateCoordinates(lat, lng);
            }, function(err) {
                alert('Gagal mengambil lokasi perangkat: ' + err.message);
            });
        } else {
            alert('Geolocation API tidak didukung pada browser ini.');
        }
    });

    function setVolume(val) {
        document.getElementById('volumeInput').value = val;
        calculatePrice();
    }

    document.getElementById('volumeInput').addEventListener('input', calculatePrice);

    // Fleet recommendation logic matching business rules
    function determineFleet(volume) {
        if (volume <= 7000) {
            return {
                category: 'Tangki 7.000 L',
                detail: 'Armada tangki terkecil untuk kapasitas s/d 7.000 Liter (1 Trip)',
                trips: 1
            };
        } else if (volume <= 8000) {
            return {
                category: 'Tangki 8.000 L',
                detail: 'Armada tangki sedang kapasitas 7.001 - 8.000 Liter (1 Trip)',
                trips: 1
            };
        } else if (volume <= 16000) {
            return {
                category: 'Tangki 16.000 L',
                detail: 'Armada tangki besar kapasitas 8.001 - 16.000 Liter (1 Trip)',
                trips: 1
            };
        } else if (volume <= 20000) {
            return {
                category: 'Tangki 20.000 L',
                detail: 'Armada tangki ekstra besar kapasitas 16.001 - 20.000 Liter (1 Trip)',
                trips: 1
            };
        } else {
            const trips = Math.ceil(volume / 20000);
            return {
                category: `XXL / Beberapa Trip (${trips} Trip)`,
                detail: `Melebihi kapasitas tangki tunggal (20.000 L). Dikirim menggunakan ${trips} Trip / Armada gabungan.`,
                trips: trips
            };
        }
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return Math.round((R * c) * 10) / 10;
    }

    function calculatePrice() {
        const volume = parseInt(document.getElementById('volumeInput').value) || 0;
        const lat = parseFloat(document.getElementById('latitude').value) || depotLat;
        const lng = parseFloat(document.getElementById('longitude').value) || depotLng;

        const distance = calculateDistance(depotLat, depotLng, lat, lng);
        document.getElementById('distanceDisplay').innerHTML = `<i class="bi bi-signpost-2 me-1"></i> Estimasi Jarak dari Depo: <strong>${distance} km</strong>`;

        const fleet = determineFleet(volume);
        document.getElementById('fleetCategoryText').innerText = fleet.category;
        document.getElementById('fleetDetailText').innerText = fleet.detail;

        const waterPrice = volume * waterRate;
        const deliveryPrice = baseFee + (distance * deliveryRate * fleet.trips);
        const totalPrice = waterPrice + deliveryPrice;

        document.getElementById('displayWaterPrice').innerText = 'Rp ' + waterPrice.toLocaleString('id-ID');
        document.getElementById('displayDeliveryPrice').innerText = 'Rp ' + deliveryPrice.toLocaleString('id-ID');
        document.getElementById('displayTotalPrice').innerText = 'Rp ' + totalPrice.toLocaleString('id-ID');
    }

    // Initial calculation on load
    calculatePrice();
</script>
@endpush

