@extends('layouts.customer')

@section('title', 'Detail Pesanan ' . $order->order_number)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <a href="{{ route('customer.orders.index') }}" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left"></i> Kembali ke Riwayat Pesanan
        </a>
        <div class="d-flex align-items-center gap-2 mt-1">
            <h3 class="fw-bold text-dark-blue mb-0">Pesanan #{{ $order->order_number }}</h3>
            @if($order->status === 'Pending')
                <span class="badge badge-pending px-3 py-2 rounded-pill"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
            @elseif($order->status === 'Diproses')
                <span class="badge badge-diproses px-3 py-2 rounded-pill"><i class="bi bi-gear-fill me-1"></i> Diproses</span>
            @elseif($order->status === 'Dikirim')
                <span class="badge badge-dikirim px-3 py-2 rounded-pill"><i class="bi bi-truck me-1"></i> Sedang Dikirim</span>
            @elseif($order->status === 'Selesai')
                <span class="badge badge-selesai px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Selesai</span>
            @elseif($order->status === 'Pending Review')
                <span class="badge badge-kendala px-3 py-2 rounded-pill"><i class="bi bi-exclamation-triangle-fill me-1"></i> Kendala di Lapangan</span>
            @else
                <span class="badge badge-dibatalkan px-3 py-2 rounded-pill"><i class="bi bi-x-circle-fill me-1"></i> {{ $order->status }}</span>
            @endif
        </div>
    </div>
    <div class="d-flex gap-2">
        @if($order->status === 'Dikirim')
            <a href="{{ route('customer.orders.tracking', $order->id) }}" class="btn btn-primary-blue shadow-sm">
                <i class="bi bi-geo-alt-fill me-1"></i> Live Tracking Maps
            </a>
        @endif
        <a href="{{ route('customer.orders.invoice', $order->id) }}" class="btn btn-outline-primary-blue">
            <i class="bi bi-receipt me-1"></i> Lihat Invoice
        </a>
    </div>
</div>

<!-- Reason Alerts if Pending, Pending Review or Rejected -->
@if($order->pending_reason)
    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4" style="background-color: #FEF9EE; border-left: 4px solid #F5B942 !important;">
        <i class="bi bi-exclamation-circle-fill fs-4 me-3 text-warning"></i>
        <div>
            <strong>Catatan Pending / Kendala Operasional:</strong>
            <p class="mb-0 small text-dark mt-1">{{ $order->pending_reason }}</p>
        </div>
    </div>
@endif

@if($order->rejection_reason)
    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4" style="background-color: #FDF2F4; border-left: 4px solid #E85D75 !important;">
        <i class="bi bi-x-circle-fill fs-4 me-3 text-danger"></i>
        <div>
            <strong>Alasan Penolakan Pesanan:</strong>
            <p class="mb-0 small text-dark mt-1">{{ $order->rejection_reason }}</p>
        </div>
    </div>
@endif

<div class="row g-4">
    <!-- Left Column: Details -->
    <div class="col-lg-8">
        <!-- Progress Steps -->
        <div class="card card-fresh p-4 mb-4">
            <h6 class="fw-bold text-dark-blue mb-3">Status Distribusi Pasokan Air</h6>
            <div class="d-flex justify-content-between position-relative text-center">
                @php
                    $steps = [
                        ['label' => 'Pesanan Dibuat', 'done' => true, 'active' => $order->status === 'Pending'],
                        ['label' => 'Disetujui Admin', 'done' => in_array($order->status, ['Diproses', 'Dikirim', 'Selesai']), 'active' => $order->status === 'Diproses'],
                        ['label' => 'Dalam Pengiriman', 'done' => in_array($order->status, ['Dikirim', 'Selesai']), 'active' => $order->status === 'Dikirim'],
                        ['label' => 'Selesai & Diterima', 'done' => $order->status === 'Selesai', 'active' => $order->status === 'Selesai'],
                    ];
                @endphp
                @foreach($steps as $idx => $step)
                    <div style="flex: 1; z-index: 2;">
                        <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center fw-bold shadow-sm mb-2"
                             style="width: 38px; height: 38px; {{ $step['done'] ? 'background-color: #20B486; color: white;' : ($step['active'] ? 'background-color: #1687D9; color: white;' : 'background-color: #E2E8F0; color: #718096;') }}">
                            @if($step['done'])
                                <i class="bi bi-check-lg"></i>
                            @else
                                {{ $idx + 1 }}
                            @endif
                        </div>
                        <span class="small fw-semibold {{ $step['done'] ? 'text-dark' : 'text-muted' }}" style="font-size: 0.78rem;">
                            {{ $step['label'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Order Information Details -->
        <div class="card card-fresh p-4 mb-4">
            <h5 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">Informasi Pengiriman</h5>
            <div class="row g-3">
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Alamat Pengiriman:</span>
                    <strong class="text-dark">{{ $order->delivery_address }}</strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Tanggal Pengiriman:</span>
                    <strong class="text-dark">{{ $order->delivery_date->format('d F Y') }}</strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Penerima di Lokasi:</span>
                    <strong class="text-dark">{{ $order->recipient_name ?? '-' }}</strong>
                    <div class="small text-secondary">{{ $order->recipient_phone ?? '-' }}</div>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small d-block">Estimasi Jarak dari Depo:</span>
                    <strong class="text-primary-blue">{{ $order->estimated_distance }} km</strong>
                </div>
                <div class="col-12">
                    <span class="text-muted small d-block">Catatan Khusus:</span>
                    <span class="text-secondary">{{ $order->notes ?: 'Tidak ada catatan tambahan.' }}</span>
                </div>
            </div>
        </div>

        <!-- Proof of Delivery (if completed) -->
        @if($order->status === 'Selesai' && $order->activeAssignment && $order->activeAssignment->proof)
            @php $proof = $order->activeAssignment->proof; @endphp
            <div class="card card-fresh p-4 mb-4 border-success">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h5 class="fw-bold text-success mb-0">
                        <i class="bi bi-patch-check-fill me-2"></i> Bukti Pengiriman Selesai (Proof of Delivery)
                    </h5>
                    <small class="text-muted">Selesai pada: {{ $proof->completed_at->format('d/m/Y H:i') }}</small>
                </div>
                <div class="row g-3 align-items-center">
                    <div class="col-md-5 text-center">
                        <img src="{{ asset('storage/' . $proof->photo_path) }}" alt="Bukti Pengiriman" class="img-fluid rounded-3 shadow-sm border" style="max-height: 220px; object-fit: cover;">
                    </div>
                    <div class="col-md-7">
                        <div class="mb-2">
                            <span class="text-muted small d-block">Diterima Oleh:</span>
                            <strong class="text-dark">{{ $proof->recipient_name ?: $order->recipient_name }}</strong>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted small d-block">Catatan Driver:</span>
                            <span class="text-secondary">{{ $proof->notes ?: 'Pengisian air selesai sesuai pesanan.' }}</span>
                        </div>
                        <div class="small text-success fw-bold">
                            <i class="bi bi-shield-check me-1"></i> Terverifikasi oleh Driver Lapangan
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Driver Rating Section -->
        @if($order->status === 'Selesai')
            <div class="card card-fresh p-4 mb-4">
                <h5 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">
                    <i class="bi bi-star-fill text-warning me-2"></i> Penilaian & Ulasan Layanan Driver
                </h5>

                @if($order->rating)
                    <div class="p-3 rounded-3 bg-light">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="text-warning fs-5">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $order->rating->rating ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                            <span class="fw-bold text-dark">{{ $order->rating->rating }} / 5 Bintang</span>
                            <small class="text-muted ms-auto">{{ $order->rating->created_at->format('d/m/Y') }}</small>
                        </div>
                        <p class="text-secondary mb-0 fst-italic">"{{ $order->rating->review }}"</p>
                    </div>
                @else
                    <p class="small text-muted mb-3">Pesanan Anda telah selesai. Mohon luangkan waktu sejenak untuk memberikan ulasan atas keramahan, ketepatan waktu, dan kualitas pelayanan driver armada kami.</p>
                    <form action="{{ route('customer.orders.rate', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Rating Bintang (1 - 5) <span class="text-danger">*</span></label>
                            <select name="rating" class="form-select w-auto" required>
                                <option value="5" selected>⭐⭐⭐⭐⭐ (5 - Luar Biasa & Sangat Puas)</option>
                                <option value="4">⭐⭐⭐⭐ (4 - Bagus & Cepat)</option>
                                <option value="3">⭐⭐⭐ (3 - Cukup Baik)</option>
                                <option value="2">⭐⭐ (2 - Kurang Puas)</option>
                                <option value="1">⭐ (1 - Buruk / Kecewa)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="review" class="form-label fw-semibold small text-dark">Ulasan & Kritik / Saran</label>
                            <textarea name="review" id="review" class="form-control" rows="3" placeholder="Tuliskan ulasan Anda mengenai kecepatan pengiriman, kondisi air, atau keramahan driver..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning fw-bold px-4">
                            <i class="bi bi-send-fill me-1"></i> Kirimkan Penilaian
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    <!-- Right Column: Assigned Driver & Vehicle, Price Summary -->
    <div class="col-lg-4">
        <!-- Assigned Fleet Card -->
        <div class="card card-fresh p-4 mb-4">
            <h6 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">Armada & Driver Ditugaskan</h6>
            @if($order->activeAssignment && $order->activeAssignment->driver)
                @php
                    $assign = $order->activeAssignment;
                    $driverUser = $assign->driver->user;
                    $vehicle = $assign->vehicle;
                @endphp
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-primary-blue text-white d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 52px; height: 52px;">
                        {{ substr($driverUser->name, 0, 1) }}
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">{{ $driverUser->name }}</h6>
                        <small class="text-muted d-block">Driver Resmi Armada Tangki</small>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $driverUser->phone) }}" target="_blank" class="btn btn-sm btn-outline-success mt-1 py-0 px-2 fw-semibold" style="font-size: 0.75rem;">
                            <i class="bi bi-whatsapp"></i> Hubungi Driver
                        </a>
                    </div>
                </div>

                <div class="card bg-light border-0 p-3 small mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">No. Plat Truk:</span>
                        <strong class="text-dark">{{ $vehicle->plate_number }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Kapasitas Tangki:</span>
                        <strong class="text-dark">{{ number_format($vehicle->capacity, 0, ',', '.') }} Liter</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Tipe Kendaraan:</span>
                        <span class="text-dark">{{ $vehicle->vehicle_type }}</span>
                    </div>
                </div>

                @if($order->status === 'Dikirim')
                    <a href="{{ route('customer.orders.tracking', $order->id) }}" class="btn btn-primary-blue w-100 fw-bold py-2 shadow-sm">
                        <i class="bi bi-geo-alt-fill me-1"></i> Buka Live Tracking Peta
                    </a>
                @endif
            @else
                <div class="text-center py-4 text-muted small">
                    <i class="bi bi-clock-history fs-2 d-block mb-2 text-warning"></i>
                    Armada dan driver sedang dalam proses alokasi oleh Administrator operasional.
                </div>
            @endif
        </div>

        <!-- Price Breakdown -->
        <div class="card card-fresh p-4">
            <h6 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">Ringkasan Tagihan</h6>
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Volume Air:</span>
                <strong class="text-dark">{{ number_format($order->volume, 0, ',', '.') }} Liter</strong>
            </div>
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Tarif Air (Rp {{ number_format($order->water_price_rate, 0, ',', '.') }}/L):</span>
                <span class="text-dark">Rp {{ number_format($order->water_price, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Ongkos Kirim ({{ $order->estimated_distance }} km):</span>
                <span class="text-dark">Rp {{ number_format($order->delivery_price, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-3 small">
                <span class="text-muted">Status Pembayaran:</span>
                <span class="badge {{ $order->payment_status === 'Paid' ? 'bg-success' : ($order->payment_status === 'Down Payment' ? 'bg-warning text-dark' : 'bg-danger') }}">
                    {{ $order->payment_status }}
                </span>
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold text-dark-blue">Total Tagihan:</span>
                <h4 class="fw-bold text-primary-blue mb-0">Rp {{ number_format($order->total_price, 0, ',', '.') }}</h4>
            </div>
            <a href="{{ route('customer.orders.invoice', $order->id) }}" class="btn btn-outline-primary-blue w-100 btn-sm">
                <i class="bi bi-printer me-1"></i> Cetak / Unduh Invoice
            </a>
        </div>
    </div>
</div>
@endsection

