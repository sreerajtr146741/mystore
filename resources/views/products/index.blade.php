{{-- resources/views/products/index.blade.php --}}
@php
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Str;

  $img = function($path){
    if(!$path) return null;
    if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
    try { return Storage::url($path); } catch (\Throwable $e) { return $path; }
  };
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Products • MyStore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body{
      min-height:100vh;
      background:
        radial-gradient(1200px 400px at 10% -10%, rgba(99,102,241,.10), transparent 60%),
        radial-gradient(1200px 400px at 90% -20%, rgba(16,185,129,.10), transparent 60%),
        linear-gradient(180deg,#f8f9fb 0%, #eef1f7 100%);
      color:#0f172a;
    }
    .navbar{ position:sticky; top:0; z-index:20; }
    .hero{
      background: linear-gradient(120deg,#6d28d9 0%, #4c1d95 45%, #3b82f6 100%);
      color:#fff; border-radius:16px; padding:28px 22px;
      box-shadow:0 18px 40px rgba(76,29,149,.25);
    }
    .search-card{ margin-top:-22px; border:0; border-radius:16px; }
    .search-input, .search-select{ height:52px; border-radius:14px; }
    .btn-go{
      height:52px; border-radius:14px; font-weight:700; border:0; color:#07101a;
      background:linear-gradient(135deg,#22d3ee,#60a5fa);
      box-shadow:0 10px 22px rgba(96,165,250,.25);
    }
    .card-prod{
      position:relative;
      border:0; border-radius:16px; overflow:hidden; transition:.25s; background:#fff;
      box-shadow:0 6px 20px rgba(2,6,23,.06);
      cursor:pointer;
    }
    .card-prod:hover{ transform:translateY(-6px); box-shadow:0 16px 32px rgba(2,6,23,.12); }
    .img-fit{ width:100%; aspect-ratio:4/3; object-fit:cover; display:block; }
    .price{ font-weight:800; }
    .strike{ text-decoration: line-through; opacity:.7; margin-right:.4rem; }
    .badge-cat{ background:#fff3c4; color:#7c2d12; border:1px solid #fde68a; }
    .muted{ color:#64748b; }
    /* Discount ribbon */
    .ribbon{
      --c:#22c55e;
      position:absolute; top:12px; left:-40px; background:var(--c); color:#fff;
      padding:6px 60px; transform:rotate(-35deg); font-weight:700; box-shadow:0 6px 16px rgba(34,197,94,.3);
      letter-spacing:.5px; font-size:.85rem;
    }
    .stock-dot{ width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:.4rem; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ route('products.index') }}">MyStore</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="topNav">
      <ul class="navbar-nav me-auto"></ul>

      {{-- Cart --}}
      <a href="{{ route('cart.index') }}" class="position-relative me-3 text-decoration-none text-dark" aria-label="Cart">
        <i class="bi bi-cart fs-4"></i>
        @php $cart = session('cart', []); @endphp
        @if(!empty($cart))
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            {{ count($cart) }}
          </span>
        @endif
      </a>

      {{-- Account --}}
      @auth
      <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
          <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center"
               style="width:40px;height:40px;">
            {{ strtoupper(mb_substr(auth()->user()->name,0,1)) }}
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li class="px-3 py-2 small text-muted">{{ auth()->user()->name }}</li>
          <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Edit Profile</a></li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form method="POST" action="{{ route('logout') }}">@csrf
              <button class="dropdown-item text-danger">Logout</button>
            </form>
          </li>
        </ul>
      </div>
      @endauth
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="hero mb-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
    <h1 class="h3 fw-bold mb-2 mb-lg-0">Explore Products</h1>
    <div class="small opacity-75">Fresh arrivals are shown first</div>
  </div>

  {{-- Search + Category --}}
  <form method="GET" action="{{ route('products.index') }}" class="card search-card shadow p-4">
    <div class="row g-3 align-items-center">
      <div class="col-md-6">
        <input type="text" name="search" class="form-control search-input"
               placeholder="Search by name or description…" value="{{ request('search') }}">
      </div>
      <div class="col-md-4">
        <select name="category" class="form-select search-select">
          <option value="">All Categories</option>
          @foreach(['Mobile Phones','Laptops','Tablets','Smart Watches','Headphones','Cameras','TVs','Gaming','Fashion','Shoes','Bags','Watches','Furniture','Home Decor','Kitchen','Sports','Gym & Fitness','Vehicles','Cars','Bikes','Accessories','Fruits','Vegetables','Groceries','Books','Toys','Other'] as $cat)
            <option value="{{ $cat }}" {{ request('category')===$cat ? 'selected' : '' }}>{{ $cat }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 d-grid">
        <button class="btn btn-go"><i class="bi bi-search me-1"></i>Search</button>
      </div>
    </div>
  </form>

  {{-- Flash --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow mt-3">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Grid --}}
  @if($products->count())
    <div class="row g-4 mt-1">
      @foreach($products as $p)
        @php
          $photo   = $img($p->image);
          $hasDisc = ($p->discount_type && $p->discount_value>0 && ($p->is_discount_active ?? false));
          $final   = $p->price;
          if($hasDisc){
            $final = $p->discount_type==='percent'
              ? max(0, $p->price - ($p->price * ($p->discount_value/100)))
              : max(0, $p->price - $p->discount_value);
          }
          $saveAmt = max(0, $p->price - $final);
          $savePct = $p->price > 0 ? round(($saveAmt / $p->price) * 100) : 0;
          $stock   = $p->stock ?? $p->qty ?? null; // supports different column names
          $href    = route('products.show', $p);
        @endphp

        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
          <div class="card card-prod h-100" data-href="{{ $href }}">
            {{-- Discount ribbon --}}
            @if($hasDisc)
              <div class="ribbon">
                {{ $p->discount_type==='percent' ? ($p->discount_value.'% OFF') : ('Save ₹'.number_format($saveAmt)) }}
              </div>
            @endif

            {{-- Image --}}
            @if($photo)
              <img class="img-fit" src="{{ $photo }}" alt="{{ $p->name }}">
            @else
              <div class="bg-light d-flex align-items-center justify-content-center" style="aspect-ratio:4/3;">
                <i class="bi bi-image fs-1 text-muted"></i>
              </div>
            @endif

            <div class="card-body d-flex flex-column">
              {{-- Name --}}
              <h5 class="fw-bold mb-1 text-truncate" title="{{ $p->name }}">{{ $p->name }}</h5>

              {{-- Category --}}
              <div class="mb-2">
                @if($p->category)
                  <span class="badge badge-cat">{{ $p->category }}</span>
                @endif
              </div>

              {{-- Description --}}
              <p class="muted small flex-grow-1">{{ $p->description ? Str::limit($p->description, 90) : 'No description' }}</p>

              {{-- Stock --}}
              @if(!is_null($stock))
                <div class="small {{ $stock>0 ? 'text-success' : 'text-danger' }}">
                  <span class="stock-dot" style="background:{{ $stock>0 ? '#22c55e' : '#ef4444' }}"></span>
                  {{ $stock>0 ? ($stock.' in stock') : 'Out of stock' }}
                </div>
              @endif

              {{-- Price + discount details --}}
              <div class="mt-2">
                @if($hasDisc)
                  <div>
                    <span class="strike muted">₹{{ number_format($p->price,2) }}</span>
                    <span class="price text-success">₹{{ number_format($final,2) }}</span>
                  </div>
                  <div class="small text-success">
                    You save ₹{{ number_format($saveAmt,2) }} ({{ $savePct }}%)
                  </div>
                @else
                  <span class="price">₹{{ number_format($p->price,2) }}</span>
                @endif
              </div>

              {{-- Add to Cart (with qty) --}}
              <div class="d-flex align-items-center justify-content-between mt-3">
                <form method="POST" action="{{ route('cart.add', $p) }}" class="d-flex align-items-center gap-2 stop-click">
                  @csrf
                  <input type="number" name="qty" value="1" min="1"
                         @if(!is_null($stock)) max="{{ max(1,(int)$stock) }}" @endif
                         class="form-control form-control-sm" style="width:80px;">
                  <button class="btn btn-primary btn-sm" @if(!is_null($stock) && $stock<=0) disabled @endif>
                    <i class="bi bi-bag-plus me-1"></i>Add to Cart
                  </button>
                </form>

                {{-- (Removed "View" button; whole card is clickable) --}}
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-4">
      {{ $products->withQueryString()->links() }}
    </div>
  @else
    <div class="text-center text-muted py-5">No products yet.</div>
  @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Make entire card clickable, except inside interactive elements
  document.querySelectorAll('.card-prod').forEach(card => {
    card.addEventListener('click', function(e){
      const block = e.target.closest('.stop-click, button, a, input, select, textarea, label');
      if(block) return; // don't navigate when interacting with controls
      const href = this.getAttribute('data-href');
      if(href) window.location.href = href;
    });
  });
</script>
</body>
</html>
