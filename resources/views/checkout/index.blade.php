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

        <!-- RIGHT PANEL -->
        <div class="col-lg-6">
            <div class="card">

                <div class="card-header bg-white border-0 py-4 text-center">
                    <h3 class="fw-bold mb-0">Secure Payment</h3>
                </div>

                <div class="card-body p-5">

                    <!-- ❗ SHOW VALIDATION ERRORS -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- PAYMENT + DELIVERY FORM -->
                    <form action="{{ route('checkout.success') }}" method="POST">
                        @csrf

                        <!-- PAYMENT TABS -->
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
                                <input type="text" class="form-control form-control-lg text-center mt-4"
                                       placeholder="yourname@upi">
                            </div>

                            <div class="tab-pane fade" id="card">
                                <div class="p-4 bg-light rounded-4">
                                    <label class="form-label fw-bold">Card Number</label>
                                    <input type="text" class="form-control form-control-lg text-center credit-card-input"
                                           placeholder="4242 4242 4242 4242">
                                </div>
                            </div>

                            <div class="tab-pane fade" id="netbank">
                                <select class="form-select form-select-lg mt-4">
                                    <option>SBI</option>
                                    <option>HDFC</option>
                                </select>
                            </div>

                            <div class="tab-pane fade" id="cod">
                                <p class="text-center mt-3">Cash on Delivery</p>
                            </div>
                        </div>

                        <!-- DELIVERY DETAILS -->
                        <div class="bg-light rounded-4 p-4 mt-5">
                            <h5 class="fw-bold mb-4">Delivery Details</h5>

                            <input type="text"
                                   name="full_name"
                                   class="form-control form-control-lg mb-3"
                                   placeholder="Enter your full name"
                                   required>

                            <input type="text"
                                   name="phone"
                                   class="form-control form-control-lg mb-3"
                                   placeholder="Enter phone number"
                                   required>

                            <input type="email"
                                   name="email"
                                   class="form-control form-control-lg mb-3"
                                   placeholder="Enter email"
                                   required>

                            <textarea name="address" class="form-control"
                                      rows="3"
                                      placeholder="Enter delivery address"
                                      required></textarea>
                        </div>

                        <!-- PAY NOW -->
                        <button type="submit" class="btn btn-pay text-white shadow-lg w-100 mt-4">
                            Pay ₹{{ number_format($total) }}
                        </button>

                    </form>

                    <!-- CANCEL BUTTON -->
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
                       ">
                        ✖ Cancel
                    </a>

                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
