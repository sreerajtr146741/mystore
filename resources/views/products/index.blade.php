{{-- resources/views/products/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products - MyStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background:
                radial-gradient(1200px 400px at 10% -10%, rgba(109,40,217,.12), transparent 60%),
                radial-gradient(1200px 400px at 90% -20%, rgba(16,185,129,.12), transparent 60%),
                linear-gradient(180deg, #f8f9fb 0%, #eef1f7 100%);
        }

        .card { transition: 0.3s; border: none; border-radius: 15px; overflow: hidden; }
        .card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.15); }

        .product-img { height: 250px; object-fit: cover; }

        .btn-submit {
            background: linear-gradient(135deg, #ff9f00, #fb641b);
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: bold;
        }
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(251,100,27,0.4);
        }

        .avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #0d6efd; }

        .cart-badge {
            position: absolute;
            top: -6px;
            right: -10px;
            background: #dc3545;
            color: white;
            font-size: .7rem;
            width: 20px;
            height: 20px;
            border-radius: 50%;
        }

        .hero {
            background: linear-gradient(135deg, #6d28d9 0%, #4c1d95 50%, #3b82f6 100%);
            color: #fff;
            border-radius: 16px;
            padding: 36px 24px;
            position: relative;
            box-shadow: 0 18px 40px rgba(76,29,149,.25);
        }

        .search-wrap {
            margin-top: -28px;
        }

        .page-wrap {
            padding-top: 84px;
        }

        /* ⭐ FIX ADDED – search bar, category, and button will NOT move ⭐ */
        .search-wrap .card:hover {
            transform: none !important;
            box-shadow: none !important;
        }
        .search-wrap .form-control:hover,
        .search-wrap .form-select:hover,
        .search-wrap button:hover {
            transform: none !important;
        }
        /* ⭐ END FIX ⭐ */

    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('products.index') }}">
            <i class="bi bi-bag-check-fill me-2 text-primary"></i> MyStore
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="topNav">
            <ul class="navbar-nav me-auto"></ul>

            <a href="{{ route('cart.index') }}" class="position-relative me-3 text-dark text-decoration-none">
                <i class="bi bi-cart3 fs-4"></i>
                @if(session('cart') && count(session('cart')) > 0)
                    <span class="cart-badge d-flex align-items-center justify-content-center">
                        {{ count(session('cart')) }}
                    </span>
                @endif
            </a>

            <div class="dropdown">
                <a href="#" data-bs-toggle="dropdown" class="d-flex align-items-center text-decoration-none">
                    @if(auth()->user()->profile_photo)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" class="avatar" alt="Profile">
                    @else
                        <div class="avatar bg-primary text-white d-flex align-items-center justify-content-center fw-bold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    @endif
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="px-3 py-2 small text-muted">{{ auth()->user()->name }}</li>
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Edit Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</nav>

<div class="page-wrap">
    <div class="container py-4">

        <div class="hero mb-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
                <h1 class="display-6 fw-bold mb-3 mb-lg-0">My Products</h1>
                <a href="{{ route('products.create') }}" class="btn btn-submit text-white shadow-lg">
                    <i class="bi bi-plus-circle me-2"></i> Add New Product
                </a>
            </div>
        </div>

        <!-- SEARCH AREA -->
        <div class="search-wrap">
            <form method="GET" action="{{ route('products.index') }}" class="card shadow-sm p-4 mb-5 border-0">
                <div class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control form-control-lg rounded-pill"
                               placeholder="Search products..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="category" class="form-select form-select-lg rounded-pill">
                            <option value="">All Categories</option>
                            @foreach(['Mobile Phones','Laptops','Fashion','Sports','Fruits','Bikes','Furniture','Other'] as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill">
                            <i class="bi bi-search me-1"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card h-100 shadow-lg">
                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top product-img">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold">{{ $product->name }}</h5>
                            <p class="text-success fs-3 fw-bold">₹{{ number_format($product->price) }}</p>
                            <p class="text-muted small flex-grow-1">
                                {{ $product->description ? Str::limit($product->description, 70) : 'No description' }}
                            </p>
                            <div class="mt-auto">
                                <span class="badge bg-warning text-dark fs-6">{{ $product->category }}</span>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-0">
                            <div class="d-flex justify-content-between gap-2">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                    View Details
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">
                                    Edit
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete this product?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <h2 class="text-muted">No products yet!</h2>
                    <a href="{{ route('products.create') }}" class="btn btn-submit text-white mt-3 px-5 py-3 fs-5">
                        Add Your First Product
                    </a>
                </div>
            @endforelse
        </div>

    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
