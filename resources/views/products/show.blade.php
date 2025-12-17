{{-- resources/views/products/show.blade.php --}}
@php
    use Illuminate\Support\Facades\Storage;
    $p = $product;

    // Image helper: supports storage path or full URL
    $img = function($path){
        if(!$path) return null;
        if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
        if (Storage::disk('public')->exists($path)) return asset('storage/'.$path);
        return $path;
    };

    $photo = $img($p->image);

    // Use final_price from controller (priority) — fallback to old logic only if not exist
    // Use discounted_price (includes Category discount) as priority
    $final = $p->discounted_price ?? $p->final_price ?? $p->price;
    $hasDisc = $final < $p->price;

    // Keep your old logic as backup (safe)
    if (!$hasDisc && ($p->discount_type && $p->discount_value > 0 && ($p->is_discount_active ?? false))) {
        $final = $p->discount_type === 'percent'
            ? max(0, $p->price - ($p->price * ($p->discount_value/100)))
            : max(0, $p->price - $p->discount_value);
        $hasDisc = true;
    }

    $saveAmt = max(0, $p->price - $final);
    $savePct = $p->price > 0 ? round(($saveAmt / $p->price) * 100) : 0;
    $stock   = $p->stock ?? $p->qty ?? null;

    // Cart total qty for badge
    $cart = session('cart', []);
    $cartQty = collect($cart)->sum('qty');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $p->name }} • MyStore</title>
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
            color:#fff; border-radius:16px; padding:22px; box-shadow:0 18px 40px rgba(76,29,149,.25);
        }
        .gallery{ border-radius:24px; overflow:hidden; background:#fff; box-shadow:0 20px 40px rgba(0,0,0,.08); display: flex; align-items: center; justify-content: center; padding: 40px; }
        .img-main{ max-width:100%; height:auto; max-height:600px; object-fit:contain; display:block; }
        .badge-cat{ background:#fff3c4; color:#7c2d12; border:1px solid #fde68a; }
        .stock-dot{ width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:.4rem; }
        .strike{ text-decoration: line-through; opacity:.7; margin-right:.4rem; }
        .price-lg{ font-size:2.2rem; font-weight:800; }
        .card-sticky{ position:sticky; top:84px; border:0; border-radius:16px; overflow:hidden; background:#fff; box-shadow:0 10px 26px rgba(2,6,23,.08); }
        .ribbon{
            --c:#22c55e;
            position:absolute; top:14px; left:-40px; background:var(--c); color:#fff;
            padding:6px 60px; transform:rotate(-35deg); font-weight:700; box-shadow:0 6px 16px rgba(34,197,94,.3);
            letter-spacing:.5px; font-size:.85rem;
        }
        .btn-cart{
            background: linear-gradient(135deg, #ff9f00, #fb641b);
            border: none; padding: 12px 18px; font-weight:700; color:#fff;
            box-shadow:0 10px 20px rgba(251,100,27,.25);
            transition: all 0.3s ease;
            border-radius: 50px;
        }
        .btn-cart:hover{
            background: linear-gradient(135deg, #ffb84d, #ff8c42) !important;
            transform: translateY(-4px);
            box-shadow: 0 16px 30px rgba(251,100,27,.4);
            color: #fff !important;
        }
        .btn-buynow{
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border: none; padding: 12px 18px; font-weight:700; color:#fff;
            box-shadow:0 10px 20px rgba(34,197,94,.25);
            transition: all 0.3s ease;
            border-radius: 50px;
        }
        .btn-buynow:hover{
            background: linear-gradient(135deg, #4ade80, #22c55e) !important;
            transform: translateY(-4px);
            box-shadow: 0 16px 30px rgba(34,197,94,.4);
            color: #fff !important;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('products.index') }}"><i class="bi bi-bag-fill me-2"></i>MyStore</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="topNav">
            <ul class="navbar-nav me-auto"></ul>
            @php $cart = session('cart', []); @endphp
            <a href="{{ route('cart.index') }}" class="position-relative me-3 text-decoration-none text-dark" aria-label="Cart">
                <i class="bi bi-cart fs-4"></i>
                @if($cart && count($cart) > 0)
                    <span class="position-absolute translate-middle badge rounded-pill bg-danger" style="top:0; right:-6px;">
                        {{ count($cart) }}
                    </span>
                @endif
            </a>

            {{-- Profile Dropdown (same as index page) --}}
            @auth
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center"
                             style="width:40px;height:40px;">
                            {{ strtoupper(mb_substr(auth()->user()->first_name ?? auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2 small text-muted">{{ auth()->user()->email }}</li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Edit Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('orders.index') }}">My Orders</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">@csrf
                                <button class="dropdown-item text-danger">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
            @endauth
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="hero mb-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
        <h1 class="display-6 fw-bold mb-2 mb-lg-0" style="letter-spacing: -0.03em;">{{ $p->name }}</h1>
        @if($p->category)
            <span class="badge badge-cat">{{ $p->category }}</span>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="position-relative gallery mb-3">
                @if($hasDisc)
                    <div class="ribbon">
                        {{ $savePct }}% OFF
                    </div>
                @endif
                @if($photo)
                    <img src="{{ $photo }}" alt="{{ $p->name }}" class="img-main">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="aspect-ratio:4/3;">
                        <i class="bi bi-image fs-1 text-muted"></i>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card card-sticky">
                <div class="card-body p-4">
                    @if(!is_null($stock))
                        <div class="mb-2 {{ $stock>0 ? 'text-success' : 'text-danger' }}">
                            <span class="stock-dot" style="background:{{ $stock>0 ? '#22c55e' : '#ef4444' }}"></span>
                            {{ $stock>0 ? ($stock.' in stock') : 'Out of stock' }}
                        </div>
                    @endif

                    <div class="mb-2">
                        @if($hasDisc)
                            <div>
                                <span class="strike">₹{{ number_format($p->price,2) }}</span>
                                <span class="price-lg text-success">₹<span id="unit_final">{{ number_format($final,2) }}</span></span>
                            </div>
                            <div class="small text-success">
                                You save ₹{{ number_format($saveAmt,2) }} ({{ $savePct }}%)
                            </div>
                        @else
                            <span class="price-lg">₹<span id="unit_final">{{ number_format($final,2) }}</span></span>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <label for="qty" class="small text-muted mb-0">Qty</label>
                        <input type="number" id="qty" value="1" min="1"
                               @if(!is_null($stock)) max="{{ max(1,(int)$stock) }}" @endif
                               class="form-control" style="width:110px;">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="small text-muted">Total</div>
                        <div class="fw-bold fs-5">₹ <span id="total_price">{{ number_format($final,2) }}</span></div>
                    </div>

                    <div class="d-flex gap-3 flex-wrap">
                        @auth
                            <form action="{{ route('cart.add', $p) }}" method="POST" class="d-inline flex-fill">
                                @csrf
                                <input type="hidden" name="qty" id="qty_add" value="1">
                                <button type="submit" class="btn btn-cart rounded-pill shadow w-100">
                                    Add to Cart
                                </button>
                            </form>

                            <form action="{{ route('checkout.single', $p->id) }}" method="GET" class="d-inline flex-fill">
                                <input type="hidden" name="qty" id="qty_buy" value="1">
                                <button type="submit" class="btn btn-buynow rounded-pill shadow w-100">
                                    Buy Now
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary rounded-pill shadow px-5">
                                Login to Buy
                            </a>
                        @endauth
                    </div>

                    <div class="mt-4 small text-muted">
                        Fast delivery • Secure checkout • Easy returns
                    </div>
                    <div class="mt-3 small text-muted">
                        Added on {{ $p->created_at?->format('d M Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Full Width Description --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Description</h5>
                    <p class="mb-0 lh-lg text-secondary" style="white-space: pre-wrap;">{{ $p->description ?: 'No description available.' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="position-fixed bottom-0 end-0 p-4" style="z-index: 9999;">
        <div class="toast show align-items-center text-white bg-success border-0 shadow-lg" role="alert">
            <div class="d-flex">
                <div class="toast-body fs-6 fw-bold">{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Live quantity sync + total price update
    (function(){
        const qtyInput   = document.getElementById('qty');
        const totalEl    = document.getElementById('total_price');
        const qtyAdd     = document.getElementById('qty_add');
        const qtyBuy     = document.getElementById('qty_buy');
        const unitPrice  = {{ $final }};

        function updateTotal() {
            const qty = Math.max(1, parseInt(qtyInput.value) || 1);
            if (qtyAdd) qtyAdd.value = qty;
            if (qtyBuy) qtyBuy.value = qty;
            const total = (unitPrice * qty).toFixed(2);
            totalEl.textContent = Number(total).toLocaleString('en-IN');
        }

        qtyInput?.addEventListener('input', updateTotal);
        updateTotal();
    })();

    // Auto-show toast
    document.addEventListener('DOMContentLoaded', () => {
        const toastEl = document.querySelector('.toast.show');
        if (toastEl) new bootstrap.Toast(toastEl, { delay: 3000 }).show();
    });
</script>
</body>
</html>