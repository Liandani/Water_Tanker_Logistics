@extends('layouts.admin')

@section('title', 'Manajemen Seluruh Pesanan')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark-blue mb-1">Manajemen Seluruh Pesanan Air Tangki</h3>
        <p class="text-muted small mb-0">Kelola persetujuan, penugasan armada, penerbitan Surat Jalan, dan pantau pengiriman</p>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card card-fresh p-3 mb-4">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari No. Pesanan, Pelanggan, Penerima..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-4">
            <select name="status" class="form-select">
                <option value="">Semua Status Pesanan</option>
                <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending (Menunggu Persetujuan)</option>
                <option value="Diproses" {{ request('status') === 'Diproses' ? 'selected' : '' }}>Diproses (Armada Ditugaskan)</option>
                <option value="Dikirim" {{ request('status') === 'Dikirim' ? 'selected' : '' }}>Dikirim (Dalam Perjalanan)</option>
                <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="Pending Review" {{ request('status') === 'Pending Review' ? 'selected' : '' }}>Pending Review (Kendala)</option>
                <option value="Dibatalkan" {{ request('status') === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary-blue flex-grow-1">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                    <th>Nomor Pesanan</th>
                    <th>Nama Pelanggan</th>
                    <th>Volume Air</th>
                    <th>Tanggal Pengiriman</th>
                    <th>Rekomendasi Armada</th>
                    <th>Total Biaya</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-decoration-none text-primary-blue">
                                {{ $order->order_number }}
                            </a>
                            <small class="text-muted d-block" style="font-size: 0.72rem;">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <strong class="text-dark">{{ $order->customer->company_name }}</strong>
                            <small class="text-muted d-block">{{ $order->customer->contact_person }}</small>
                        </td>
                        <td>
                            <span class="fw-bold text-dark-blue">{{ number_format($order->volume, 0, ',', '.') }}</span> Liter
                        </td>
                        <td>
                            <i class="bi bi-calendar3 me-1 text-muted"></i>
                            {{ $order->delivery_date->format('d/m/Y') }}
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
                        <td class="text-end">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm fw-bold">
                                Kelola Pesanan <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">Belum ada data pesanan yang sesuai filter.</td>
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

