<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Customer Portal') - Tirta Mandiri Water Logistics</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    @stack('styles')
</head>
<body class="bg-main d-flex flex-column min-vh-100">
    <!-- Customer Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm sticky-top py-2">
        <div class="container">
            <a class="navbar-brand py-1" href="{{ route('customer.dashboard') }}">
                @include('partials.logo', ['size' => 38])
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#customerNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="customerNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 fw-semibold {{ request()->routeIs('customer.dashboard') ? 'text-primary-blue active' : 'text-dark' }}" href="{{ route('customer.dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 fw-semibold {{ request()->routeIs('customer.orders.create') ? 'text-primary-blue active' : 'text-dark' }}" href="{{ route('customer.orders.create') }}">
                            <i class="bi bi-plus-circle-fill me-1 text-primary-blue"></i> Buat Pesanan Baru
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 fw-semibold {{ request()->routeIs('customer.orders.index') || request()->routeIs('customer.orders.show') || request()->routeIs('customer.orders.tracking') ? 'text-primary-blue active' : 'text-dark' }}" href="{{ route('customer.orders.index') }}">
                            <i class="bi bi-clock-history me-1"></i> Riwayat Pesanan
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <!-- Notifications -->
                    @php
                        $unreadNotifications = auth()->user()->unreadNotifications;
                    @endphp
                    <div class="dropdown">
                        <button class="btn btn-light rounded-circle position-relative p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px;">
                            <i class="bi bi-bell text-secondary fs-5"></i>
                            @if($unreadNotifications->count() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                    {{ $unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2" style="width: 320px; max-height: 400px; overflow-y: auto;">
                            <div class="d-flex justify-content-between align-items-center px-2 py-1 mb-2 border-bottom">
                                <span class="fw-bold small text-dark">Notifikasi ({{ $unreadNotifications->count() }})</span>
                                @if($unreadNotifications->count() > 0)
                                    <form action="{{ route('notifications.markAllRead') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size: 0.75rem;">Tandai Dibaca</button>
                                    </form>
                                @endif
                            </div>
                            @forelse($unreadNotifications->take(5) as $notif)
                                <form action="{{ route('notifications.read', $notif->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item p-2 rounded text-wrap text-start mb-1 bg-light">
                                        <div class="d-flex align-items-start gap-2">
                                            <i class="bi bi-bell-fill text-primary mt-1"></i>
                                            <div>
                                                <div class="fw-semibold small text-dark">{{ $notif->data['title'] ?? 'Pemberitahuan' }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">{{ Str::limit($notif->data['message'] ?? '', 70) }}</div>
                                                <small class="text-muted" style="font-size: 0.68rem;">{{ $notif->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </button>
                                </form>
                            @empty
                                <div class="text-center py-3 text-muted small">Tidak ada notifikasi baru.</div>
                            @endforelse
                            <div class="text-center pt-2 border-top">
                                <a href="{{ route('notifications.index') }}" class="small text-decoration-none text-primary-blue fw-semibold">Lihat Semua Notifikasi</a>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Profile Dropdown -->
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle gap-2" data-bs-toggle="dropdown">
                            <div class="bg-primary-blue text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 38px; height: 38px;">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="d-none d-md-block text-start">
                                <div class="fw-bold small" style="line-height: 1.2;">{{ auth()->user()->customer->company_name ?? auth()->user()->name }}</div>
                                <small class="text-muted" style="font-size: 0.72rem;">{{ auth()->user()->name }}</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item" href="{{ route('customer.profile') }}"><i class="bi bi-building me-2"></i> Profil Perusahaan</a></li>
                            <li><a class="dropdown-item" href="{{ route('customer.orders.index') }}"><i class="bi bi-receipt me-2"></i> Daftar Pesanan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="py-4 flex-grow-1">
        <div class="container">
            @include('partials.alerts')
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-4 text-center text-muted small mt-auto">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div>
                    &copy; {{ date('Y') }} <strong>Tirta Mandiri Water Logistics</strong>. Layanan Distribusi Air Tangki Bersih & Cepat.
                </div>
                <div class="d-flex gap-3 text-secondary">
                    <span><i class="bi bi-telephone-fill me-1 text-primary-blue"></i> +62 811-9876-5432</span>
                    <span><i class="bi bi-envelope-fill me-1 text-primary-blue"></i> cs@tirtamandiri.co.id</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @stack('scripts')
</body>
</html>

