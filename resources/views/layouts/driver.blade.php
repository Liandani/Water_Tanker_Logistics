<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Driver Mobile App') - Tirta Mandiri</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <style>
        body {
            background-color: #EDF5FA;
            padding-bottom: 75px;
        }
        .driver-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 15px rgba(11, 61, 98, 0.06);
        }
        .btn-driver-action {
            padding: 0.85rem 1.25rem;
            font-size: 1.05rem;
            font-weight: 700;
            border-radius: 12px;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="driver-mobile-container">
        <!-- Top App Bar -->
        <header class="bg-dark-blue text-white py-3 px-3 shadow-sm sticky-top">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary-blue text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 1.1rem;">
                        <i class="bi bi-truck"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size: 0.95rem; line-height: 1.2;">{{ auth()->user()->name }}</div>
                        <small class="text-info" style="font-size: 0.72rem;">Driver Truk Tangki</small>
                    </div>
                </div>

                <!-- Work Status Toggle Badge -->
                @if(auth()->user()->driver)
                    <form action="{{ route('driver.toggleAvailability') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ auth()->user()->driver->availability_status === 'Aktif' ? 'btn-success' : (auth()->user()->driver->availability_status === 'On Delivery' ? 'btn-primary' : 'btn-secondary') }} rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.55rem;"></i>
                            {{ auth()->user()->driver->availability_status }}
                        </button>
                    </form>
                @endif
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="p-3">
            @include('partials.alerts')
            @yield('content')
        </main>

        <!-- Bottom Navigation Bar for Mobile -->
        <nav class="driver-bottom-nav">
            <a href="{{ route('driver.dashboard') }}" class="{{ request()->routeIs('driver.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door-fill"></i>
                <span>Beranda</span>
            </a>
            <a href="{{ route('driver.jobs.index') }}" class="{{ request()->routeIs('driver.jobs.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-check-fill"></i>
                <span>Tugas Job</span>
            </a>
            <a href="{{ route('notifications.index') }}" class="{{ request()->routeIs('notifications.*') ? 'active' : '' }} position-relative">
                <i class="bi bi-bell-fill"></i>
                @php
                    $unreadCount = auth()->user()->unreadNotifications->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="position-absolute top-0 start-50 translate-middle-x badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        {{ $unreadCount }}
                    </span>
                @endif
                <span>Notifikasi</span>
            </a>
            <a href="{{ route('driver.profile') }}" class="{{ request()->routeIs('driver.profile') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i>
                <span>Profil</span>
            </a>
        </nav>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @stack('scripts')
</body>
</html>

