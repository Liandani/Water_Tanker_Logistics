@extends('layouts.public')

@section('title', 'Menunggu Verifikasi Driver')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card card-fresh p-4 p-md-5 text-center">
                <div class="rounded-circle bg-warning text-dark d-inline-flex align-items-center justify-content-center mb-3 mx-auto shadow-sm" style="width: 72px; height: 72px;">
                    <i class="bi bi-hourglass-split fs-1"></i>
                </div>
                <h4 class="fw-bold text-dark-blue">Akun Sedang Diverifikasi</h4>
                <p class="text-muted small mb-4">
                    Halo <strong>{{ auth()->user()->name }}</strong>, pendaftaran akun Mitra Driver Anda telah berhasil kami terima dan saat ini sedang dalam proses verifikasi oleh tim Administrator Tirta Mandiri.
                </p>

                <div class="card bg-light border-0 p-3 mb-4 text-start small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Nama:</span>
                        <strong class="text-dark">{{ auth()->user()->name }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Email:</span>
                        <span class="text-dark">{{ auth()->user()->email }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">WhatsApp:</span>
                        <span class="text-dark">{{ auth()->user()->phone ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Status Akun:</span>
                        <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> Menunggu Verifikasi Admin</span>
                    </div>
                </div>

                <p class="text-muted small mb-4">
                    Setelah diverifikasi oleh Admin, Anda akan langsung dapat mengakses Dashboard Driver, melihat penugasan pengiriman, dan mengoperasikan armada truk tangki.
                </p>

                <div class="d-flex flex-column gap-2">
                    <a href="https://wa.me/6281198765432?text=Halo%20Admin,%20saya%20telah%20mendaftar%20sebagai%20driver%20({{ auth()->user()->name }})%20dan%20menunggu%20verifikasi." target="_blank" class="btn btn-success fw-bold py-2">
                        <i class="bi bi-whatsapp me-1"></i> Hubungi Dispatcher Admin
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary w-100 py-2">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout Sementara
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

