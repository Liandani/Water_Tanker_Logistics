@extends('layouts.admin')

@section('title', 'Detail Pelanggan ' . $customer->company_name)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <a href="{{ route('admin.customers.index') }}" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left"></i> Kembali ke Data Pelanggan
        </a>
        <h3 class="fw-bold text-dark-blue mt-1 mb-0">{{ $customer->company_name }}</h3>
        <span class="badge bg-light text-primary-blue border">{{ $customer->business_category }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i> Edit Data
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-5">
        <div class="card card-fresh p-4 h-100">
            <h6 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">Informasi Profil Perusahaan</h6>
            <div class="mb-2">
                <span class="text-muted small d-block">Penanggung Jawab (PIC):</span>
                <strong class="text-dark">{{ $customer->contact_person }}</strong>
            </div>
            <div class="mb-2">
                <span class="text-muted small d-block">Email Akun:</span>
                <span class="text-dark">{{ $customer->user->email }}</span>
            </div>
            <div class="mb-2">
                <span class="text-muted small d-block">Nomor Telepon / WhatsApp:</span>
                <span class="text-dark">{{ $customer->user->phone ?? '-' }}</span>
            </div>
            <div class="mb-2">
                <span class="text-muted small d-block">Kategori Bisnis:</span>
                <span class="badge bg-light-blue text-primary-blue">{{ $customer->business_category }}</span>
            </div>
            <div class="mb-3">
                <span class="text-muted small d-block">Alamat Pengiriman:</span>
                <span class="text-secondary small">{{ $customer->address }}</span>
            </div>
            <div class="pt-3 border-top mt-auto small text-muted">
                Bergabung sejak: {{ $customer->created_at->format('d F Y') }}
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card card-fresh p-4 h-100">
            <h6 class="fw-bold text-dark-blue mb-3 pb-2 border-bottom">Riwayat Pesanan Pelanggan Ini</h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead>
                        <tr class="small text-muted">
                            <th>No. Order</th>
                            <th>Volume</th>
                            <th>Tgl Kirim</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->orders as $ord)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $ord->id) }}" class="fw-bold text-primary-blue text-decoration-none">
                                        {{ $ord->order_number }}
                                    </a>
                                </td>
                                <td>{{ number_format($ord->volume, 0, ',', '.') }} L</td>
                                <td>{{ $ord->delivery_date->format('d/m/Y') }}</td>
                                <td>Rp {{ number_format($ord->total_price, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $ord->status === 'Selesai' ? 'badge-selesai' : 'badge-pending' }}">{{ $ord->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">Belum ada pesanan dari pelanggan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

