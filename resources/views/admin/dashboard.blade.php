{{-- resources/views/admin/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard • MyStore</title>

  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">

  <style>
    body { margin:0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .text-gradient { 
      background: linear-gradient(90deg, #00d4ff, #ff00c8);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    .card-glass { background: rgba(255,255,255,0.08); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.15); }
    .hover-lift:hover { transform: translateY(-10px) scale(1.02); box-shadow: 0 30px 60px rgba(0,0,0,0.35)!important; }
    .backdrop-blur-lg { backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
    .btn-cyan { background: linear-gradient(135deg, #06b6d4, #0891b2); }
    .btn-indigo { background: linear-gradient(135deg, #6366f1, #4f46e5); }
    .bg-gradient-green { background: linear-gradient(135deg, #10b981, #059669); }
    .bg-gradient-warning { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
    .minvh { min-height: 100vh; }
    .z-50{ z-index:50 }
  </style>
</head>
<body>

<!-- top-right profile dropdown -->
<div class="position-absolute top-0 end-0 p-4 z-50">
  <div class="dropdown">
    <button class="btn backdrop-blur-lg text-white border border-white border-opacity-25 dropdown-toggle d-flex align-items-center gap-3 shadow"
            data-bs-toggle="dropdown" type="button">
      <img src="{{ auth()->user()->profile_photo_url ?? asset('images/avatar.png') }}" class="rounded-circle border border-white border-3 shadow" width="50" height="50">
      <div class="text-start">
        <div class="fw-bold">{{ auth()->user()->name }}</div>
        <small class="text-info">Super Admin</small>
      </div>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" style="min-width:220px;">
      <li>
        <a href="{{ route('profile.edit') }}" class="dropdown-item d-flex align-items-center gap-3 py-3">
          <i class="bi bi-person-circle fs-4 text-info"></i>
          <div><div class="fw-bold">My Profile</div><small class="text-muted">Edit personal info</small></div>
        </a>
      </li>
      <li><hr class="dropdown-divider"></li>
      <li>
        <form method="POST" action="{{ route('logout') }}" class="m-0">@csrf
          <button type="submit" class="dropdown-item d-flex align-items-center gap-3 py-3 text-danger">
            <i class="bi bi-box-arrow-right fs-4"></i>
            <div><div class="fw-bold">Logout</div><small class="text-muted">End admin session</small></div>
          </button>
        </form>
      </li>
    </ul>
  </div>
</div>

<div class="minvh d-flex flex-column">
  <div class="container py-5 pt-5">

    <!-- hero -->
    <div class="text-center text-white mb-5">
      <h1 class="display-4 fw-bold mb-2">
        Welcome back, <span class="text-warning">{{ auth()->user()->name }}</span>!
      </h1>
      <p class="lead opacity-90">Here's what's happening in your store today</p>
      <div class="mt-3">
        <span class="badge bg-gradient-green text-dark fs-6 px-4 py-2 rounded-pill shadow">
          Super Admin • Full Control
        </span>
      </div>
    </div>

    <!-- stats -->
    <div class="row g-4 mb-5">
      <div class="col-md-6 col-lg-3">
        <div class="card card-glass text-white border-0 rounded-4 hover-lift">
          <div class="card-body text-center p-5">
            <i class="bi bi-people-fill fs-1 text-info mb-3"></i>
            <h2 class="display-6 fw-bold mb-1">{{ $stats['total_users'] }}</h2>
            <p class="mb-0 opacity-90">Total Users</p>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card card-glass text-white border-0 rounded-4 hover-lift">
          <div class="card-body text-center p-5">
            <i class="bi bi-box-seam-fill fs-1 text-success mb-3"></i>
            <h2 class="display-6 fw-bold mb-1">{{ $stats['total_products'] ?? 0 }}</h2>
            <p class="mb-0 opacity-90">Total Products</p>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card card-glass text-white border-0 rounded-4 hover-lift">
          <div class="card-body text-center p-5">
            <i class="bi bi-person-check-fill fs-1 text-warning mb-3"></i>
            <h2 class="display-6 fw-bold mb-1">{{ $stats['pending_sellers'] ?? 0 }}</h2>
            <p class="mb-0 opacity-90">Pending Sellers</p>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card card-glass text-white border-0 rounded-4 hover-lift">
          <div class="card-body text-center p-5">
            <i class="bi bi-cart-check-fill fs-1 text-danger mb-3"></i>
            <h2 class="display-6 fw-bold mb-1">{{ $stats['total_orders'] ?? 0 }}</h2>
            <p class="mb-0 opacity-90">Total Orders</p>
          </div>
        </div>
      </div>
    </div>

    <!-- pending sellers alert -->
    @if(($stats['pending_sellers'] ?? 0) > 0)
    <div class="alert bg-gradient-warning border-0 rounded-4 shadow text-dark mb-5 animate__animated animate__headShake animate__slower">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center">
          <i class="bi bi-bell-fill fs-1 me-4 text-danger"></i>
          <div>
            <h4 class="fw-bold mb-1">{{ $stats['pending_sellers'] }} Seller Application(s) Pending!</h4>
            <p class="mb-0 opacity-90">New sellers are waiting to join your marketplace</p>
          </div>
        </div>
        <a href="{{ route('admin.seller-applications') }}" class="btn btn-dark btn-lg rounded-pill px-5 shadow">
          Review Now <i class="bi bi-arrow-right ms-2"></i>
        </a>
      </div>
    </div>
    @endif

    <!-- control center -->
    <div class="text-center mb-5">
      <h2 class="text-white mb-4 fw-light">Control Center</h2>
      <div class="d-flex flex-wrap justify-content-center gap-3">
        <a href="{{ route('admin.users') }}" class="btn btn-cyan text-white btn-lg px-5 py-3 rounded-pill shadow"> <i class="bi bi-people-fill me-2"></i> Manage Users </a>
        <a href="{{ route('admin.products') }}" class="btn btn-indigo text-white btn-lg px-5 py-3 rounded-pill shadow"> <i class="bi bi-grid-3x3-gap-fill me-2"></i> All Products </a>
        <a href="{{ route('admin.seller-applications') }}" class="btn btn-warning btn-lg px-5 py-3 rounded-pill text-dark shadow">
          <i class="bi bi-person-check-fill me-2"></i> Review Sellers
          <span class="badge bg-danger ms-2">{{ $stats['pending_sellers'] ?? 0 }}</span>
        </a>
        <button type="button" onclick="alert('Analytics Dashboard • Coming Soon!')" class="btn btn-success btn-lg px-5 py-3 rounded-pill shadow">
          <i class="bi bi-graph-up-arrow me-2"></i> Analytics Report
        </button>
      </div>
    </div>

    <!-- footer -->
    <div class="text-center text-white-50 pb-4">
      <p class="mb-0">
        <i class="bi bi-clock-history"></i> Updated: {{ now()->format('d M Y • h:i A') }} • MyStore Admin Panel v2.0
      </p>
    </div>

  </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
