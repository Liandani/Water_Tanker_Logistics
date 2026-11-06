@extends('layouts.driver')

@section('title', 'Profil Driver')

@section('content')
<div class="card driver-card p-4 bg-white mb-3">
    <div class="text-center mb-3">
        <div class="rounded-circle bg-primary-blue text-white d-inline-flex align-items-center justify-content-center fw-bold fs-2 shadow-sm mb-2" style="width: 72px; height: 72px;">
            {{ substr(auth()->user()->name, 0, 1) }}
        </div>
        <h5 class="fw-bold text-dark mb-0">{{ auth()->user()->name }}</h5>
        <span class="badge bg-success-subtle text-success border border-success-subtle small mt-1">
            Status Akun: {{ ucfirst(auth()->user()->status) }}
        </span>
    </div>

    <!-- Average Rating -->
    <div class="p-3 rounded-3 bg-light text-center mb-3">
        <span class="small text-muted d-block mb-1">Rata-Rata Penilaian Pelanggan:</span>
        <div class="d-flex justify-content-center align-items-center gap-2">
            <span class="fs-4 fw-bold text-warning">{{ $driver->averageRating() }}</span>
            <div class="text-warning fs-5">
                @for($i = 1; $i <= 5; $i++)
                    <i class="bi bi-star{{ $i <= round($driver->averageRating()) ? '-fill' : '' }}"></i>
                @endfor
            </div>
            <small class="text-muted">({{ $driver->ratings()->count() }} ulasan)</small>
        </div>
    </div>

    <form action="{{ route('driver.profile.update') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label small fw-bold text-dark">Nama Lengkap</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label small fw-bold text-dark">Nomor WhatsApp Aktif</label>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required>
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label small fw-bold text-dark">Email Akun (Login)</label>
            <input type="email" class="form-control bg-light" value="{{ auth()->user()->email }}" readonly>
        </div>

        <div class="mb-3">
            <label for="license_number" class="form-label small fw-bold text-dark">Nomor SIM</label>
            <input type="text" class="form-control" id="license_number" name="license_number" value="{{ old('license_number', $driver->license_number) }}" placeholder="SIM B1 / B2">
        </div>

        <div class="mb-4">
            <label for="identity_number" class="form-label small fw-bold text-dark">Nomor KTP</label>
            <input type="text" class="form-control" id="identity_number" name="identity_number" value="{{ old('identity_number', $driver->identity_number) }}" placeholder="Nomor Induk Kependudukan">
        </div>

        <button type="submit" class="btn btn-primary-blue w-100 fw-bold py-2 shadow-sm mb-2">
            <i class="bi bi-save me-1"></i> Simpan Perubahan Profil
        </button>
    </form>

    <div class="pt-3 border-top mt-2">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100 py-2">
                <i class="bi bi-box-arrow-right me-1"></i> Keluar dari Aplikasi
            </button>
        </form>
    </div>
</div>
@endsection

