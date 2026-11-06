@extends('layouts.public')

@section('title', 'Masuk ke Akun Anda')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="card card-fresh p-4 p-md-5">
                <div class="text-center mb-4">
                    @include('partials.logo', ['size' => 48])
                    <h4 class="fw-bold mt-3 text-dark-blue">Masuk ke Sistem</h4>
                    <p class="text-muted small">Sistem Pemesanan & Distribusi Truk Tangki Air Bersih</p>
                </div>

                @include('partials.alerts')

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold small text-dark">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="nama@email.com">
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label fw-semibold small text-dark mb-0">Kata Sandi</label>
                            <a href="{{ route('password.request') }}" class="small text-decoration-none text-primary-blue">Lupa Password?</a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-muted" for="remember">
                            Ingat Saya di Perangkat Ini
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary-blue w-100 py-2 fw-bold shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                    </button>
                </form>

                <!-- Quick Login Demo Buttons for Easy Testing -->
                <div class="mt-4 pt-3 border-top">
                    <div class="text-center small text-muted fw-semibold mb-2">⚡ Quick Login Akun Demo:</div>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-sm btn-outline-dark text-start d-flex justify-content-between align-items-center" onclick="fillLogin('admin@tirtamandiri.co.id', 'password123')">
                            <span><i class="bi bi-shield-lock-fill text-danger me-2"></i> <strong>Admin</strong>: admin@tirtamandiri.co.id</span>
                            <span class="badge bg-danger">Admin</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary text-start d-flex justify-content-between align-items-center" onclick="fillLogin('hendri@hotelsantika.co.id', 'password123')">
                            <span><i class="bi bi-building text-primary me-2"></i> <strong>Pelanggan</strong>: Hotel Santika</span>
                            <span class="badge bg-primary">Pelanggan</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success text-start d-flex justify-content-between align-items-center" onclick="fillLogin('budi.driver@tirtamandiri.co.id', 'password123')">
                            <span><i class="bi bi-truck text-success me-2"></i> <strong>Driver</strong>: Budi Santoso</span>
                            <span class="badge bg-success">Driver</span>
                        </button>
                    </div>
                </div>

                <div class="text-center mt-4 pt-2">
                    <p class="small text-muted mb-1">Belum memiliki akun?</p>
                    <div class="d-flex justify-content-center gap-3 small">
                        <a href="{{ route('register.customer') }}" class="text-decoration-none fw-semibold text-primary-blue">
                            <i class="bi bi-person-plus"></i> Daftar Pelanggan
                        </a>
                        <span class="text-muted">|</span>
                        <a href="{{ route('register.driver') }}" class="text-decoration-none fw-semibold text-success">
                            <i class="bi bi-truck"></i> Daftar Driver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function fillLogin(email, pass) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = pass;
    }
</script>
@endpush

