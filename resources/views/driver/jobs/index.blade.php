@extends('layouts.driver')

@section('title', 'Daftar Tugas Pengiriman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-dark-blue mb-0">Daftar Tugas Pengiriman</h5>
    <span class="badge bg-primary-blue text-white rounded-pill px-3">{{ $jobs->total() }} Tugas</span>
</div>

<!-- Status Filters -->
<div class="d-flex gap-1 overflow-auto pb-2 mb-3" style="white-space: nowrap;">
    <a href="{{ route('driver.jobs.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary-blue text-white' : 'btn-light border text-muted' }} rounded-pill px-3">Semua</a>
    <a href="{{ route('driver.jobs.index', ['status' => 'Assigned']) }}" class="btn btn-sm {{ request('status') === 'Assigned' ? 'btn-primary-blue text-white' : 'btn-light border text-muted' }} rounded-pill px-3">Tugas Baru</a>
    <a href="{{ route('driver.jobs.index', ['status' => 'Accepted']) }}" class="btn btn-sm {{ request('status') === 'Accepted' ? 'btn-primary-blue text-white' : 'btn-light border text-muted' }} rounded-pill px-3">Diterima</a>
    <a href="{{ route('driver.jobs.index', ['status' => 'In Transit']) }}" class="btn btn-sm {{ request('status') === 'In Transit' ? 'btn-primary-blue text-white' : 'btn-light border text-muted' }} rounded-pill px-3">Di Jalan</a>
    <a href="{{ route('driver.jobs.index', ['status' => 'Completed']) }}" class="btn btn-sm {{ request('status') === 'Completed' ? 'btn-primary-blue text-white' : 'btn-light border text-muted' }} rounded-pill px-3">Selesai</a>
</div>

<!-- Jobs List -->
<div class="d-flex flex-column gap-3 mb-4">
    @forelse($jobs as $job)
        <div class="card driver-card p-3 bg-white">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge bg-light text-primary-blue border fw-bold" style="font-size: 0.72rem;">#{{ $job->order->order_number }}</span>
                    <h6 class="fw-bold text-dark-blue mt-1 mb-0">{{ $job->order->customer->company_name }}</h6>
                </div>
                <span class="badge {{ $job->status === 'Completed' ? 'badge-selesai' : ($job->status === 'In Transit' ? 'badge-dikirim' : ($job->status === 'Assigned' ? 'badge-pending' : 'badge-diproses')) }} px-2 py-1" style="font-size: 0.72rem;">
                    {{ $job->status }}
                </span>
            </div>

            <p class="small text-muted mb-2">
                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $job->order->delivery_address }}
            </p>

            <div class="p-2 rounded bg-light small mb-3 d-flex justify-content-between">
                <span>Muatan: <strong>{{ number_format($job->order->volume, 0, ',', '.') }} L</strong></span>
                <span>Truk: <strong>{{ $job->vehicle->plate_number }}</strong></span>
                <span>Tgl: <strong>{{ $job->order->delivery_date->format('d/m') }}</strong></span>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('driver.jobs.show', $job->id) }}" class="btn btn-primary-blue btn-sm flex-grow-1 fw-bold py-2">
                    <i class="bi bi-eye-fill me-1"></i> Detail Tugas
                </a>
                @if(in_array($job->status, ['Accepted', 'In Transit', 'Filling Water']))
                    <a href="{{ route('driver.jobs.navigation', $job->id) }}" class="btn btn-outline-primary btn-sm px-3 fw-bold" title="Navigasi">
                        <i class="bi bi-compass-fill"></i>
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div class="card driver-card p-4 bg-white text-center text-muted">
            <i class="bi bi-inbox fs-1 mb-2 text-secondary"></i>
            Tidak ada tugas pengiriman yang ditemukan.
        </div>
    @endforelse
</div>

@if($jobs->hasPages())
    <div class="d-flex justify-content-center">
        {{ $jobs->links('pagination::bootstrap-5') }}
    </div>
@endif
@endsection

