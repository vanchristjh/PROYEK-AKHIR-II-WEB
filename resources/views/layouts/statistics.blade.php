<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SMAN 1 Girsip') }} - Statistik Tugas</title>
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js with explicit loading -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    <!-- App CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/statistics-fix.css') }}" rel="stylesheet">
    
    <style>
        body, html {
            display: block !important;
            min-height: 100vh;
            background-color: #f8fafc;
        }
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .navbar-custom {
            background-color: #1e40af;
            color: white;
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: white;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Simple Navigation -->
    <nav class="navbar navbar-dark navbar-custom shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-school me-2"></i>
                SMAN 1 GIRSIP
            </a>
            <div>
                <a href="{{ url()->previous() }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="main-container mt-4">
        <div class="bg-white shadow-sm rounded p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Statistik Tugas</h2>
                <span class="text-secondary small">
                    <i class="fas fa-info-circle me-1"></i> Halaman Alternatif
                </span>
            </div>
        </div>
        
        @yield('content')
    </div>
    
    <!-- Footer -->
    <footer class="bg-light py-3 mt-4">
        <div class="container text-center">
            <p class="text-muted small mb-0">© {{ date('Y') }} SMAN 1 GIRSIP - Sistem Informasi Arsip Digital</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/statistics-helper.js') }}"></script>
    
    @yield('scripts')
</body>
</html>
