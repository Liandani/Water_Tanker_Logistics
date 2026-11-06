@extends('layouts.public')

@section('title', 'Registrasi Driver Truk Tangki')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div class="card card-fresh p-4 p-md-5">
                <div class="text-center mb-4">
                    @include('partials.logo', ['size' => 44])
                    <h4 class="fw-bold mt-3 text-dark-blue">Pendaftaran Mitra Driver Tangki</h4>
                    <p class="text-muted small">Bergabunglah sebagai pengemudi armada truk tangki air profesional</p>
                </div>

                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4" style="background-color: #EAF7FF; color: #0B3D62;">
                    <i class="bi bi-info-circle-fill fs-4 me-3 text-primary-blue"></i>
                    <div class="small">
                        <strong>Catatan Verifikasi:</strong> Setelah pendaftaran, akun Driver Anda akan ditinjau oleh Admin dan membutuhkan verifikasi sebelum Anda dapat menerima tugas pengiriman.
                    </div>
                </div>

                @include('partials.alerts')

                <form method="POST" action="{{ route('register.driver') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold small text-dark">Nama Lengkap Sesuai KTP <span class="text-danger">*</span></label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required placeholder="Contoh: Rahmat Santoso">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold small text-dark">Alamat Email <span class="text-danger">*</span></label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="driver@email.com">
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

                        <div class="col-md-6">
                            <label for="identity_number" class="form-label fw-semibold small text-dark">Nomor KTP (Opsional)</label>
                            <input id="identity_number" type="text" class="form-control @error('identity_number') is-invalid @enderror" name="identity_number" value="{{ old('identity_number') }}" placeholder="3171xxxxxxxxxxxx">
                            @error('identity_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="license_number" class="form-label fw-semibold small text-dark">Nomor SIM (SIM B1 / B2 Umum) (Opsional)</label>
                            <input id="license_number" type="text" class="form-control @error('license_number') is-invalid @enderror" name="license_number" value="{{ old('license_number') }}" placeholder="Contoh: SIM-B2-12345678">
                            @error('license_number')
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
                        <button type="submit" class="btn btn-primary-blue w-100 py-2 fw-bold shadow-sm" style="background-color: #20B486; border-color: #20B486;">
                            <i class="bi bi-truck me-1"></i> Daftar Sebagai Driver
                        </button>
                    </div>

                    <div class="text-center mt-3 pt-2 border-top">
                        <span class="small text-muted">Sudah memiliki akun driver?</span>
                        <a href="{{ route('login') }}" class="small text-decoration-none fw-semibold text-primary-blue ms-1">Masuk Di Sini</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

