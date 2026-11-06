@extends('layouts.customer')

@section('title', 'Dashboard Pelanggan')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h3 class="fw-bold text-dark-blue mb-1">Selamat Datang, {{ $customer->company_name }}</h3>
        <p class="text-muted small mb-0">
            Penanggung Jawab: <strong>{{ $customer->contact_person }}</strong> &bull; Kategori: <span class="badge bg-light text-primary-blue border">{{ $customer->business_category }}</span>
        </p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <a href="{{ route('customer.orders.create') }}" class="btn btn-primary-blue shadow-sm px-3 py-2 fw-bold">
            <i class="bi bi-plus-circle-fill me-1"></i> Pesan Air Tangki Baru
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Pesanan</span>
                    <h3 class="fw-bold text-dark-blue my-1">{{ $totalOrders }}</h3>
                    <small class="text-primary-blue fw-semibold">Sepanjang waktu</small>
                </div>
                <div class="stat-icon bg-light-blue text-primary-blue">
                    <i class="bi bi-cart4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Sedang Diproses</span>
                    <h3 class="fw-bold text-dark-blue my-1">{{ $processingOrders }}</h3>
                    <small class="text-warning fw-semibold">Verifikasi & armada</small>
                </div>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card aqua">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Sedang Dikirim</span>
                    <h3 class="fw-bold text-dark-blue my-1">{{ $inTransitOrders }}</h3>
                    <small class="text-info fw-semibold">Truk dalam perjalanan</small>
                </div>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="bi bi-truck"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Pesanan Selesai</span>
                    <h3 class="fw-bold text-dark-blue my-1">{{ $completedOrders }}</h3>
                    <small class="text-success fw-semibold">Pengisian berhasil</small>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-check2-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Active Delivery Alert if any -->
@php
    $activeDelivery = $recentOrders->where('status', 'Dikirim')->first();
@endphp
@if($activeDelivery)
    <div class="card card-fresh border-0 mb-4 shadow-sm" style="background: linear-gradient(135deg, #0B3D62, #1687D9); color: white;">
        <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-aqua text-dark fw-bold mb-2">PENGIRIMAN SEDANG BERLANGSUNG</span>
                <h5 class="fw-bold mb-1">Pesanan #{{ $activeDelivery->order_number }} sedang menuju lokasi Anda!</h5>
                <p class="mb-0 text-light opacity-75 small">
                    Driver: <strong>{{ $activeDelivery->activeAssignment->driver->user->name ?? 'Ditugaskan' }}</strong> &bull; Truk Tangki: <strong>{{ $activeDelivery->activeAssignment->vehicle->plate_number ?? '-' }}</strong> ({{ number_format($activeDelivery->volume, 0, ',', '.') }} Liter)
                </p>
            </div>
            <div>
                <a href="{{ route('customer.orders.tracking', $activeDelivery->id) }}" class="btn btn-aqua fw-bold px-4 py-2 text-nowrap shadow">
                    <i class="bi bi-geo-alt-fill me-1"></i> Buka Live Tracking Peta
                </a>
            </div>
        </div>
    </div>
@endif

<!-- Recent Orders Table -->
<div class="card card-fresh">
    <div class="card-header-fresh d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold text-dark-blue mb-0">Riwayat Pemesanan Terbaru</h5>
            <small class="text-muted">Pantau status distribusi pasokan air tangki Anda</small>
        </div>
        <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-primary-blue btn-sm fw-semibold">
            Lihat Semua Pesanan <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-fresh align-middle mb-0">
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Tanggal Kirim</th>
                    <th>Volume Air</th>
                    <th>Rekomendasi Armada</th>
                    <th>Total Biaya</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('customer.orders.show', $order->id) }}" class="fw-bold text-decoration-none text-primary-blue">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td>
                            <i class="bi bi-calendar3 me-1 text-muted"></i>
                            {{ $order->delivery_date->format('d/m/Y') }}
                        </td>
                        <td>
                            <span class="fw-bold text-dark-blue">{{ number_format($order->volume, 0, ',', '.') }}</span> Liter
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $order->fleet_category_recommendation }}</span>
                        </td>
                        <td class="fw-bold text-dark-blue">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($order->status === 'Pending')
                                <span class="badge badge-pending px-3 py-2 rounded-pill"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                            @elseif($order->status === 'Diproses')
                                <span class="badge badge-diproses px-3 py-2 rounded-pill"><i class="bi bi-gear-wide-connected me-1"></i> Diproses</span>
                            @elseif($order->status === 'Dikirim')
                                <span class="badge badge-dikirim px-3 py-2 rounded-pill"><i class="bi bi-truck me-1"></i> Dikirim</span>
                            @elseif($order->status === 'Selesai')
                                <span class="badge badge-selesai px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Selesai</span>
                            @elseif($order->status === 'Pending Review')
                                <span class="badge badge-kendala px-3 py-2 rounded-pill"><i class="bi bi-exclamation-triangle-fill me-1"></i> Kendala</span>
                            @else
                                <span class="badge badge-dibatalkan px-3 py-2 rounded-pill"><i class="bi bi-x-circle-fill me-1"></i> {{ $order->status }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-outline-secondary" title="Detail Pesanan">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($order->status === 'Dikirim')
                                    <a href="{{ route('customer.orders.tracking', $order->id) }}" class="btn btn-primary-blue" title="Live Tracking">
                                        <i class="bi bi-geo-alt"></i>
                                    </a>
                                @endif
                                <a href="{{ route('customer.orders.invoice', $order->id) }}" class="btn btn-outline-info" title="Invoice">
                                    <i class="bi bi-receipt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            Belum ada riwayat pesanan. Silakan buat pesanan air tangki pertama Anda!
                            <div class="mt-3">
                                <a href="{{ route('customer.orders.create') }}" class="btn btn-primary-blue btn-sm">
                                    <i class="bi bi-plus-circle me-1"></i> Buat Pesanan Baru
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

