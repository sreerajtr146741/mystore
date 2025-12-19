<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'MyStore')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @stack('styles')
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        
        /* Blue Header Styles */
        .blue-header {
            background-color: #87CEEB; /* Sky blue matching image */
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 60px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            position: sticky; top: 0; z-index: 1000;
        }

        .nav-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            max-width: 1200px;
            padding: 0 15px;
        }

        .left-nav {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .nav-link-custom {
            color: #4a4a4a;
            font-weight: 500;
            text-decoration: none;
            padding: 8px 16px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .nav-link-custom:hover {
            color: #000;
        }

        .nav-link-custom.active {
            background-color: #fff;
            color: #333;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .right-nav {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .right-item {
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .right-item i {
            font-size: 1.1rem;
        }

        .icon-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            color: #333;
            padding: 0;
        }

        /* Search Toggle */
        .search-bar-container {
            display: none;
            position: absolute;
            top: 60px;
            left: 0;
            width: 100%;
            background: #fff;
            padding: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            justify-content: center;
        }
        .search-bar-container.active { display: flex; }
        .search-input-header {
            width: 100%;
            max-width: 600px;
            border: 1px solid #ddd;
            padding: 8px 15px;
            border-radius: 20px;
            outline: none;
        }

        /* Mobile Responsive */
        @media (max-width: 991px) {
            .left-nav { display: none; } /* Use hamburger for full menu usually, simplifying for this request */
            .mobile-menu-btn { display: block; margin-right: 15px; font-size: 1.5rem; cursor: pointer; }
        }
        @media (min-width: 992px) {
            .mobile-menu-btn { display: none; }
        }
    </style>
</head>
<body>

<header class="blue-header">
    <div class="nav-container">
        <!-- Mobile Menu Icon (Left) -->
        <i class="bi bi-list mobile-menu-btn" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu"></i>

        <!-- Left/Center Navigation -->
        <div class="d-flex align-items-center gap-4 left-nav">
            <!-- Logo area -->
            <a href="{{ route('products.index') }}" class="d-flex align-items-center text-decoration-none text-dark me-3">
                <i class="bi bi-bag-fill fs-4 me-2"></i>
                <span class="fw-bold fs-5" style="letter-spacing: -0.5px;">MyStore</span>
            </a>

            <!-- Links -->
            <nav class="d-flex align-items-center gap-2">
                <a href="{{ route('products.index') }}" class="nav-link-custom {{ request()->routeIs('products.index') ? 'active' : '' }}">Home</a>
                <a href="{{ route('products.index') }}" class="nav-link-custom">Shop</a>
                <a href="{{ route('about') }}" class="nav-link-custom {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
                <a href="{{ route('contact') }}" class="nav-link-custom {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
            </nav>
        </div>

        <!-- Right Side Icons -->
        <div class="right-nav">
            <a href="{{ route('cart.index') }}" class="right-item position-relative">
                <i class="bi bi-bag-fill"></i>
                @php $cartCount = count(session('cart', [])); @endphp
                @if($cartCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>

            @auth
                <div class="dropdown">
                    <a href="#" class="right-item dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-fill"></i>
                        <span>{{ auth()->user()->first_name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('orders.index') }}">Orders</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="right-item">
                    <i class="bi bi-person-fill"></i>
                    <span>Login</span>
                </a>
            @endauth
        </div>
    </div>
</header>

<!-- Mobile Menu Offcanvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
  <div class="offcanvas-header bg-light">
    <h5 class="offcanvas-title fw-bold">Menu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <div class="d-grid gap-2">
        <a href="{{ route('products.index') }}" class="btn {{ request()->routeIs('products.index') ? 'btn-primary' : 'btn-outline-dark' }} text-start border-0 fw-bold">Home</a>
        <a href="{{ route('products.index') }}" class="btn btn-outline-dark text-start border-0">Shop</a>
        <a href="{{ route('about') }}" class="btn {{ request()->routeIs('about') ? 'btn-primary' : 'btn-outline-dark' }} text-start border-0 fw-bold">About</a>
        <a href="{{ route('contact') }}" class="btn {{ request()->routeIs('contact') ? 'btn-primary' : 'btn-outline-dark' }} text-start border-0 fw-bold">Contact</a>
    </div>
  </div>
</div>

<main class="min-vh-100">
    @yield('content')
</main>

{{-- Toast --}}
@if(session('success'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div class="toast show align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSearch() {
        const bar = document.getElementById('searchBar');
        bar.classList.toggle('active');
        if(bar.classList.contains('active')) {
            bar.querySelector('input').focus();
        }
    }
</script>
@stack('scripts')

</body>
</html>
