@extends('layouts.admin')

@section('title', 'Tambah Pelanggan Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="mb-3">
            <a href="{{ route('admin.customers.index') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pelanggan
            </a>
            <h4 class="fw-bold text-dark-blue mt-1">Tambah Pelanggan Baru</h4>
        </div>

        <div class="card card-fresh p-4 p-md-5">
            <form action="{{ route('admin.customers.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="company_name" class="form-label small fw-bold text-dark">Nama Perusahaan / Mitra <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name') }}" required placeholder="Contoh: PT Grand Sahid Hotel">
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="name" class="form-label small fw-bold text-dark">Nama Penanggung Jawab (PIC) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Nama lengkap">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-bold text-dark">Alamat Email Login <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="email@perusahaan.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label small fw-bold text-dark">Nomor Telepon / WA <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="081234567890">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="business_category" class="form-label small fw-bold text-dark">Kategori Bisnis <span class="text-danger">*</span></label>
                        <select id="business_category" class="form-select @error('business_category') is-invalid @enderror" name="business_category" required>
                            <option value="Hotel">Hotel & Penginapan</option>
                            <option value="Proyek Konstruksi">Proyek Konstruksi</option>
                            <option value="Pabrik/Industri">Pabrik / Kawasan Industri</option>
                            <option value="Restoran">Restoran & Cafe</option>
                            <option value="Lainnya" selected>Lainnya</option>
                        </select>
                        @error('business_category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label small fw-bold text-dark">Alamat Lengkap Pengiriman <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required placeholder="Jl. Raya Industri Blok C No. 5, Jakarta">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary-blue fw-bold px-4">
                        <i class="bi bi-save me-1"></i> Simpan Data Pelanggan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

