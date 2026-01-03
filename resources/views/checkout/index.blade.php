@extends('layouts.master')

@section('title', 'Checkout')

@push('styles')
<style>
    body { background-color: #f1f3f6; } /* Flipkart-like grey background */
    
    .checkout-container { max-width: 1200px; margin: 0 auto; padding-top: 20px; }
    
    /* Section Styles */
    .step-section { background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.1); border-radius: 2px; margin-bottom: 12px; border: none; }
    .step-header { padding: 12px 24px; display: flex; align-items: center; cursor: pointer; color: #878787; background: #fff; font-size: 16px; font-weight: 500; border-bottom: 1px solid #f0f0f0; }
    .step-header.active { background: #2874f0; color: #fff; border-radius: 2px 2px 0 0; border-bottom: none; padding: 16px 24px; }
    .step-header.completed { background: #fff; color: #878787; border-bottom: 1px solid #f0f0f0; }
    
    .step-number { 
        background: #f0f0f0; color: #2874f0; font-size: 12px; font-weight: 500; 
        width: 20px; height: 20px; display: flex; justify-content: center; align-items: center; 
        border-radius: 2px; margin-right: 16px;
    }
    .active .step-number { background: #fff; color: #2874f0; border-radius: 2px; }
    .title { font-weight: 500; font-size: 16px; text-transform: uppercase; }
    .active .title { font-weight: 600; letter-spacing: 0.2px; }
    
    .step-body { padding: 24px; display: none; }
    .step-body.show { display: block; }

    /* Change Button */
    .btn-change { 
        border: 1px solid #e0e0e0; color: #2874f0; font-weight: 500; font-size: 14px; 
        padding: 8px 24px; background: #fff; text-decoration: none; border-radius: 2px;
        margin-left: auto; display: none; text-transform: uppercase;
    }
    .completed .btn-change { display: block; }
    
    /* Order Summary specific */
    .item-row { padding: 24px 0; border-bottom: 1px solid #f0f0f0; }
    .item-image { width: 112px; height: 112px; object-fit: contain; }
    
    .qty-control { margin-top: 15px; display: flex; align-items: center; gap: 8px; }
    .qty-btn { 
        width: 28px; height: 28px; border: 1px solid #e0e0e0; background: #fff; 
        border-radius: 50%; display: flex; align-items: center; justify-content: center; 
        color: #212121; font-weight: bold; cursor: default; font-size: 18px;
    }
    .qty-input { 
        width: 46px; height: 28px; border: 1px solid #e0e0e0; text-align: center; 
        font-size: 14px; font-weight: 500; border-radius: 2px;
    }

    /* Info Box */
    .info-box { 
        background: #fff; border: 1px solid #f0f0f0; padding: 12px; border-radius: 2px; 
        display: flex; align-items: flex-start; gap: 12px; max-width: 320px;
    }
    .info-icon { font-size: 24px; color: #ff9f00; }
    .info-text { font-size: 12px; line-height: 1.5; color: #212121; }
    .info-text strong { font-weight: 600; }
    .know-more { color: #2874f0; text-decoration: none; font-weight: 500; }

    /* Summary Footer */
    .summary-footer { 
        display: flex; align-items: center; justify-content: space-between; 
        padding: 16px 24px; border-top: 1px solid #f0f0f0; background: #fff;
    }
    .email-line { font-size: 14px; color: #212121; display: flex; align-items: center; gap: 8px; }
    .email-line input { border: none; border-bottom: 2px solid #2874f0; font-weight: 500; color: #878787; background: transparent; padding: 2px 4px; outline: none; }

    /* Price Styles */
    .price-final { font-size: 18px; font-weight: 600; color: #212121; margin-right: 8px; }
    .price-old { font-size: 14px; color: #878787; text-decoration: line-through; margin-right: 8px; }
    .price-off { font-size: 14px; color: #388e3c; font-weight: 600; }
    .coupon-applied { font-size: 12px; color: #388e3c; font-weight: 600; display: flex; align-items: center; gap: 4px; }

    /* Price Details Sidebar */
    .price-card { background: #fff; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,.1); overflow: hidden; margin-bottom: 20px; }
    .price-header { padding: 13px 24px; border-bottom: 1px solid #f0f0f0; color: #878787; font-weight: 500; font-size: 16px; text-transform: uppercase; }
    .price-body { padding: 0 24px; }
    .price-row { display: flex; justify-content: space-between; align-items: center; margin: 20px 0; font-size: 16px; color: #212121; }
    .total-row { border-top: 1px dashed #e0e0e0; border-bottom: 1px dashed #e0e0e0; padding: 20px 0; font-weight: 700; font-size: 18px; color: #212121; }
    .savings { color: #388e3c; font-weight: 600; font-size: 16px; padding: 15px 24px; }

    /* Trust Section */
    .trust-section { margin-top: 24px; padding: 0 10px; }
    .trust-badge { display: flex; gap: 12px; align-items: center; color: #878787; font-size: 14px; font-weight: 500; margin-bottom: 15px; }
    .trust-icon { font-size: 28px; color: #878787; opacity: 0.7; }
    .disclaimer { font-size: 12px; color: #878787; line-height: 1.6; }
    .disclaimer a { color: #2874f0; text-decoration: none; font-weight: 600; }

    /* Continue Button */
    .btn-continue { 
        background: #fb641b; color: #fff !important; border: none; font-weight: 600; font-size: 16px; 
        padding: 14px 40px; border-radius: 2px; text-transform: uppercase; 
        box-shadow: 0 1px 2px 0 rgba(0,0,0,.2); cursor: pointer;
    }
    .btn-continue:hover { background: #f45b12; }

    /* Inputs */
    .form-floating input, .form-floating textarea { border-radius: 2px; border: 1px solid #e0e0e0; }
    .form-floating input:focus, .form-floating textarea:focus { border-color: #2874f0; box-shadow: none; }
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    
    // Use items passed from controller (already normalized)
    // Convert to object for Blade syntax compatibility ($item->name)
    $items = collect($items)->map(function($item, $key) {
         $item['id'] = $key; // Ensure ID matches array index for removal
         return (object)$item;
    });
    
    // Calculations are already done in controller but we need variables for display if not passed?
    // Controller passes: subtotal, shipping, discount, total.
    // So we don't need to recalculate them here.
    
    // We already have $subtotal, $shipping, $discount, $total from controller.
    // But we need to ensure they are available as variables if View::share wasn't used.
    // The controller passes them in generic array.
    
    $prefillName = old('full_name', $user->first_name . ' ' . $user->last_name);
    $prefillPhone = old('phone', $user->phone);
    $prefillEmail = old('email', $user->email);
    $prefillAddress = old('address', $user->address);
@endphp

<div class="container-fluid checkout-container">
    <div class="row">
        <!-- LEFT COLUMN: STEPS -->
        <div class="col-lg-8">
            
            <form action="{{ route('checkout.proceed') }}" method="POST" id="checkoutForm">
                @csrf
                <!-- Hidden Totals -->
                <input type="hidden" name="subtotal" value="{{ $subtotal }}">
                <input type="hidden" name="shipping" value="{{ $shipping }}">
                <input type="hidden" name="discount" value="{{ $discount }}">
                <input type="hidden" name="total" value="{{ $total }}">
                <!-- STEP 1: DELIVERY ADDRESS (Completed style showing details) -->
                <div class="step-section completed" id="step1">
                    <div class="step-header d-flex align-items-center" id="header1">
                        <div class="step-number">1</div>
                        <div>
                            <span class="title d-block">DELIVERY ADDRESS</span>
                            <span class="text-dark small fw-bold" id="preview-name">{{ $user->first_name }} {{ $user->last_name }}</span>
                            <span class="text-muted small ms-2" id="preview-address">({{ substr($user->address, 0, 40) }}...)</span>
                        </div>
                        <button type="button" class="btn-change ms-auto" onclick="goToStep(1)"><i class="bi bi-pencil-square fs-5"></i></button>
                    </div>
                    <div class="step-body" id="body1">
                        <!-- Address Form (Initially hidden since we start at Summary?) -->
                         <!-- Actually, usually user confirms address first. 
                              But images show Summary as active. So I'll make Address 'completed'. -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="full_name" class="form-control" id="fName" placeholder="Full Name" value="{{ $prefillName }}" required>
                                    <label for="fName">Full Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" name="phone" class="form-control" id="fPhone" placeholder="10-digit mobile number" value="{{ old('phone', $user->phoneno) }}" required>
                                    <label for="fPhone">10-digit mobile number</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="email" name="email" class="form-control" id="fEmail" placeholder="Email (for order updates)" value="{{ $prefillEmail }}" required>
                                    <label for="fEmail">Email (for order updates)</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea name="address" class="form-control" id="fAddress" style="height: 100px" placeholder="Full Address (House No, Building, Street, Area)" required>{{ $prefillAddress }}</textarea>
                                    <label for="fAddress">Full Address (House No, Building, Street, Area)</label>
                                </div>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="button" class="btn-continue" onclick="goToStep(2)">DELIVER HERE</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: ORDER SUMMARY -->
                <div class="step-section active" id="step2">
                    <div class="step-header active" id="header2">
                        <div class="step-number">1</div> <!-- Matching reference numbering if preferred, but I'll use 2 -->
                        <span class="title">ORDER SUMMARY</span>
                    </div>
                    <script>document.getElementById('header2').querySelector('.step-number').innerText = '2';</script>
                    
                    <div class="step-body show" id="body2" style="padding: 0;">
                        @forelse($items as $item)
                        <div class="item-row d-flex px-4">
                            <!-- Image and Quantity Section -->
                            <div class="flex-shrink-0 text-center" style="width: 112px;">
                                @if(\Illuminate\Support\Str::startsWith($item->image, 'http'))
                                    <img src="{{ $item->image }}" class="item-image mb-2" alt="Product">
                                @else
                                    <img src="{{ asset('storage/'.$item->image) }}" class="item-image mb-2" alt="Product">
                                @endif
                                
                                <div class="qty-control justify-content-center">
                                    <button type="button" class="qty-btn" onclick="updateQty('{{ $item->id }}', -1)">−</button>
                                    <input type="text" class="qty-input" id="qty-{{ $item->id }}" value="{{ $item->qty }}" readonly>
                                    <button type="button" class="qty-btn" onclick="updateQty('{{ $item->id }}', 1)">+</button>
                                </div>
                            </div>

                            <!-- Details Section -->
                            <div class="item-details flex-grow-1 ps-4">
                                <h5 class="mb-1 text-dark" style="font-size: 16px; font-weight: 400; line-height: 1.2;">{{ $item->name }}</h5>
                                <div class="text-muted mb-2" style="font-size: 12px;">Seller: MyStore Official</div>
                                
                                <div class="d-flex align-items-center mb-1">
                                    <span class="price-old">₹{{ number_format($item->price * 1.5) }}</span>
                                    <span class="price-final">₹{{ number_format($item->price) }}</span>
                                    <span class="price-off">33% Off</span>
                                </div>
                                <div class="mb-3" style="font-size: 13px; color: #212121;">
                                    Or Pay ₹{{ number_format($item->price * 0.9, 0) }} + <img src="https://static-assets-web.flixcart.com/fk-p-linchpin-web/fk-cp-zion/img/supercoin_fbd6a4.png" width="16" alt="coin"> {{ rand(100, 500) }}
                                </div>

                                <div class="mt-auto pt-2">
                                    <!-- Remove button removed as requested -->
                                </div>
                            </div>

                            <!-- Delivery Section -->
                            <div class="text-end" style="min-width: 250px;">
                                <div style="font-size: 14px; color: #212121;">
                                    Delivery by {{ now()->addDays(5)->format('D M d') }} | <span class="text-success">FREE</span>
                                </div>
                            </div>
                        </div>
                        @empty
                            <div class="p-4 text-center text-muted">
                                <p>No items in checkout.</p>
                                <a href="{{ route('products.index') }}" class="btn btn-sm btn-primary">Continue Shopping</a>
                            </div>
                        @endforelse

                        <!-- Summary Footer -->
                        <div class="summary-footer d-flex align-items-center justify-content-between px-4 py-3" id="summaryFooter" style="box-shadow: 0 -2px 10px rgba(0,0,0,0.05); margin-top: 20px;">
                            <div class="email-line" style="font-size: 14px;">
                                <span class="text-muted">Order confirmation email will be sent to</span>
                                <input type="text" value="{{ $user->email }}" readonly style="width: auto; margin-left:8px; border:none; border-bottom: 2px solid #2874f0; font-weight: 600; color: #212121; outline: none; padding: 0 4px;">
                            </div>
                            <button type="submit" class="btn-continue" style="min-width: 200px; padding: 14px 30px;">CONTINUE</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <!-- RIGHT COLUMN: PRICE DETAILS -->
        <div class="col-lg-4">
            <div class="price-header px-4 py-2 bg-white border border-bottom-0">PRICE DETAILS</div>
            <div class="price-card p-4">
                <div class="price-row">
                    <span>Price ({{ count($items) }} item{{ count($items) > 1 ? 's' : '' }})</span>
                    <span id="ui-subtotal">₹{{ number_format($subtotal) }}</span>
                </div>
                <div class="price-row">
                    <span>Delivery Charges</span>
                    @if($shipping > 0)
                        <span class="text-dark">₹{{ number_format($shipping) }}</span>
                    @else
                        <span class="text-success">FREE</span>
                    @endif
                </div>
                <div class="price-row">
                    <span>Platform Fee</span>
                    <span class="text-dark" id="ui-platform-fee">₹{{ number_format($platform_fee) }}</span>
                </div>
                
                <div class="total-row d-flex justify-content-between">
                    <span>Total Payable</span>
                    <span id="ui-total">₹{{ number_format($total) }}</span>
                </div>
                
                <div class="savings mt-3">
                        Your Total Savings on this order <span id="ui-savings">₹{{ number_format($discount + ($subtotal * 0.5)) }}</span>
                </div>
            </div>

            <div class="trust-section">
                <div class="trust-badge">
                    <i class="bi bi-shield-fill-check trust-icon"></i>
                    <span>Safe and Secure Payments. Easy returns.<br>100% Authentic products.</span>
                </div>
                <div class="disclaimer">
                    By continuing with the order, you confirm that you are above 18 years of age, and you agree to the MyStore's <a href="#">Terms of Use</a> and <a href="#">Privacy Policy</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Item remove forms -->
@foreach($items as $item)
    <form id="remove-{{ $item->id }}" action="{{ route('checkout.remove', $item->id) }}" method="POST" style="display:none;">
        @csrf @method('DELETE')
    </form>
@endforeach

@push('scripts')
<script>
    function goToStep(step) {
        // Validation for Address (Step 1 in new renumbered flow)
        if (step === 2) {
            const req = ['fName', 'fPhone', 'fEmail', 'fAddress'];
            let valid = true;
            req.forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                if (!el.value.trim()) {
                    el.classList.add('is-invalid');
                    valid = false;
                } else {
                    el.classList.remove('is-invalid');
                }
            });
            if (!valid) return;

            // Sync Preview Details
            const name = document.getElementById('fName').value;
            const address = document.getElementById('fAddress').value;
            document.getElementById('preview-name').innerText = name;
            document.getElementById('preview-address').innerText = '(' + address.substring(0, 40) + '...)';
        }

        // Switch active states
        const prev = step - 1;
        if (document.getElementById('body' + prev)) {
            document.getElementById('body' + prev).classList.remove('show');
            document.getElementById('header' + prev).classList.remove('active');
            document.getElementById('header' + prev).classList.add('completed');
        }
        
        if (document.getElementById('body' + step)) {
            document.getElementById('body' + step).classList.add('show');
            document.getElementById('header' + step).classList.remove('completed');
            document.getElementById('header' + step).classList.add('active');
            document.getElementById('step' + step).scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    // Header Click handlers
    ['header1', 'header2'].forEach((id, idx) => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('click', function() {
                if (this.classList.contains('completed')) {
                     const step = idx + 1;
                     document.querySelectorAll('.step-body').forEach(b => b.classList.remove('show'));
                     document.querySelectorAll('.step-header').forEach(h => h.classList.remove('active'));
                     
                     document.getElementById('body' + step).classList.add('show');
                     document.getElementById('header' + step).classList.add('active');
                }
            });
        }
    });

    function updateQty(id, change) {
        const input = document.getElementById('qty-' + id);
        let newQty = parseInt(input.value) + change;
        if (newQty < 1) return;

        // AJAX update
        // We use a dummy ID '0' and replace it with the actual ID to prevent Route errors if id is missing in definition
        const route = "{{ route('checkout.update_qty', 0) }}".replace('/0', '/' + id);

        fetch(route, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ qty: newQty })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                input.value = newQty;
                updateTotalsUI(data.totals);
            }
        })
        .catch(err => console.error('Qty update error:', err));
    }

    function updateTotalsUI(t) {
        // Find UI elements
        const sub = document.getElementById('ui-subtotal');
        const ship = document.getElementById('ui-shipping');
        const pFee = document.getElementById('ui-platform-fee');
        const tot = document.getElementById('ui-total');
        const sav = document.getElementById('ui-savings');

        if(sub) sub.innerText = '₹' + t.subtotal.toLocaleString();
        if(ship) ship.innerText = t.shipping > 0 ? '₹' + t.shipping.toLocaleString() : 'FREE';
        if(pFee) pFee.innerText = '₹' + t.platform_fee.toLocaleString();
        if(tot) tot.innerText = '₹' + t.total.toLocaleString();
        if(sav) sav.innerText = '₹' + (t.discount + (t.subtotal * 0.5)).toLocaleString();
        
        // Update price hidden input? No need if server-side handles it on submit.
    }
</script>
@endpush