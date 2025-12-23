<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Admin Panel • MyStore')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @include('partials.premium-styles')
    <style>
        .navbar-blur {
            background: rgba(15, 23, 42, 0.8) !important;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .page-wrap { padding-top: 100px; min-height: 100vh; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-blur fixed-top py-3">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('admin.dashboard') }}">
            <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info border border-info border-opacity-25" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-speedometer2"></i>
            </div>
            <span class="text-white tracking-tight">Admin<span class="text-info">Portal</span></span>
        </a>

        <div class="d-flex align-items-center gap-3 ms-auto">
            <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="bi bi-shop me-1"></i>View Store
            </a>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button class="btn btn-warning btn-sm rounded-pill px-3">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<div class="page-wrap container-fluid px-4">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
