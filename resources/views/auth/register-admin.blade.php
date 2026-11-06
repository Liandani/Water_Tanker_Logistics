@extends('layouts.public')

@section('title', 'Aktivasi Akun Administrator')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="card card-fresh p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 50px; height: 50px;">
                        <i class="bi bi-shield-lock-fill fs-3"></i>
                    </div>
                    <h4 class="fw-bold text-dark-blue">Aktivasi Administrator</h4>
                    <p class="text-muted small">Khusus staf manajemen operasional Tirta Mandiri</p>
                </div>

                @include('partials.alerts')

                <form method="POST" action="{{ route('register.admin') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold small text-dark">Nama Lengkap Administrator</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required placeholder="Nama lengkap">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold small text-dark">Email Resmi Perusahaan</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="admin@tirtamandiri.co.id">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold small text-dark">Nomor Telepon / WhatsApp</label>
                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="08123456789">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="security_key" class="form-label fw-semibold small text-danger">Security Activation Key <span class="badge bg-danger">Wajib</span></label>
                        <input id="security_key" type="password" class="form-control @error('security_key') is-invalid @enderror" name="security_key" required placeholder="Masukkan kunci otorisasi admin">
                        <small class="text-muted" style="font-size: 0.72rem;">Kunci demo default: <code>TIRTA-ADMIN-2026</code></small>
                        @error('security_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold small text-dark">Kata Sandi</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Minimal 8 karakter">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold small text-dark">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required placeholder="Ulangi kata sandi">
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2 fw-bold shadow-sm">
                        <i class="bi bi-shield-check me-1"></i> Aktifkan Akun Admin
                    </button>
                </form>

                <div class="text-center mt-3 pt-2 border-top">
                    <a href="{{ route('login') }}" class="small text-decoration-none fw-semibold text-primary-blue">Kembali ke Halaman Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

