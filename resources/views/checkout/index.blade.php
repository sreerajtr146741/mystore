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
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
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
        .credit-card-input { font-family: 'Courier New', monospace; letter-spacing: 6px; font-size: 1.6rem; text-align: center; }
        .edit-btn { border: none; background: transparent; color: var(--primary); font-weight: 700; }
        .form-disabled :is(input,select,textarea){ background:#f1f5f9 !important; cursor:not-allowed; }
    </style>
</head>
<body>

@php
    // Prefer controller-provided $items; else fallback to session cart
    // Session cart shape: [productId => ['name','price','qty','image','category','description']]
    $items = $items ?? array_values(session('cart', []));

    // Normalize items
    $normItems = collect($items)->map(function($it){
        $qty   = (int)($it['qty'] ?? 1);
        $price = (float)($it['price'] ?? 0);
        return array_merge($it, [
            'qty'        => $qty,
            'price'      => $price,
            'line_total' => $qty * $price,
        ]);
    });

    $subtotal = (float) $normItems->sum('line_total');
    $shipping = $subtotal < 300 && $subtotal > 0 ? 59.0 : 0.0;
    $total = $subtotal + $shipping;

    // Pre-fill delivery details from authenticated user if present
    $u = auth()->user();
    $prefillName    = old('full_name', $u->name ?? '');
    $prefillPhone   = old('phone',     $u->phone ?? '');
    $prefillEmail   = old('email',     $u->email ?? '');
    $prefillAddress = old('address',   $u->address ?? '');
@endphp

<div class="container checkout-wrapper">
    <div class="row g-5">

        <!-- LEFT: Order Summary -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-gradient text-white py-4" style="background: linear-gradient(135deg, #6d28d9,#4c1d95)!important;">
                    <h3 class="mb-0 fw-bold">Order Summary</h3>
                </div>

                <div class="card-body p-5">

                    @forelse($normItems as $item)
                        <div class="d-flex gap-4 py-4 border-bottom">
                            @if(!empty($item['image']))
                                <img src="{{ asset('storage/'.$item['image']) }}" width="90" class="rounded-3 shadow" alt="Product">
                            @else
                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width:90px;height:90px;">
                                    <i class="bi bi-box fs-2 text-muted"></i>
                                </div>
                            @endif

                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-1">{{ $item['name'] ?? 'Item' }}</h5>
                                <div class="text-muted small mb-1">{{ $item['category'] ?? '' }}</div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge bg-light text-dark">Qty: {{ $item['qty'] }}</span>
                                    <span class="fw-semibold text-secondary">₹{{ number_format($item['price']) }} each</span>
                                </div>
                            </div>

                            <div class="text-end">
                                <div class="fw-bold">₹{{ number_format($item['line_total']) }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            Your cart is empty.
                        </div>
                    @endforelse

                    <div class="bg-light rounded-4 p-4 mt-4">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fs-5">Subtotal</span>
                            <strong class="fs-5">₹{{ number_format($subtotal) }}</strong>
                        </div>

                        <div class="d-flex justify-content-between mb-3 {{ $shipping == 0 ? 'text-success' : 'text-warning' }}">
                            <span class="fs-5">Delivery Charge</span>
                            <strong class="fs-5">
                                {{ $shipping == 0 ? 'FREE' : '₹'.number_format($shipping) }}
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

                <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                    <h3 class="fw-bold mb-0">Secure Payment</h3>
                    <button type="button" id="toggleEditBtn" class="edit-btn">
                        <i class="bi bi-pencil-square me-1"></i> Edit details
                    </button>
                </div>

                <div class="card-body p-5">

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('checkout.process') }}" method="POST">
                        @csrf

                        <!-- PAYMENT TABS (demo only) -->
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
                                <input type="text" class="form-control form-control-lg text-center mt-4" placeholder="yourname@upi">
                            </div>
                            <div class="tab-pane fade" id="card">
                                <div class="p-4 bg-light rounded-4">
                                    <label class="form-label fw-bold">Card Number</label>
                                    <input type="text" class="form-control form-control-lg text-center credit-card-input" placeholder="4242 4242 4242 4242">
                                </div>
                            </div>
                            <div class="tab-pane fade" id="netbank">
                                <select class="form-select form-select-lg mt-4">
                                    <option value="">Select Bank</option>
                                    <option>SBI</option>
                                    <option>HDFC</option>
                                </select>
                            </div>
                            <div class="tab-pane fade" id="cod">
                                <p class="text-center mt-3">Cash on Delivery</p>
                            </div>
                        </div>

                        <!-- DELIVERY DETAILS -->
                        <div id="deliveryPanel" class="bg-light rounded-4 p-4 mt-5 form-disabled">
                            <h5 class="fw-bold mb-4">Delivery Details</h5>

                            <input type="text" id="full_name" name="full_name"
                                   class="form-control form-control-lg mb-3"
                                   placeholder="Enter your full name"
                                   value="{{ $prefillName }}" required readonly>

                            <input type="text" id="phone" name="phone"
                                   class="form-control form-control-lg mb-3"
                                   placeholder="Enter phone number"
                                   value="{{ $prefillPhone }}" readonly>

                            <input type="email" id="email" name="email"
                                   class="form-control form-control-lg mb-3"
                                   placeholder="Enter email"
                                   value="{{ $prefillEmail }}" required readonly>

                            <textarea id="address" name="address"
                                      class="form-control" rows="3"
                                      placeholder="Enter delivery address" required readonly>{{ $prefillAddress }}</textarea>
                        </div>

                        <!-- Hidden totals (server recomputes anyway) -->
                        <input type="hidden" name="subtotal" value="{{ $subtotal }}">
                        <input type="hidden" name="shipping" value="{{ $shipping }}">
                        <input type="hidden" name="total" value="{{ $total }}">

                        <button type="submit" class="btn btn-pay text-white shadow-lg w-100 mt-4" {{ $total <= 0 ? 'disabled' : '' }}>
                            Pay ₹{{ number_format($total) }}
                        </button>
                    </form>

                    <!-- CANCEL -->
                    <form action="{{ route('checkout.cancel') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit"
                            class="w-100 text-center d-block"
                            style="
                                padding: 14px 0;
                                font-weight: 700;
                                color: #6d28d9;
                                border-radius: 50px;
                                border: 2px solid transparent;
                                background:
                                    linear-gradient(#fff, #fff) padding-box,
                                    linear-gradient(135deg, #6d28d9, #4c1d95) border-box;
                            ">
                            ✖ Cancel
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle Delivery Details editability
    (function(){
        const btn = document.getElementById('toggleEditBtn');
        const panel = document.getElementById('deliveryPanel');
        const inputs = panel.querySelectorAll('input, textarea, select');

        let editable = false;
        function setEditable(state){
            editable = state;
            inputs.forEach(el => {
                if(state){
                    el.removeAttribute('readonly');
                    el.removeAttribute('disabled');
                } else {
                    el.setAttribute('readonly','readonly');
                }
            });
            panel.classList.toggle('form-disabled', !state);
            btn.innerHTML = state
                ? '<i class="bi bi-check2-square me-1"></i> Done'
                : '<i class="bi bi-pencil-square me-1"></i> Edit details';
        }

        btn.addEventListener('click', function(e){
            e.preventDefault();
            setEditable(!editable);
        });

        // start as read-only
        setEditable(false);

        // Auto-enable edit if any required field is empty
        const requiredIds = ['full_name','email','address']; // phone optional
        const anyEmpty = requiredIds.some(id => {
            const el = document.getElementById(id);
            return !el || !el.value.trim();
        });
        if (anyEmpty) setEditable(true);
    })();
</script>

</body>
</html>
