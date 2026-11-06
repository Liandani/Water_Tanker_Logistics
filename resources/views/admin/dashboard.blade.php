@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark-blue mb-1">Dashboard Operasional & Pemantauan</h3>
        <p class="text-muted small mb-0">Overview sistem manajemen pemesanan, armada truk tangki, dan keuangan</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.monitoring.maps') }}" class="btn btn-outline-primary-blue shadow-sm">
            <i class="bi bi-geo-alt-fill me-1"></i> Monitoring Peta Armada
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'Pending']) }}" class="btn btn-primary-blue shadow-sm position-relative">
            <i class="bi bi-check2-circle me-1"></i> Validasi Pesanan
            @if($pendingOrders > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $pendingOrders }}
                </span>
            @endif
        </a>
    </div>
</div>

<!-- Order Status Metrics (Top Row) -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card primary">
            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Order Hari Ini</span>
            <h3 class="fw-bold text-dark-blue my-1">{{ $todayOrders }}</h3>
            <small class="text-primary-blue fw-semibold" style="font-size: 0.72rem;">Pesanan baru</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card warning">
            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Pending</span>
            <h3 class="fw-bold text-warning my-1">{{ $pendingOrders }}</h3>
            <small class="text-muted" style="font-size: 0.72rem;">Menunggu approval</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card aqua">
            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Diproses</span>
            <h3 class="fw-bold text-dark-blue my-1">{{ $processingOrders }}</h3>
            <small class="text-info fw-semibold" style="font-size: 0.72rem;">Armada disiapkan</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card primary">
            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Sedang Dikirim</span>
            <h3 class="fw-bold text-primary-blue my-1">{{ $inTransitOrders }}</h3>
            <small class="text-primary fw-semibold" style="font-size: 0.72rem;">Live di jalan</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card success">
            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Selesai</span>
            <h3 class="fw-bold text-success my-1">{{ $completedOrders }}</h3>
            <small class="text-success fw-semibold" style="font-size: 0.72rem;">Pengisian sukses</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="stat-card danger">
            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Kendala Lapangan</span>
            <h3 class="fw-bold text-danger my-1">{{ $pendingReviewOrders }}</h3>
            <small class="text-danger fw-semibold" style="font-size: 0.72rem;">Pending review</small>
        </div>
    </div>
</div>

<!-- Financial & Fleet Metrics (Second Row) -->
<div class="row g-3 mb-4">
    <!-- Financial Summary -->
    <div class="col-md-4">
        <div class="card card-fresh p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="small text-muted fw-bold text-uppercase">Finansial Bulan Ini</span>
                <span class="badge bg-light-blue text-primary-blue">{{ date('F Y') }}</span>
            </div>

            <div class="mb-3">
                <span class="small text-muted d-block">Pemasukan (Penjualan Air Selesai):</span>
                <h4 class="fw-bold text-success mb-0">+ Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}</h4>
            </div>

            <div class="mb-3">
                <span class="small text-muted d-block">Pengeluaran (Operasional, Solar, Servis):</span>
                <h5 class="fw-bold text-danger mb-0">- Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}</h5>
            </div>

            <div class="pt-3 border-top mt-auto">
                <span class="small text-muted d-block">Laba Bersih Operasional:</span>
                <h3 class="fw-bold {{ $netProfitThisMonth >= 0 ? 'text-primary-blue' : 'text-danger' }} mb-0">
                    Rp {{ number_format($netProfitThisMonth, 0, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>

    <!-- Fleet Status Counters -->
    <div class="col-md-8">
        <div class="card card-fresh p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <h6 class="fw-bold text-dark-blue mb-0">Status Kesiapan Armada & Driver</h6>
                <a href="{{ route('admin.vehicles.index') }}" class="small text-decoration-none text-primary-blue fw-semibold">Kelola Armada</a>
            </div>

            <div class="row g-3 text-center">
                <div class="col-sm-3 col-6">
                    <div class="p-3 rounded-3 bg-light border">
                        <span class="stat-icon bg-success-subtle text-success mx-auto mb-2">
                            <i class="bi bi-truck"></i>
                        </span>
                        <h4 class="fw-bold text-success mb-1">{{ $readyVehicles }}</h4>
                        <span class="small fw-semibold text-dark">Armada Ready</span>
                        <small class="text-muted d-block" style="font-size: 0.7rem;">Siap ditugaskan</small>
                    </div>
                </div>
                <div class="col-sm-3 col-6">
                    <div class="p-3 rounded-3 bg-light border">
                        <span class="stat-icon bg-primary-subtle text-primary mx-auto mb-2">
                            <i class="bi bi-geo-alt-fill"></i>
                        </span>
                        <h4 class="fw-bold text-primary mb-1">{{ $onDutyVehicles }}</h4>
                        <span class="small fw-semibold text-dark">Armada On Duty</span>
                        <small class="text-muted d-block" style="font-size: 0.7rem;">Sedang bertugas</small>
                    </div>
                </div>
                <div class="col-sm-3 col-6">
                    <div class="p-3 rounded-3 bg-light border">
                        <span class="stat-icon bg-danger-subtle text-danger mx-auto mb-2">
                            <i class="bi bi-tools"></i>
                        </span>
                        <h4 class="fw-bold text-danger mb-1">{{ $maintenanceVehicles }}</h4>
                        <span class="small fw-semibold text-dark">Maintenance</span>
                        <small class="text-muted d-block" style="font-size: 0.7rem;">Dalam perbaikan</small>
                    </div>
                </div>
                <div class="col-sm-3 col-6">
                    <div class="p-3 rounded-3 bg-light border">
                        <span class="stat-icon bg-info-subtle text-info mx-auto mb-2">
                            <i class="bi bi-person-badge-fill"></i>
                        </span>
                        <h4 class="fw-bold text-info mb-1">{{ $activeDrivers }}</h4>
                        <span class="small fw-semibold text-dark">Driver Aktif</span>
                        <small class="text-muted d-block" style="font-size: 0.7rem;">Siap jalan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Graphs Row -->
<div class="row g-4 mb-4">
    <!-- Chart 1: Pemasukan & Pengeluaran -->
    <div class="col-lg-8">
        <div class="card card-fresh p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-bold text-dark-blue mb-0">Tren Finansial: Pemasukan vs Pengeluaran</h6>
                    <small class="text-muted">Grafik 6 bulan terakhir</small>
                </div>
                <a href="{{ route('admin.financial.reports') }}" class="btn btn-outline-primary-blue btn-sm">Lihat Laporan</a>
            </div>
            <div style="height: 280px;">
                <canvas id="financeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart 2: Status Pengiriman & Armada -->
    <div class="col-lg-4">
        <div class="card card-fresh p-4 h-100">
            <h6 class="fw-bold text-dark-blue mb-1">Distribusi Status Pesanan</h6>
            <small class="text-muted mb-3 d-block">Komposisi status pesanan aktif & selesai</small>
            <div style="height: 250px;" class="d-flex align-items-center justify-content-center">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="card card-fresh">
    <div class="card-header-fresh d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold text-dark-blue mb-0">Pesanan Masuk & Operasional Terbaru</h5>
            <small class="text-muted">Kelola persetujuan, penugasan armada, dan pantau pengiriman</small>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-primary-blue btn-sm fw-semibold">
            Kelola Seluruh Pesanan <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-fresh align-middle mb-0">
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Nama Pelanggan</th>
                    <th>Volume</th>
                    <th>Tgl Kirim</th>
                    <th>Rekomendasi</th>
                    <th>Driver & Armada</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-decoration-none text-primary-blue">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td>
                            <strong class="text-dark">{{ $order->customer->company_name }}</strong>
                            <small class="text-muted d-block">{{ $order->customer->contact_person }}</small>
                        </td>
                        <td>
                            <span class="fw-bold text-dark-blue">{{ number_format($order->volume, 0, ',', '.') }}</span> L
                        </td>
                        <td>{{ $order->delivery_date->format('d/m/Y') }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $order->fleet_category_recommendation }}</span></td>
                        <td>
                            @if($order->activeAssignment && $order->activeAssignment->driver)
                                <div class="small fw-semibold text-dark">{{ $order->activeAssignment->driver->user->name }}</div>
                                <span class="badge bg-info-subtle text-info border" style="font-size: 0.7rem;">{{ $order->activeAssignment->vehicle->plate_number }}</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary small">Belum Ditugaskan</span>
                            @endif
                        </td>
                        <td class="fw-bold text-dark-blue">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td>
                            @if($order->status === 'Pending')
                                <span class="badge badge-pending px-2 py-1">Pending</span>
                            @elseif($order->status === 'Diproses')
                                <span class="badge badge-diproses px-2 py-1">Diproses</span>
                            @elseif($order->status === 'Dikirim')
                                <span class="badge badge-dikirim px-2 py-1">Dikirim</span>
                            @elseif($order->status === 'Selesai')
                                <span class="badge badge-selesai px-2 py-1">Selesai</span>
                            @elseif($order->status === 'Pending Review')
                                <span class="badge badge-kendala px-2 py-1">Kendala</span>
                            @else
                                <span class="badge badge-dibatalkan px-2 py-1">{{ $order->status }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                Kelola <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Belum ada pesanan masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Chart 1: Pemasukan & Pengeluaran
    const ctxFinance = document.getElementById('financeChart').getContext('2d');
    new Chart(ctxFinance, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [
                {
                    label: 'Pemasukan (Rp)',
                    data: {!! json_encode($incomeData) !!},
                    backgroundColor: 'rgba(32, 180, 134, 0.8)',
                    borderColor: '#20B486',
                    borderWidth: 1,
                    borderRadius: 6
                },
                {
                    label: 'Pengeluaran (Rp)',
                    data: {!! json_encode($expenseData) !!},
                    backgroundColor: 'rgba(232, 93, 117, 0.8)',
                    borderColor: '#E85D75',
                    borderWidth: 1,
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000) + ' Jt';
                        }
                    }
                }
            }
        }
    });

    // Chart 2: Order Status Doughnut
    const ctxStatus = document.getElementById('orderStatusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Diproses', 'Dikirim', 'Selesai', 'Kendala'],
            datasets: [{
                data: [
                    {{ $pendingOrders }},
                    {{ $processingOrders }},
                    {{ $inTransitOrders }},
                    {{ $completedOrders }},
                    {{ $pendingReviewOrders }}
                ],
                backgroundColor: [
                    '#F5B942', // Pending
                    '#1687D9', // Diproses
                    '#55C9D9', // Dikirim
                    '#20B486', // Selesai
                    '#E85D75'  // Kendala
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endpush

