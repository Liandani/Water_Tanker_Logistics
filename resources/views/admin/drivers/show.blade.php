@extends('layouts.admin')

@section('title', 'Detail Driver ' . $driver->user->name)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <a href="{{ route('admin.drivers.index') }}" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left"></i> Kembali ke Data Driver
        </a>
        <div class="d-flex align-items-center gap-2 mt-1">
            <h3 class="fw-bold text-dark-blue mb-0">{{ $driver->user->name }}</h3>
            @if($driver->verification_status === 'verified')
                <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i> Verified</span>
            @elseif($driver->verification_status === 'pending')
                <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> Pending Verification</span>
            @else
                <span class="badge bg-danger">Rejected</span>
            @endif
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.drivers.edit', $driver->id) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i> Edit Data
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Driver Profile Information -->
    <div class="col-md-4">
        <div class="card card-fresh p-4 h-100">
            <div class="text-center mb-3">
                <div class="rounded-circle bg-primary-blue text-white d-inline-flex align-items-center justify-content-center fw-bold fs-2 shadow-sm mb-2" style="width: 64px; height: 64px;">
                    {{ substr($driver->user->name, 0, 1) }}
                </div>
                <h5 class="fw-bold text-dark mb-0">{{ $driver->user->name }}</h5>
                <small class="text-muted">{{ $driver->user->email }}</small>
            </div>

            <div class="p-3 rounded-3 bg-light text-center mb-3">
                <span class="small text-muted d-block">Rating Pelanggan:</span>
                <div class="d-flex justify-content-center align-items-center gap-2">
                    <span class="fs-4 fw-bold text-warning">{{ $driver->averageRating() }}</span>
                    <div class="text-warning fs-5">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= round($driver->averageRating()) ? '-fill' : '' }}"></i>
                        @endfor
                    </div>
                    <small class="text-muted">({{ $driver->ratings->count() }})</small>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-muted small d-block">Nomor WhatsApp:</span>
                <strong class="text-dark">{{ $driver->user->phone ?? '-' }}</strong>
            </div>
            <div class="mb-2">
                <span class="text-muted small d-block">Nomor SIM:</span>
                <span class="badge bg-light text-dark border">{{ $driver->license_number ?? 'Belum Diisi' }}</span>
            </div>
            <div class="mb-2">
                <span class="text-muted small d-block">Nomor KTP:</span>
                <span class="text-dark">{{ $driver->identity_number ?? '-' }}</span>
            </div>
            <div class="mb-2">
                <span class="text-muted small d-block">Kesiapan Kerja:</span>
                <span class="badge {{ $driver->availability_status === 'Aktif' ? 'bg-success' : ($driver->availability_status === 'On Delivery' ? 'bg-primary' : 'bg-secondary') }}">
                    {{ $driver->availability_status }}
                </span>
            </div>
            <div class="pt-3 border-top mt-auto small text-muted">
                Terdaftar: {{ $driver->created_at->format('d F Y') }}
            </div>
        </div>
    </div>

    <!-- Assignment History -->
    <div class="col-md-8">
        <div class="card card-fresh p-4 h-100">
            <h6 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">Riwayat Penugasan Pengiriman</h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead>
                        <tr class="small text-muted">
                            <th>No. Order</th>
                            <th>Pelanggan</th>
                            <th>Armada</th>
                            <th>Volume</th>
                            <th>Status Job</th>
                            <th>Tgl Ditugaskan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($driver->assignments as $a)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $a->order_id) }}" class="fw-bold text-primary-blue text-decoration-none">
                                        {{ $a->order->order_number }}
                                    </a>
                                </td>
                                <td>{{ $a->order->customer->company_name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $a->vehicle->plate_number }}</span></td>
                                <td>{{ number_format($a->order->volume, 0, ',', '.') }} L</td>
                                <td>
                                    <span class="badge {{ $a->status === 'Completed' ? 'badge-selesai' : ($a->status === 'In Transit' ? 'badge-dikirim' : 'badge-pending') }}">
                                        {{ $a->status }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $a->assigned_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted small">Belum ada riwayat penugasan untuk driver ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Customer Reviews for this Driver -->
            <h6 class="fw-bold text-dark-blue mt-4 mb-3 pb-2 border-bottom">Ulasan Pelanggan Terhadap Driver</h6>
            <div class="d-flex flex-column gap-2">
                @forelse($driver->ratings as $r)
                    <div class="p-2 rounded bg-light border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="small text-dark">{{ $r->customer->company_name }}</strong>
                            <div class="text-warning small">
                                @for($i = 1; $i <= $r->rating; $i++)
                                    <i class="bi bi-star-fill"></i>
                                @endfor
                                <span class="text-dark fw-bold ms-1">{{ $r->rating }} / 5</span>
                            </div>
                        </div>
                        <p class="small text-secondary mb-0 fst-italic">"{{ $r->review }}"</p>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted small">Belum ada ulasan yang masuk.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

