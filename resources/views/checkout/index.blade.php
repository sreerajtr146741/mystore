{{-- resources/views/checkout/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Secure Checkout - MyStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: #6d28d9; --success: #10b981; }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .checkout-wrapper { max-width: 1100px; margin: 2rem auto; }
        .card { border: none; border-radius: 24px; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.2); }
        .price { font-size: 3rem; color: #d32f2f; font-weight: 900; }
        .btn-pay {
            background: linear-gradient(135deg, #10b981, #059669);
            padding: 20px 80px; font-size: 1.8rem; font-weight: bold;
            border: none; border-radius: 50px;
            box-shadow: 0 15px 35px rgba(16,185,129,0.4);
        }
        .payment-tab-icon { font-size: 1.8rem; margin-right: 8px; }
        .upi-section img { width: 100px; margin-bottom: 1.5rem; }
        .credit-card-input { font-family: 'Courier New', monospace; letter-spacing: 6px; font-size: 1.6rem; text-align: center; }
    </style>
</head>
<body>

<div class="container checkout-wrapper">
    <div class="row g-5">
        <!-- LEFT: Order Summary -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-gradient text-white py-4" style="background: linear-gradient(135deg, #6d28d9,#4c1d95)!important;">
                    <h3 class="mb-0 fw-bold">Order Summary</h3>
                </div>
                <div class="card-body p-5">
                    @php
                        $subtotal = collect($items)->sum('price');
                        $shipping = $subtotal < 300 ? 59 : 0;
                        $total = $subtotal + $shipping;
                    @endphp
                    @foreach($items as $item)
                    <div class="d-flex gap-4 py-4 border-bottom">
                        @if($item['image'] ?? null)
                            <img src="{{ asset('storage/'.$item['image']) }}" width="90" class="rounded-3 shadow">
                        @else
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width:90px;height:90px;">
                                <i class="bi bi-box fs-2 text-muted"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <h5 class="fw-bold">{{ $item['name'] }}</h5>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <span class="badge bg-success fs-6">Special Offer</span>
                                @if($subtotal >= 300)
                                    <span class="badge bg-success-subtle text-success fs-6">Free Delivery</span>
                                @endif
                            </div>
                            <p class="fs-4 fw-bold text-success mt-2">₹{{ number_format($item['price']) }}</p>
                        </div>
                    </div>
                    @endforeach
                    <div class="bg-light rounded-4 p-4 mt-4">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fs-5">Subtotal</span>
                            <strong class="fs-5">₹{{ number_format($subtotal) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3 {{ $shipping == 0 ? 'text-success' : 'text-warning' }}">
                            <span class="fs-5">Delivery Charge</span>
                            <strong class="fs-5">
                                {{ $shipping == 0 ? 'FREE' : '₹59' }}
                                @if($subtotal < 300)
                                    <small class="text-muted d-block">Add ₹{{ 300 - $subtotal }} more for FREE delivery</small>
                                @endif
                            </strong>
                        </div>
                        <hr class="my-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="fw-bold mb-0">Total Amount</h4>
                            <h2 class="price mb-0">₹{{ number_format($total) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Payment + Delivery -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white border-0 py-4 text-center">
                    <h3 class="fw-bold mb-0">Secure Payment</h3>
                </div>
                <div class="card-body p-5">
                    @if(session('paid'))
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 8rem;"></i>
                            <h1 class="display-4 fw-bold text-success mt-4">Payment Successful!</h1>
                            <a href="{{ route('products.index') }}" class="btn btn-success btn-lg px-5 mt-4 rounded-pill shadow">
                                Continue Shopping
                            </a>
                        </div>

                    @elseif(session('cancelled'))
                        <div class="text-center py-5">
                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 8rem;"></i>
                            <h1 class="display-4 fw-bold text-danger mt-4">Payment Cancelled</h1>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg px-5 mt-4 rounded-pill">
                                Back to Shopping
                            </a>
                        </div>

                    @else

                    <!-- PAYMENT SECTION STARTS -->
                    <ul class="nav nav-pills mb-5 justify-content-center gap-3">
                        <li class="nav-item">
                            <a class="nav-link active px-4 py-3 rounded-pill" data-bs-toggle="pill" href="#upi">
                                <i class="bi bi-phone-fill payment-tab-icon"></i> UPI
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3 rounded-pill" data-bs-toggle="pill" href="#card">
                                <i class="bi bi-credit-card-2-back-fill payment-tab-icon"></i> Card
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3 rounded-pill" data-bs-toggle="pill" href="#netbank">
                                <i class="bi bi-bank payment-tab-icon"></i> Net Banking
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4 py-3 rounded-pill" data-bs-toggle="pill" href="#cod">
                                <i class="bi bi-cash-stack payment-tab-icon"></i> COD
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="upi">
                            <div class="text-center py-5">
                                <img src="https://img.icons8.com/color/120/000000/upi.png" alt="UPI">
                                <input type="text" class="form-control form-control-lg text-center mt-4"
                                       placeholder="yourname@upi" style="font-size:1.5rem;">
                            </div>
                        </div>

                        <div class="tab-pane fade" id="card">
                            <div class="p-4 bg-light rounded-4">
                                <div class="text-center mb-4">
                                    <i class="bi bi-credit-card-2-back-fill text-primary" style="font-size:4rem;"></i>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Card Number</label>
                                    <input type="text" class="form-control form-control-lg text-center credit-card-input"
                                           placeholder="4242 4242 4242 4242">
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label fw-bold">Expiry</label>
                                        <input type="text" class="form-control form-control-lg text-center" placeholder="MM/YY">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold">CVV</label>
                                        <input type="text" class="form-control form-control-lg text-center" placeholder="123">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="netbank">
                            <div class="text-center py-5">
                                <i class="bi bi-bank text-primary" style="font-size:4rem;"></i>
                                <select class="form-select form-select-lg mt-4">
                                    <option>SBI</option>
                                    <option>HDFC</option>
                                    <option>ICICI</option>
                                </select>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="cod">
                            <div class="text-center py-5">
                                <i class="bi bi-cash-stack text-success" style="font-size:5rem;"></i>
                                <h4 class="fw-bold mt-3">Cash on Delivery</h4>
                                <p class="text-muted">Pay on delivery</p>
                            </div>
                        </div>
                    </div>

                    <!-- DELIVERY DETAILS -->
                    <div class="bg-light rounded-4 p-4 mt-5">
                        <h5 class="fw-bold mb-4">Delivery Details</h5>
                        <input type="text" class="form-control form-control-lg mb-3" value="{{ auth()->user()->name }}">
                        <input type="text" class="form-control form-control-lg mb-3" value="{{ auth()->user()->phone ?? '+91 9876543210' }}">
                        <input type="email" class="form-control form-control-lg mb-3" value="{{ auth()->user()->email }}">
                        <textarea class="form-control" rows="3">123 Main Street, Mumbai</textarea>
                    </div>

                    <!-- PAY NOW BUTTON -->
                    <form action="{{ route('checkout.success') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-pay text-white shadow-lg w-100">
                            Pay ₹{{ number_format($total) }}
                        </button>
                    </form>

                    <!-- ⭐ NEW BEAUTIFUL CANCEL BUTTON (only change made) ⭐ -->
                    <a href="{{ route('checkout.cancel') }}"
                       class="mt-3 w-100 text-center d-block"
                       style="
                            padding: 14px 0;
                            font-weight: 700;
                            color: #6d28d9;
                            border-radius: 50px;
                            border: 2px solid transparent;
                            background:
                                linear-gradient(#fff, #fff) padding-box,
                                linear-gradient(135deg, #6d28d9, #4c1d95) border-box;
                            transition: .3s;
                       "
                       onmouseover="this.style.color='white'; this.style.background='linear-gradient(135deg, #6d28d9, #4c1d95)';"
                       onmouseout="this.style.color='#6d28d9'; this.style.background='linear-gradient(#fff, #fff) padding-box, linear-gradient(135deg, #6d28d9, #4c1d95) border-box';">
                        ✖ Cancel
                    </a>

                    <div class="text-center mt-4">
                        <small class="text-muted">Secured by 256-bit SSL • Demo Mode</small>
                    </div>

                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function formatCardNumber(input) {
    let v = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    let parts = [];
    for (let i = 0; i < v.length; i += 4) {
        parts.push(v.substring(i, i + 4));
    }
    input.value = parts.length ? parts.join(' ') : v;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
