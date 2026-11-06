@extends('layouts.admin')

@section('title', 'Tambah Driver Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mb-3">
            <a href="{{ route('admin.drivers.index') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left"></i> Kembali ke Data Driver
            </a>
            <h4 class="fw-bold text-dark-blue mt-1">Tambah Driver Baru</h4>
        </div>

        <div class="card card-fresh p-4 p-md-5">
            <form action="{{ route('admin.drivers.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label small fw-bold text-dark">Nama Lengkap Driver <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Nama lengkap">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label small fw-bold text-dark">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="081234567890">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-bold text-dark">Alamat Email Login <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="driver@tirtamandiri.co.id">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="availability_status" class="form-label small fw-bold text-dark">Status Kesiapan Awal <span class="text-danger">*</span></label>
                        <select id="availability_status" class="form-select @error('availability_status') is-invalid @enderror" name="availability_status" required>
                            <option value="Aktif" selected>Aktif (Siap Bertugas)</option>
                            <option value="Libur">Libur</option>
                            <option value="On Delivery">On Delivery</option>
                        </select>
                        @error('availability_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="license_number" class="form-label small fw-bold text-dark">Nomor SIM (SIM B1 / B2)</label>
                        <input type="text" class="form-control" id="license_number" name="license_number" value="{{ old('license_number') }}" placeholder="Contoh: SIM-B2-8829100">
                    </div>

                    <div class="col-md-6">
                        <label for="identity_number" class="form-label small fw-bold text-dark">Nomor KTP</label>
                        <input type="text" class="form-control" id="identity_number" name="identity_number" value="{{ old('identity_number') }}" placeholder="3171xxxxxxxxxxxx">
                    </div>

                    <div class="col-12">
                        <label for="password" class="form-label small fw-bold text-dark">Kata Sandi Akun <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Minimal 8 karakter">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary-blue fw-bold px-4">
                        <i class="bi bi-save me-1"></i> Simpan Driver Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

