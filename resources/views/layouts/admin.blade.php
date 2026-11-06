<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Tirta Mandiri Water Logistics</title>

    <!-- Google Fonts: Inter -->
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
<body class="bg-main">
    <div class="d-flex" id="wrapper">
        <!-- Admin Sidebar -->
        <aside class="admin-sidebar d-flex flex-column flex-shrink-0 p-3 shadow" id="sidebar">
            <div class="d-flex align-items-center justify-content-between mb-4 px-2 pt-2">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                    @include('partials.logo', ['size' => 36, 'textColor' => '#ffffff'])
                </a>
            </div>

            <hr style="border-color: rgba(85, 201, 217, 0.2);" class="my-1">

            <div class="small text-uppercase px-3 py-2 text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 1px; color: #55C9D9 !important;">
                MENU UTAMA
            </div>

            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i class="bi bi-cart-check-fill"></i> Data Pesanan
                        @php
                            $pendingOrdersCount = \App\Models\Order::where('status', 'Pending')->count();
                        @endphp
                        @if($pendingOrdersCount > 0)
                            <span class="badge rounded-pill bg-warning text-dark ms-auto">{{ $pendingOrdersCount }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.monitoring.maps') }}" class="nav-link {{ request()->routeIs('admin.monitoring.*') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt-fill"></i> Monitoring Maps
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.surat-jalan.index') }}" class="nav-link {{ request()->routeIs('admin.surat-jalan.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text-fill"></i> Surat Izin Jalan
                    </a>
                </li>

                <div class="small text-uppercase px-3 pt-3 pb-1 text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 1px; color: #55C9D9 !important;">
                    DATA MASTER
                </div>

                <li>
                    <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                        <i class="bi bi-buildings-fill"></i> Data Pelanggan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.drivers.index') }}" class="nav-link {{ request()->routeIs('admin.drivers.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge-fill"></i> Data Driver
                        @php
                            $pendingDriversCount = \App\Models\Driver::where('verification_status', 'pending')->count();
                        @endphp
                        @if($pendingDriversCount > 0)
                            <span class="badge rounded-pill bg-danger ms-auto">{{ $pendingDriversCount }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.vehicles.index') }}" class="nav-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}">
                        <i class="bi bi-truck"></i> Data Armada
                    </a>
                </li>

                <div class="small text-uppercase px-3 pt-3 pb-1 text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 1px; color: #55C9D9 !important;">
                    FINANSIAL & LAPORAN
                </div>

                <li>
                    <a href="{{ route('admin.financial.index') }}" class="nav-link {{ request()->routeIs('admin.financial.index') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack"></i> Keuangan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.financial.reports') }}" class="nav-link {{ request()->routeIs('admin.financial.reports') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart-line-fill"></i> Laporan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.ratings.index') }}" class="nav-link {{ request()->routeIs('admin.ratings.*') ? 'active' : '' }}">
                        <i class="bi bi-star-fill"></i> Rating & Ulasan
                    </a>
                </li>

                <div class="small text-uppercase px-3 pt-3 pb-1 text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 1px; color: #55C9D9 !important;">
                    SISTEM
                </div>

                <li>
                    <a href="{{ route('admin.tariff.index') }}" class="nav-link {{ request()->routeIs('admin.tariff.*') ? 'active' : '' }}">
                        <i class="bi bi-sliders"></i> Pengaturan Tarif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i> Log Aktivitas
                    </a>
                </li>
            </ul>

            <div class="mt-auto pt-3 border-top border-secondary">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 btn-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-grow-1 d-flex flex-column" style="min-width: 0;">
            <!-- Top Navbar -->
            <header class="navbar navbar-expand bg-white border-bottom shadow-sm px-4 py-3 sticky-top">
                <button class="btn btn-sm btn-light border d-lg-none me-3" id="sidebarToggle" type="button">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <div class="d-none d-md-flex align-items-center">
                    <span class="badge bg-light-blue text-primary-blue fw-semibold px-3 py-2 rounded-pill">
                        <i class="bi bi-shield-check me-1"></i> Administrator Workspace
                    </span>
                </div>

                <div class="ms-auto d-flex align-items-center gap-3">
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
                                        <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size: 0.75rem;">Tandai Semua Dibaca</button>
                                    </form>
                                @endif
                            </div>
                            @forelse($unreadNotifications->take(5) as $notif)
                                <form action="{{ route('notifications.read', $notif->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item p-2 rounded text-wrap text-start mb-1 bg-light">
                                        <div class="d-flex align-items-start gap-2">
                                            <i class="bi bi-info-circle-fill text-primary mt-1"></i>
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

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle gap-2" data-bs-toggle="dropdown">
                            <div class="bg-primary-blue text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="d-none d-sm-block text-start">
                                <div class="fw-bold small" style="line-height: 1.2;">{{ auth()->user()->name }}</div>
                                <small class="text-muted" style="font-size: 0.72rem;">Super Admin</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item" href="{{ route('admin.tariff.index') }}"><i class="bi bi-gear me-2"></i> Pengaturan Sistem</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="p-4 flex-grow-1">
                @include('partials.alerts')
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-top py-3 px-4 text-center text-muted small mt-auto">
                &copy; {{ date('Y') }} <strong>Tirta Mandiri Water Logistics</strong>. Sistem Pemesanan & Distribusi Truk Tangki Air Bersih.
            </footer>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar')?.classList.toggle('d-none');
        });
    </script>

    @stack('scripts')
</body>
</html>

