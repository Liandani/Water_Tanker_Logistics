<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pemesanan Air Bersih Tangki') - Tirta Mandiri</title>

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
    <!-- Public Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm sticky-top py-2">
        <div class="container">
            <a class="navbar-brand py-1" href="{{ route('home') }}">
                @include('partials.logo', ['size' => 40])
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="publicNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-dark" href="{{ route('home') }}#hero">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-dark" href="{{ route('home') }}#services">Layanan Armada</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-dark" href="{{ route('home') }}#calculator">Kalkulator Tarif</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-dark" href="{{ route('home') }}#testimonials">Ulasan Mitra</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary-blue btn-sm px-3">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard Admin
                            </a>
                        @elseif(auth()->user()->isDriver())
                            <a href="{{ route('driver.dashboard') }}" class="btn btn-primary-blue btn-sm px-3">
                                <i class="bi bi-truck me-1"></i> Dashboard Driver
                            </a>
                        @else
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-primary-blue btn-sm px-3">
                                <i class="bi bi-water me-1"></i> Dashboard Pelanggan
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary-blue btn-sm px-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-primary-blue btn-sm px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-plus me-1"></i> Daftar Akun
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item py-2" href="{{ route('register.customer') }}"><i class="bi bi-building text-primary me-2"></i> Daftar Sebagai Pelanggan</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('register.driver') }}"><i class="bi bi-truck text-success me-2"></i> Daftar Sebagai Driver</a></li>
                            </ul>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark-blue text-white py-5 mt-auto">
        <div class="container">
            <div class="row g-4 justify-content-between">
                <div class="col-lg-4">
                    @include('partials.logo', ['size' => 42, 'textColor' => '#ffffff'])
                    <p class="text-white-50 mt-3 small">
                        Penyedia layanan pasokan dan distribusi air bersih berkualitas menggunakan armada truk tangki modern berkapasitas 7.000 L hingga 20.000+ L untuk industri, konstruksi, hotel, dan komersial.
                    </p>
                </div>
                <div class="col-lg-3">
                    <h6 class="fw-bold text-aqua mb-3 text-uppercase" style="letter-spacing: 1px;">Kapasitas Armada</h6>
                    <ul class="list-unstyled text-white-50 small mb-0 d-flex flex-column gap-2">
                        <li><i class="bi bi-check2 text-aqua me-2"></i> Tangki 7.000 Liter (Elf NMR 71)</li>
                        <li><i class="bi bi-check2 text-aqua me-2"></i> Tangki 8.000 Liter (Fuso Canter)</li>
                        <li><i class="bi bi-check2 text-aqua me-2"></i> Tangki 16.000 Liter (Fighter FN)</li>
                        <li><i class="bi bi-check2 text-aqua me-2"></i> Tangki 20.000 Liter (Hino Ranger)</li>
                        <li><i class="bi bi-check2 text-aqua me-2"></i> Kategori XXL / Multiple Trip</li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="fw-bold text-aqua mb-3 text-uppercase" style="letter-spacing: 1px;">Kontak Operasional</h6>
                    <ul class="list-unstyled text-white-50 small mb-0 d-flex flex-column gap-2">
                        <li><i class="bi bi-geo-alt-fill text-aqua me-2"></i> Jl. Pengisian Armada Tangki No. 10, Jakarta Pusat</li>
                        <li><i class="bi bi-telephone-fill text-aqua me-2"></i> +62 811-9876-5432 (24 Jam)</li>
                        <li><i class="bi bi-whatsapp text-success me-2"></i> WhatsApp Dispatcher: +62 812-3456-7890</li>
                        <li><i class="bi bi-envelope-fill text-aqua me-2"></i> operasional@tirtamandiri.co.id</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 border-secondary opacity-25">
            <div class="text-center text-white-50 small">
                &copy; {{ date('Y') }} <strong>Tirta Mandiri Water Logistics</strong>. Seluruh Hak Cipta Dilindungi Undang-Undang.
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

