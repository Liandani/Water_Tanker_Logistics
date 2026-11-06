@extends('layouts.admin')

@section('title', 'Daftar Surat Izin Jalan Digital')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark-blue mb-1">Surat Izin Jalan (Digital)</h3>
        <p class="text-muted small mb-0">Arsip dokumen digital Surat Izin Jalan armada truk tangki yang diterbitkan secara otomatis</p>
    </div>
</div>

<!-- Search Card -->
<div class="card card-fresh p-3 mb-4">
    <form method="GET" action="{{ route('admin.surat-jalan.index') }}" class="row g-2">
        <div class="col-md-9">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari Nomor Surat, No. Order, Pelanggan, Plat Truk..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary-blue flex-grow-1">Cari Dokumen</button>
            @if(request()->has('search'))
                <a href="{{ route('admin.surat-jalan.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                    <th>Nomor Surat Jalan</th>
                    <th>Nomor Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Driver & Truk Tangki</th>
                    <th>Volume Muatan</th>
                    <th>Tanggal Terbit</th>
                    <th>Revisi</th>
                    <th class="text-end">Aksi Dokumen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td>
                            <a href="{{ route('admin.surat-jalan.show', $doc->id) }}" class="fw-bold text-primary-blue text-decoration-none">
                                <i class="bi bi-file-earmark-text-fill me-1"></i> {{ $doc->document_number }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $doc->order_id) }}" class="text-secondary small fw-semibold text-decoration-none">
                                {{ $doc->order->order_number }}
                            </a>
                        </td>
                        <td>
                            <strong class="text-dark small">{{ $doc->order->customer->company_name }}</strong>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $doc->assignment->driver->user->name }}</div>
                            <span class="badge bg-light text-dark border">{{ $doc->assignment->vehicle->plate_number }}</span>
                        </td>
                        <td>
                            <span class="fw-bold text-dark-blue">{{ number_format($doc->order->volume, 0, ',', '.') }}</span> L
                        </td>
                        <td class="small text-muted">{{ $doc->issued_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge {{ $doc->revision > 1 ? 'bg-warning text-dark' : 'bg-secondary-subtle text-secondary' }}">Rev. {{ $doc->revision }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.surat-jalan.show', $doc->id) }}" class="btn btn-outline-primary" title="Preview">
                                    <i class="bi bi-eye"></i> Preview
                                </a>
                                <a href="{{ route('admin.surat-jalan.pdf', $doc->id) }}" class="btn btn-outline-danger" title="Download PDF">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                                <a href="{{ route('admin.surat-jalan.print', $doc->id) }}" target="_blank" class="btn btn-outline-secondary" title="Print">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">Belum ada Surat Izin Jalan yang diterbitkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($documents->hasPages())
        <div class="card-footer bg-white border-top p-3">
            {{ $documents->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

