{{-- resources/views/products/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
    .navbar { position: sticky; top:0; z-index: 20; }
    .card { transition: .3s; border: none; border-radius: 15px; overflow: hidden; cursor: pointer; position: relative; }
    .card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.15); }
    .product-img { height: 250px; object-fit: cover; }
    .btn-submit { background: linear-gradient(135deg, #ff9f00, #fb641b); border: none; padding: 12px 30px; border-radius: 12px; font-weight: bold; color: #fff; }
    .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(251,100,27,0.4); }
    .avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #0d6efd; }
    .cart-badge { position: absolute; top: -6px; right: -10px; background: #dc3545; color:#fff; font-size:.7rem; width: 20px; height: 20px; border-radius: 50%; display:flex; align-items:center; justify-content:center; }
    .hero { background: linear-gradient(135deg, #6d28d9 0%, #4c1d95 50%, #3b82f6 100%); color: #fff; border-radius: 16px; padding: 36px 24px; position: relative; box-shadow: 0 18px 40px rgba(76,29,149,.25); }
    .search-wrap { margin-top: -28px; }
    .page-wrap { padding-top: 20px; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ route('products.index') }}">MyStore</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="topNav">
      <ul class="navbar-nav me-auto"></ul>

      <!-- Cart icon (click → cart page) -->
      <a href="{{ route('cart.index') }}" class="position-relative me-3 text-dark text-decoration-none" aria-label="Cart">
        <i class="bi bi-cart fs-4"></i>
        @if(session('cart') && count(session('cart')) > 0)
          <span class="cart-badge">{{ count(session('cart')) }}</span>
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
          <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Edit Profile</a></li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form method="POST" action="{{ route('logout') }}">@csrf
              <button type="submit" class="dropdown-item text-danger">Logout</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<div class="page-wrap">
  <div class="container py-4">
    <div class="hero mb-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
      <h1 class="display-6 fw-bold mb-3 mb-lg-0">My Products</h1>
      <!-- Add button intentionally removed -->
    </div>

    <div class="search-wrap">
      <form method="GET" action="{{ route('products.index') }}" class="card shadow-sm p-4 mb-4 border-0">
        <div class="row g-3 align-items-center">
          <div class="col-md-5">
            <input type="text" name="search" class="form-control form-control-lg rounded-pill" placeholder="Search products..." value="{{ request('search') }}">
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
            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill">Search</button>
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
        <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
          <div class="card h-100 shadow-lg">
            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top product-img" alt="{{ $product->name }}">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title fw-bold text-dark">{{ $product->name }}</h5>
              <p class="text-success fs-4 fw-bold mb-2">₹{{ number_format($product->price) }}</p>
              <p class="text-muted small flex-grow-1">
                {{ $product->description ? Str::limit($product->description, 70) : 'No description' }}
              </p>
              <div class="mt-auto">
                <span class="badge bg-warning text-dark fs-6">{{ $product->category }}</span>
              </div>
            </div>
          </div>
        </a>
      </div>
      @empty
      <div class="col-12 text-center py-5">
        <h2 class="text-muted">No products yet!</h2>
      </div>
      @endforelse
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
