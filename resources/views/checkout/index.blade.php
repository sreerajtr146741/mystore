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
            border: none; border-radius: 50px;
            box-shadow: 0 10px 20px rgba(16,185,129,0.3);
            transition: transform 0.2s;
        }
        .btn-pay:hover { transform: translateY(-2px); }
        .payment-tab-icon { font-size: 1.8rem; margin-right: 8px; }
        .credit-card-input { font-family: 'Courier New', monospace; letter-spacing: 6px; font-size: 1.6rem; text-align: center; }
        .edit-btn { border: none; background: transparent; color: var(--primary); font-weight: 700; }
        .form-disabled :is(input,select,textarea){ background:#f1f5f9 !important; cursor:not-allowed; }
    </style>
</head>
<body>
@php
    $items    = $items ?? array_values(session('cart', []));
    $items    = collect($items)->map(function ($it) {
        $qty   = (int)($it['qty'] ?? 1);
        $price = (float)($it['price'] ?? 0);
        return array_merge($it, ['qty'=>$qty,'price'=>$price,'line_total'=>$qty*$price]);
    })->values();

    $subtotal = isset($subtotal) ? (float)$subtotal : (float)$items->sum('line_total');
    $shipping = isset($shipping) ? (float)$shipping : ($subtotal > 0 && $subtotal < 300 ? 59.0 : 0.0);
    $discount = isset($discount) ? (float)$discount : 0.0;
    $total    = isset($total)    ? (float)$total    : max(0.0, ($subtotal - $discount) + $shipping);
    $coupon   = $coupon ?? session('coupon_code');

    $u = auth()->user();
    $prefillName    = old('full_name', $u->first_name.' '.$u->last_name ?? '');
    $prefillPhone   = old('phone',     $u->phone ?? '');
    $prefillEmail   = old('email',     $u->email ?? '');
    $prefillAddress = old('address',   $u->address ?? '');
@endphp

<div class="container checkout-wrapper">
    <div class="row g-5">
        <!-- LEFT: Order Summary -->
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header bg-gradient text-white py-4" style="background: linear-gradient(135deg, #6d28d9,#4c1d95)!important;">
                    <h3 class="mb-0 fw-bold">Order Summary</h3>
                </div>
                <div class="card-body p-5">
                    @forelse($items as $item)
                        <div class="d-flex gap-4 py-4 border-bottom">
                            @if(!empty($item['image']))
                                <img src="{{ asset('storage/'.$item['image']) }}" width="120" class="rounded-3 shadow" alt="Product">
                            @else
                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width:120px;height:120px;">
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
                                <div class="fw-bold fs-5">₹{{ number_format($item['line_total']) }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">Your cart is empty.</div>
                    @endforelse

                    @if($coupon)
                        <div class="alert alert-success py-2 mt-4">
                            Applied coupon: <strong>{{ $coupon }}</strong>
                            <form action="{{ route('checkout.coupon.remove') }}" method="POST" class="d-inline ms-2">
                                @csrf @method('DELETE')
                                <button class="btn btn-link p-0 align-baseline">Remove</button>
                            </form>
                        </div>
                    @endif

                    <div class="bg-light rounded-4 p-4 mt-3">
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Subtotal</span>
                                <strong>₹{{ number_format($subtotal, 2) }}</strong>
                            </li>
                            @if($discount > 0)
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Discount</span>
                                    <strong>-₹{{ number_format($discount, 2) }}</strong>
                                </li>
                            @endif
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Shipping</span>
                                <strong>{{ $shipping == 0 ? 'FREE' : '₹'.number_format($shipping, 2) }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between fs-4">
                                <span>Total</span>
                                <strong class="text-success">₹{{ number_format($total, 2) }}</strong>
                            </li>
                        </ul>

                        <form action="{{ route('checkout.coupon.apply') }}" method="POST" class="mb-1">
                            @csrf
                            <div class="input-group">
                                <input name="coupon_code" class="form-control" placeholder="Have a coupon?" value="">
                                <button class="btn btn-outline-secondary" type="submit">Apply</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Payment + Delivery -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                    <h3 class="fw-bold mb-0">Secure Payment</h3>
                    <button type="button" id="toggleEditBtn" class="edit-btn">
                        Edit details
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

                    <!-- FINAL PAYMENT: Sends OTP (your requirement) -->
                    <form action="{{ route('pay.now') }}" method="POST">
                        @csrf

                        <!-- Hidden totals for server -->
                        <input type="hidden" name="subtotal" value="{{ $subtotal }}">
                        <input type="hidden" name="shipping" value="{{ $shipping }}">
                        <input type="hidden" name="discount" value="{{ $discount }}">
                        <input type="hidden" name="total" value="{{ $total }}">

                        <!-- Delivery Details (editable) -->
                        <div id="deliveryPanel" class="bg-light rounded-4 p-4 mt-4 form-disabled">
                            <h5 class="fw-bold mb-4">Delivery Address</h5>
                            <input type="text" name="full_name" class="form-control form-control-lg mb-3"
                                   placeholder="Full Name" value="{{ $prefillName }}" required readonly>
                            <input type="text" name="phone" class="form-control form-control-lg mb-3"
                                   placeholder="Phone" value="{{ $prefillPhone }}" readonly>
                            <input type="email" name="email" class="form-control form-control-lg mb-3"
                                   placeholder="Email" value="{{ $prefillEmail }}" required readonly>
                            <textarea name="address" class="form-control" rows="3"
                                      placeholder="Full Address" required readonly>{{ $prefillAddress }}</textarea>
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="mt-4">
                            <h5 class="fw-bold mb-3">Payment Method</h5>
                            <div class="d-flex flex-column gap-2">
                                <!-- Card Option -->
                                <div class="form-check p-3 rounded-3 border">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay_card" value="card" checked>
                                    <label class="form-check-label w-100 fw-semibold" for="pay_card">
                                        <i class="bi bi-credit-card me-2 text-primary"></i> Credit / Debit Card
                                    </label>
                                    <!-- Dynamic Card Details -->
                                    <div id="card_details" class="mt-3 ps-2 payment-details-group">
                                        <input type="text" name="card_number" class="form-control credit-card-input mb-2" placeholder="0000 0000 0000 0000" maxlength="19">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <input type="text" name="card_expiry" class="form-control text-center fs-5" placeholder="MM/YY" maxlength="5">
                                            </div>
                                            <div class="col-6">
                                                <input type="password" name="card_cvc" class="form-control text-center fs-5" placeholder="CVC" maxlength="3">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- UPI Option -->
                                <div class="form-check p-3 rounded-3 border">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay_upi" value="upi">
                                    <label class="form-check-label w-100 fw-semibold" for="pay_upi">
                                        <i class="bi bi-phone me-2 text-primary"></i> UPI / Netbanking
                                    </label>
                                    <!-- Dynamic UPI Details -->
                                    <div id="upi_details" class="mt-3 ps-2 payment-details-group" style="display:none;">
                                        <input type="text" name="upi_id" class="form-control fs-5" placeholder="username@upi">
                                    </div>
                                </div>

                                <!-- COD Option -->
                                <div class="form-check p-3 rounded-3 border">
                                    <input class="form-check-input" type="radio" name="payment_method" id="pay_cod" value="cod">
                                    <label class="form-check-label w-100 fw-semibold" for="pay_cod">
                                        <i class="bi bi-cash-stack me-2 text-success"></i> COD (Cash on Delivery)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- PAY NOW BUTTON (OTP will be sent) -->
                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-pay text-white shadow-lg rounded-pill px-5 py-3" {{ $total <= 0 ? 'disabled' : '' }}>
                                Proceed to Pay<br>
                                <small class="opacity-90 fs-6">Total: ₹{{ number_format($total, 2) }}</small>
                            </button>
                        </div>
                    </form>

                    <!-- Cancel Button -->
                    <div class="text-center mt-4">
                        <form action="{{ route('checkout.cancel') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger rounded-pill px-5 py-3">
                                Cancel Order
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Payment Method Toggle
    (function() {
        const radios = document.querySelectorAll('input[name="payment_method"]');
        const cardDetails = document.getElementById('card_details');
        const upiDetails = document.getElementById('upi_details');

        function togglePaymentFields() {
            // Hide all first
            cardDetails.style.display = 'none';
            upiDetails.style.display = 'none';

            const selected = document.querySelector('input[name="payment_method"]:checked').value;
            if(selected === 'card') {
                cardDetails.style.display = 'block';
            } else if(selected === 'upi') {
                upiDetails.style.display = 'block';
            }
        }

        radios.forEach(radio => {
            radio.addEventListener('change', togglePaymentFields);
        });

        // Initialize state
        togglePaymentFields();

        // VALIDATION LOGIC
        const form = document.querySelector('form[action*="pay-now"]'); // Safer selector
        if(form) {
            form.addEventListener('submit', function(e) {
                const checked = document.querySelector('input[name="payment_method"]:checked');
                if(!checked) return; // Should select one
                
                const method = checked.value;
                let isValid = true;
                let msg = '';

                if (method === 'card') {
                    const num = document.querySelector('input[name="card_number"]').value.trim();
                    const exp = document.querySelector('input[name="card_expiry"]').value.trim();
                    const cvc = document.querySelector('input[name="card_cvc"]').value.trim();

                    if(!num || !exp || !cvc) {
                        isValid = false;
                        msg = 'Please fill in all Card details (Number, Expiry, CVC).';
                    }
                } else if (method === 'upi') {
                    const upi = document.querySelector('input[name="upi_id"]').value.trim();
                    if(!upi) {
                        isValid = false;
                        msg = 'Please enter your UPI ID.';
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    alert(msg);
                }
            });
        }
    })();

    // Toggle edit mode for delivery details
    (function(){
        const btn = document.getElementById('toggleEditBtn');
        const panel = document.getElementById('deliveryPanel');
        const inputs = panel.querySelectorAll('input, textarea');
        let editable = false;

        function setEditable(state){
            editable = state;
            inputs.forEach(el => {
                if(state) {
                    el.removeAttribute('readonly');
                } else {
                    el.setAttribute('readonly', 'readonly');
                }
            });
            panel.classList.toggle('form-disabled', !state);
            btn.innerHTML = state ? 'Done' : 'Edit details';
        }

        btn.addEventListener('click', function(e){
            e.preventDefault();
            setEditable(!editable);
        });

        // Auto-enable edit if required fields empty
        const required = ['full_name', 'email', 'address'];
        const empty = required.some(id => {
            const el = document.querySelector(`[name="${id}"]`);
            return !el || !el.value.trim();
        });
        if (empty) setEditable(true);
    })();
</script>
</body>
</html>