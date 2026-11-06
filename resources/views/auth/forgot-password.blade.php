@extends('layouts.public')

@section('title', 'Lupa Kata Sandi')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card card-fresh p-4 p-md-5">
                <div class="text-center mb-4">
                    @include('partials.logo', ['size' => 42])
                    <h4 class="fw-bold mt-3 text-dark-blue">Pemulihan Kata Sandi</h4>
                    <p class="text-muted small">Masukkan alamat email Anda untuk menerima tautan pemulihan kata sandi.</p>
                </div>

                @include('partials.alerts')

                @if (session('status'))
                    <div class="alert alert-success small mb-3">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold small text-dark">Alamat Email Terdaftar</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="nama@email.com">
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary-blue w-100 py-2 fw-bold shadow-sm">
                        <i class="bi bi-send me-1"></i> Kirim Tautan Reset
                    </button>
                </form>

                <div class="text-center mt-4 pt-2 border-top">
                    <a href="{{ route('login') }}" class="small text-decoration-none fw-semibold text-primary-blue">
                        <i class="bi bi-arrow-left"></i> Kembali ke Halaman Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

