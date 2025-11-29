{{-- resources/views/cart/index.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>My Cart - MyStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .pay-btn { background: linear-gradient(135deg, #28a745, #20c997); }
        .pay-all-btn { background: linear-gradient(135deg, #ff9f00, #fb641b); font-size: 1.4rem; padding: 18px; }
        .item-card:hover { transform: translateY(-5px); transition: 0.3s; }
    </style>
</head>
<body>

<div class="container py-5">
    <!-- Back + Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-5 fw-bold">My Cart</h1>
        <a href="{{ route('products.index') }}" class="btn btn-outline-dark">
            Continue Shopping
        </a>
    </div>

    @if(session('cart') && count(session('cart')) > 0)
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-lg-8">
                @foreach(session('cart') as $id => $item)
                    <div class="card item-card shadow-sm mb-3">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-3">
                                <img src="{{ asset('storage/' . $item['image']) }}" 
                                     class="img-fluid rounded-start" style="height: 160px; object-fit: cover;">
                            </div>
                            <div class="col-md-9">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="fw-bold">{{ $item['name'] }}</h5>
                                        <p class="text-success fs-4 fw-bold">₹{{ number_format($item['price']) }}</p>
                                        <small class="text-muted">Qty: {{ $item['quantity'] ?? 1 }}</small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <!-- Pay for this item only -->
                                        <form action="{{ route('checkout.single', $id) }}" method="GET">
                                            <button type="submit" class="btn pay-btn text-white shadow">
                                                Pay Now
                                            </button>
                                        </form>
                                        <!-- Remove -->
                                        <form action="{{ route('cart.remove', $id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pay All Button -->
            <div class="col-lg-4">
                <div class="card shadow-lg sticky-top" style="top: 20px;">
                    <div class="card-body text-center">
                        <h3 class="fw-bold mb-4">Order Summary</h3>
                        <h2 class="text-success">
                            ₹{{ number_format(collect(session('cart'))->sum(fn($i) => $i['price'] * ($i['quantity'] ?? 1))) }}
                        </h2>
                        <p class="text-muted">{{ count(session('cart')) }} items</p>
                        <a href="{{ route('checkout') }}" class="btn pay-all-btn text-white w-100 shadow-lg rounded-pill">
                            Pay All Items
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h2 class="mt-4">Your cart is empty</h2>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg mt-3">Start Shopping</a>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>