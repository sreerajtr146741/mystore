@extends('layouts.master')

@section('title', 'My Cart • MyStore')

@push('styles')
<style>
    body{background:linear-gradient(180deg,#f8f9fb 0%,#eef1f7 100%);}
    .item-img{width:72px;height:72px;object-fit:cover;border-radius:10px}
    .empty{padding:60px 0}
    .total-card{border-radius:16px}
    .btn-checkout{background:linear-gradient(135deg,#10b981,#059669);border:none}
    .btn-continue{background:linear-gradient(135deg,#6366f1,#4f46e5);border:none}
    .btn-buy{background:linear-gradient(135deg,#ff9f00,#fb641b);border:none;color:#fff}
</style>
@endpush

@section('content')
<div class="container py-4">
  <h1 class="h3 fw-bold mb-4">Your Cart</h1>

  @php
    $cart = session('cart', []);
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

                      {{-- Per-item Buy Now --}}
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

            {{-- All-items checkout --}}
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
@endsection
