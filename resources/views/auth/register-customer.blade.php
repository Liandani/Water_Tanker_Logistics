@extends('layouts.public')

@section('title', 'Registrasi Pelanggan')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div class="card card-fresh p-4 p-md-5">
                <div class="text-center mb-4">
                    @include('partials.logo', ['size' => 44])
                    <h4 class="fw-bold mt-3 text-dark-blue">Registrasi Mitra Pelanggan</h4>
                    <p class="text-muted small">Daftarkan perusahaan Anda untuk pemesanan armada air tangki secara cepat dan terintegrasi</p>
                </div>

                @include('partials.alerts')

                <form method="POST" action="{{ route('register.customer') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold small text-dark">Nama Penanggung Jawab (PIC) <span class="text-danger">*</span></label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required placeholder="Contoh: Bpk. Budi Darmawan">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="company_name" class="form-label fw-semibold small text-dark">Nama Perusahaan / Mitra <span class="text-danger">*</span></label>
                            <input id="company_name" type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name') }}" required placeholder="Contoh: PT Sumber Pangan Hotel">
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold small text-dark">Alamat Email Perusahaan <span class="text-danger">*</span></label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="purchasing@perusahaan.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold small text-dark">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required placeholder="081234567890">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="business_category" class="form-label fw-semibold small text-dark">Kategori Bisnis <span class="text-danger">*</span></label>
                            <select id="business_category" class="form-select @error('business_category') is-invalid @enderror" name="business_category" required>
                                <option value="" selected disabled>Pilih Kategori Bisnis...</option>
                                <option value="Hotel" {{ old('business_category') === 'Hotel' ? 'selected' : '' }}>Hotel & Penginapan</option>
                                <option value="Proyek Konstruksi" {{ old('business_category') === 'Proyek Konstruksi' ? 'selected' : '' }}>Proyek Konstruksi & Sipil</option>
                                <option value="Pabrik/Industri" {{ old('business_category') === 'Pabrik/Industri' ? 'selected' : '' }}>Pabrik / Kawasan Industri</option>
                                <option value="Restoran" {{ old('business_category') === 'Restoran' ? 'selected' : '' }}>Restoran & Food Beverage</option>
                                <option value="Lainnya" {{ old('business_category') === 'Lainnya' ? 'selected' : '' }}>Lainnya / Komersial</option>
                            </select>
                            @error('business_category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label fw-semibold small text-dark">Alamat Lengkap Perusahaan / Lokasi Pengisian <span class="text-danger">*</span></label>
                            <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" rows="3" required placeholder="Jl. Raya Industri No. 88, Blok A, Jakarta">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold small text-dark">Kata Sandi <span class="text-danger">*</span></label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Minimal 8 karakter">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold small text-dark">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required placeholder="Ulangi kata sandi">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary-blue w-100 py-2 fw-bold shadow-sm">
                            <i class="bi bi-building-add me-1"></i> Daftar Sebagai Pelanggan
                        </button>
                    </div>

                    <div class="text-center mt-3 pt-2 border-top">
                        <span class="small text-muted">Sudah memiliki akun?</span>
                        <a href="{{ route('login') }}" class="small text-decoration-none fw-semibold text-primary-blue ms-1">Masuk Di Sini</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

