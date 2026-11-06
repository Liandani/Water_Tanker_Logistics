@extends('layouts.admin')

@section('title', 'Edit Driver ' . $driver->user->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mb-3">
            <a href="{{ route('admin.drivers.index') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left"></i> Kembali ke Data Driver
            </a>
            <h4 class="fw-bold text-dark-blue mt-1">Ubah Data Driver</h4>
        </div>

        <div class="card card-fresh p-4 p-md-5">
            <form action="{{ route('admin.drivers.update', $driver->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label small fw-bold text-dark">Nama Lengkap Driver <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $driver->user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label small fw-bold text-dark">Nomor WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $driver->user->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-bold text-dark">Email Akun <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $driver->user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="availability_status" class="form-label small fw-bold text-dark">Kesiapan Kerja <span class="text-danger">*</span></label>
                        <select id="availability_status" class="form-select @error('availability_status') is-invalid @enderror" name="availability_status" required>
                            <option value="Aktif" {{ old('availability_status', $driver->availability_status) === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="On Delivery" {{ old('availability_status', $driver->availability_status) === 'On Delivery' ? 'selected' : '' }}>On Delivery</option>
                            <option value="Libur" {{ old('availability_status', $driver->availability_status) === 'Libur' ? 'selected' : '' }}>Libur</option>
                        </select>
                        @error('availability_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="verification_status" class="form-label small fw-bold text-dark">Status Verifikasi <span class="text-danger">*</span></label>
                        <select id="verification_status" class="form-select @error('verification_status') is-invalid @enderror" name="verification_status" required>
                            <option value="verified" {{ old('verification_status', $driver->verification_status) === 'verified' ? 'selected' : '' }}>Verified (Disetujui)</option>
                            <option value="pending" {{ old('verification_status', $driver->verification_status) === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                            <option value="rejected" {{ old('verification_status', $driver->verification_status) === 'rejected' ? 'selected' : '' }}>Rejected (Ditolak)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label small fw-bold text-dark">Status Akun Login <span class="text-danger">*</span></label>
                        <select id="status" class="form-select @error('status') is-invalid @enderror" name="status" required>
                            <option value="active" {{ old('status', $driver->user->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $driver->user->status) === 'inactive' ? 'selected' : '' }}>Inactive / Nonaktif</option>
                            <option value="pending" {{ old('status', $driver->user->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="license_number" class="form-label small fw-bold text-dark">Nomor SIM</label>
                        <input type="text" class="form-control" id="license_number" name="license_number" value="{{ old('license_number', $driver->license_number) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="identity_number" class="form-label small fw-bold text-dark">Nomor KTP</label>
                        <input type="text" class="form-control" id="identity_number" name="identity_number" value="{{ old('identity_number', $driver->identity_number) }}">
                    </div>

                    <div class="col-12">
                        <label for="password" class="form-label small fw-bold text-dark">Ubah Kata Sandi (Kosongkan jika tidak diubah)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Minimal 8 karakter baru">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary-blue fw-bold px-4">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan Driver
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

