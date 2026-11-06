@extends('layouts.customer')

@section('title', 'Riwayat Pesanan Air')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark-blue mb-1">Riwayat Pesanan Air</h3>
        <p class="text-muted small mb-0">Daftar seluruh pesanan air tangki yang telah Anda ajukan</p>
    </div>
    <div>
        <a href="{{ route('customer.orders.create') }}" class="btn btn-primary-blue shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Buat Pesanan Baru
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card card-fresh p-3 mb-4">
    <form method="GET" action="{{ route('customer.orders.index') }}" class="row g-2">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari No. Pesanan atau Alamat..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-4">
            <select name="status" class="form-select">
                <option value="">Semua Status Pengiriman</option>
                <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending (Menunggu Persetujuan)</option>
                <option value="Diproses" {{ request('status') === 'Diproses' ? 'selected' : '' }}>Diproses (Armada Ditugaskan)</option>
                <option value="Dikirim" {{ request('status') === 'Dikirim' ? 'selected' : '' }}>Dikirim (Dalam Perjalanan)</option>
                <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="Pending Review" {{ request('status') === 'Pending Review' ? 'selected' : '' }}>Kendala / Pending Review</option>
                <option value="Dibatalkan" {{ request('status') === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary-blue flex-grow-1">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card card-fresh">
    <div class="table-responsive">
        <table class="table table-fresh align-middle mb-0">
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Tanggal Kirim</th>
                    <th>Volume</th>
                    <th>Armada</th>
                    <th>Total Harga</th>
                    <th>Status Pengiriman</th>
                    <th>Pembayaran</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('customer.orders.show', $order->id) }}" class="fw-bold text-decoration-none text-primary-blue">
                                {{ $order->order_number }}
                            </a>
                            <small class="text-muted d-block" style="font-size: 0.72rem;">Dibuat: {{ $order->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <i class="bi bi-calendar3 me-1 text-muted"></i>
                            {{ $order->delivery_date->format('d/m/Y') }}
                        </td>
                        <td>
                            <span class="fw-bold text-dark-blue">{{ number_format($order->volume, 0, ',', '.') }}</span> L
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $order->fleet_category_recommendation }}</span>
                            @if($order->activeAssignment && $order->activeAssignment->vehicle)
                                <small class="d-block text-secondary mt-1">Plat: <strong>{{ $order->activeAssignment->vehicle->plate_number }}</strong></small>
                            @endif
                        </td>
                        <td class="fw-bold text-dark-blue">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($order->status === 'Pending')
                                <span class="badge badge-pending px-3 py-2 rounded-pill"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                            @elseif($order->status === 'Diproses')
                                <span class="badge badge-diproses px-3 py-2 rounded-pill"><i class="bi bi-gear-fill me-1"></i> Diproses</span>
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
                        <td>
                            @if($order->payment_status === 'Paid')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Paid</span>
                            @elseif($order->payment_status === 'Down Payment')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Down Payment</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Unpaid</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-outline-secondary" title="Detail">
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
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            Tidak ada pesanan yang sesuai dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="card-footer bg-white border-top p-3">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

