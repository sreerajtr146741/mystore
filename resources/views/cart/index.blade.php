{{-- resources/views/cart/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Cart • MyStore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body{background:linear-gradient(180deg,#f8f9fb 0%,#eef1f7 100%);}
    .navbar{position:sticky;top:0;z-index:20}
    .item-img{width:72px;height:72px;object-fit:cover;border-radius:10px}
    .empty{padding:60px 0}
    .total-card{border-radius:16px}
    .btn-checkout{background:linear-gradient(135deg,#10b981,#059669);border:none}
    .btn-continue{background:linear-gradient(135deg,#6366f1,#4f46e5);border:none}
    .btn-buy{background:linear-gradient(135deg,#ff9f00,#fb641b);border:none;color:#fff}
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="{{ route('products.index') }}"><i class="bi bi-bag-fill me-2"></i>MyStore</a>

    <div class="ms-auto d-flex align-items-center gap-3">
      <a href="{{ route('cart.index') }}" class="position-relative text-dark text-decoration-none" aria-label="Cart">
        <i class="bi bi-cart fs-4"></i>
        @php $cart = session('cart', []); @endphp
        @if($cart && count($cart) > 0)
          <span class="position-absolute translate-middle badge rounded-pill bg-danger" style="top:0; right:-6px;">
            {{ count($cart) }}
          </span>
        @endif
      </a>

      <div class="dropdown">
        @auth
          <a href="#" data-bs-toggle="dropdown" class="d-flex align-items-center text-decoration-none">
            @if(auth()->user()->profile_photo ?? false)
              <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Profile" class="rounded-circle" width="40" height="40" style="object-fit:cover;border:2px solid #0d6efd;">
            @else
              <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:40px;height:40px;border:2px solid #0d6efd;">
                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
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
        @else
          <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4">Login</a>
        @endauth
      </div>
    </div>
  </div>
</nav>

<div class="container py-4">
  <h1 class="h3 fw-bold mb-4">Your Cart</h1>

  @php
    $cart = session('cart', []);
    // Expected: [productId => ['name','price','qty','image','category','description']]
    $total = 0;
  @endphp

  @if(!$cart || count($cart) === 0)
    <div class="text-center empty">
      <img src="https://img.icons8.com/ios-filled/150/cccccc/shopping-cart.png" alt="">
      <h2 class="mt-3 text-muted">Your cart is empty</h2>
      <a href="{{ route('products.index') }}" class="btn btn-continue text-white mt-3 px-4 py-2">Continue Shopping</a>
    </div>
  @else
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
          <div class="card-body p-0">

            @foreach($cart as $pid => $item)
              @php
                $qty = (int)($item['qty'] ?? 1);
                $price = (float)($item['price'] ?? 0);
                $line = $qty * $price;
                $total += $line;
              @endphp
              <div class="p-3 p-md-4 border-bottom">
                <div class="d-flex align-items-center gap-3">
                  <img class="item-img" src="{{ isset($item['image']) ? asset('storage/'.$item['image']) : 'https://img.icons8.com/ios-filled/100/0d6efd/box.png' }}" alt="">
                  <div class="flex-grow-1">
                    <div class="fw-bold">{{ $item['name'] ?? 'Product #'.$pid }}</div>
                    <div class="text-muted small">{{ $item['category'] ?? '' }}</div>
                    <div class="mt-1 d-flex align-items-center gap-3 flex-wrap">
                      <span class="badge bg-light text-dark">Qty: {{ $qty }}</span>

                      {{-- Per-item Buy Now → payment for this item --}}
                      <form action="{{ route('checkout.single', $pid) }}" method="GET" class="m-0">
                        <button class="btn btn-sm btn-buy px-3 py-1 rounded-pill">
                          <i class="bi bi-lightning-charge-fill me-1"></i> Buy Now
                        </button>
                      </form>
                    </div>
                  </div>
                  <div class="text-end">
                    <div class="fw-bold">₹{{ number_format($line) }}</div>
                    <div class="text-muted small">₹{{ number_format($price) }} each</div>
                  </div>
                </div>
              </div>
            @endforeach

          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card total-card shadow-sm border-0">
          <div class="card-body">
            <h5 class="fw-bold mb-3">Summary</h5>
            <div class="d-flex justify-content-between">
              <span>Items</span>
              <span>{{ count($cart) }}</span>
            </div>
            <div class="d-flex justify-content-between mt-2">
              <span>Subtotal</span>
              <span class="fw-semibold">₹{{ number_format($total) }}</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between h5">
              <span>Total</span>
              <span class="fw-bold text-success">₹{{ number_format($total) }}</span>
            </div>

            {{-- All-items checkout → payment page --}}
            <a href="{{ route('checkout.index') }}" class="btn btn-checkout w-100 text-white mt-3">
              <i class="bi bi-lock-fill me-1"></i> Proceed to Checkout
            </a>

            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">
              Continue Shopping
            </a>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
