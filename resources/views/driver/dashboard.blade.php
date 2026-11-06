@extends('layouts.driver')

@section('title', 'Dashboard Driver Mobile')

@section('content')
<!-- Driver Status Banner -->
<div class="card driver-card p-3 mb-3 bg-white shadow-sm border-0">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <span class="small text-muted d-block">Status Kesiapan Armada:</span>
            <span class="fw-bold fs-6 {{ $driver->availability_status === 'Aktif' ? 'text-success' : ($driver->availability_status === 'On Delivery' ? 'text-primary' : 'text-secondary') }}">
                <i class="bi bi-circle-fill me-1" style="font-size: 0.6rem;"></i>
                {{ $driver->availability_status }}
            </span>
        </div>
        <form action="{{ route('driver.toggleAvailability') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm {{ $driver->availability_status === 'Aktif' ? 'btn-outline-secondary' : 'btn-success' }} fw-bold px-3 py-2 rounded-pill">
                @if($driver->availability_status === 'Aktif')
                    <i class="bi bi-pause-circle me-1"></i> Set Libur
                @else
                    <i class="bi bi-play-circle me-1"></i> Aktif Bekerja
                @endif
            </button>
        </form>
    </div>
</div>

<!-- Quick Stats (Mobile Friendly) -->
<div class="row g-2 mb-3">
    <div class="col-6">
        <div class="card driver-card p-3 bg-white text-center">
            <span class="small text-muted fw-semibold d-block">Tugas Hari Ini</span>
            <h3 class="fw-bold text-dark-blue my-1">{{ $tasksToday }}</h3>
            <small class="text-secondary" style="font-size: 0.7rem;">Pengiriman terjadwal</small>
        </div>
    </div>
    <div class="col-6">
        <div class="card driver-card p-3 bg-white text-center">
            <span class="small text-muted fw-semibold d-block">Total Selesai</span>
            <h3 class="fw-bold text-success my-1">{{ $completedTotal }}</h3>
            <small class="text-success" style="font-size: 0.7rem;">Tugas terlaksana</small>
        </div>
    </div>
</div>

<!-- Active Ongoing Job (If any) -->
@if($activeJob)
    <div class="card driver-card p-3 mb-3 shadow" style="background: linear-gradient(135deg, #0B3D62, #1687D9); color: white;">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="badge bg-warning text-dark fw-bold">
                @if($activeJob->status === 'Assigned')
                    <i class="bi bi-bell-fill me-1"></i> TUGAS BARU MENUNGGU
                @elseif($activeJob->status === 'Accepted')
                    <i class="bi bi-check2-circle me-1"></i> TUGAS DITERIMA - SIAP
                @elseif($activeJob->status === 'In Transit')
                    <i class="bi bi-truck me-1"></i> DALAM PERJALANAN
                @elseif($activeJob->status === 'Filling Water')
                    <i class="bi bi-water me-1"></i> PENGISIAN AIR BERLANGSUNG
                @endif
            </span>
            <small class="text-light opacity-75">#{{ $activeJob->order->order_number }}</small>
        </div>

        <h5 class="fw-bold text-white mb-1">{{ $activeJob->order->customer->company_name }}</h5>
        <p class="small text-light opacity-90 mb-2">
            <i class="bi bi-geo-alt-fill me-1 text-warning"></i> {{ Str::limit($activeJob->order->delivery_address, 75) }}
        </p>

        <div class="bg-white bg-opacity-10 rounded p-2 mb-3 small d-flex justify-content-between">
            <span>Muatan: <strong>{{ number_format($activeJob->order->volume, 0, ',', '.') }} L</strong></span>
            <span>Plat: <strong>{{ $activeJob->vehicle->plate_number }}</strong></span>
            <span>Jarak: <strong>{{ $activeJob->order->estimated_distance }} km</strong></span>
        </div>

        <div class="d-grid gap-2">
            <a href="{{ route('driver.jobs.show', $activeJob->id) }}" class="btn btn-warning fw-bold py-2 shadow-sm fs-6">
                <i class="bi bi-arrow-right-circle-fill me-1"></i> Buka Rincian & Update Job
            </a>
            @if(in_array($activeJob->status, ['Accepted', 'In Transit', 'Filling Water']))
                <a href="{{ route('driver.jobs.navigation', $activeJob->id) }}" class="btn btn-outline-light btn-sm py-2 fw-semibold">
                    <i class="bi bi-map-fill me-1"></i> Peta Navigasi & Kirim Posisi GPS
                </a>
            @endif
        </div>
    </div>
@else
    <div class="card driver-card p-4 mb-3 bg-white text-center">
        <i class="bi bi-cup-hot text-muted fs-1 mb-2"></i>
        <h6 class="fw-bold text-dark mb-1">Tidak Ada Tugas Aktif Saat Ini</h6>
        <p class="small text-muted mb-0">
            Anda sedang tidak dalam tugas pengiriman. Notifikasi akan berbunyi saat Admin menugaskan pengiriman baru.
        </p>
    </div>
@endif

<!-- Recent Delivery History -->
<div class="card driver-card p-3 bg-white mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <h6 class="fw-bold text-dark-blue mb-0">Riwayat Pengiriman Terakhir</h6>
        <a href="{{ route('driver.jobs.index') }}" class="small text-decoration-none fw-semibold text-primary-blue">Semua Job</a>
    </div>

    @forelse($recentJobs as $job)
        <a href="{{ route('driver.jobs.show', $job->id) }}" class="text-decoration-none text-dark d-block p-2 rounded mb-2 hover-bg-light border-bottom">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <strong class="text-dark small">{{ $job->order->customer->company_name }}</strong>
                <span class="badge {{ $job->status === 'Completed' ? 'bg-success' : 'bg-secondary' }}" style="font-size: 0.68rem;">
                    {{ $job->status }}
                </span>
            </div>
            <div class="d-flex justify-content-between text-muted" style="font-size: 0.72rem;">
                <span>#{{ $job->order->order_number }} &bull; {{ number_format($job->order->volume, 0, ',', '.') }} L</span>
                <span>{{ $job->assigned_at->format('d/m/Y') }}</span>
            </div>
        </a>
    @empty
        <div class="text-center py-3 text-muted small">Belum ada riwayat tugas selesai.</div>
    @endforelse
</div>
@endsection

