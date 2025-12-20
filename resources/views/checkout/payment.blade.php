@extends('layouts.master')

@section('title', 'Complete Payment')

@push('styles')
<style>
    body { background-color: #f1f3f6; } /* Flipkart grey */
    .container-payment { max-width: 1200px; margin: 20px auto; display: flex; gap: 20px; }
    
    /* Left Column */
    .payment-options-card { background: #fff; box-shadow: 0 1px 2px 0 rgba(0,0,0,.2); border-radius: 2px; flex: 1; min-height: 500px; }
    .payment-header { padding: 16px 24px; border-bottom: 1px solid #f0f0f0; background: #2874f0; color: #fff; font-weight: 500; font-size: 16px; border-radius: 2px 2px 0 0; display: flex; align-items: center; }
    .payment-header i { font-size: 18px; margin-right: 12px; cursor: pointer; }
    
    .payment-option-row { border-bottom: 1px solid #f0f0f0; display: flex; }
    
    /* Tab Headers (Left Side of Payment Card if vertical tabs, but image shows vertical stack accordion style or radio-like) 
       Actually image shows a list. When one is selected, it expands.
    */
    .option-header { padding: 18px 24px; cursor: pointer; display: flex; align-items: flex-start; gap: 16px; width: 100%; transition: background 0.2s; }
    .option-header:hover { background: #f9f9f9; }
    .option-header.active { background: #f5faff; }
    
    .option-icon { color: #878787; font-size: 20px; width: 24px; text-align: center; margin-top: -2px; }
    .option-title { font-size: 16px; font-weight: 500; color: #212121; }
    .option-subtitle { font-size: 12px; color: #878787; margin-top: 4px; }
    .option-offers { font-size: 12px; color: #26a541; margin-top: 4px; font-weight: 500; }
    
    /* Expanded Content */
    .option-content { padding: 0 24px 20px 64px; display: none; background: #f5faff; }
    .option-content.show { display: block; }
    
    /* Form Elements */
    .upi-input-group { display: flex; align-items: center; gap: 10px; margin-top: 15px; }
    .upi-input { flex: 1; padding: 12px 16px; border: 1px solid #e0e0e0; border-radius: 2px; font-size: 14px; outline: none; }
    .upi-input:focus { border-color: #2874f0; }
    .btn-verify { background: #2874f0; color: #fff; border: none; padding: 12px 20px; font-weight: 600; font-size: 14px; border-radius: 2px; cursor: pointer; }
    
    .btn-pay { background: #fb641b; color: #fff; border: none; width: 100%; padding: 16px; font-size: 16px; font-weight: 600; text-transform: uppercase; margin-top: 20px; box-shadow: 0 1px 2px 0 rgba(0,0,0,.2); cursor: pointer; }
    .btn-pay:hover { background: #f45b12; }
    
    /* Right Column */
    .price-sidebar { width: 340px; }
    .price-card { background: #fff; border-radius: 2px; box-shadow: 0 1px 2px 0 rgba(0,0,0,.2); overflow: hidden; }
    .price-header { padding: 16px 24px; border-bottom: 1px solid #f0f0f0; color: #878787; font-weight: 600; font-size: 14px; text-transform: uppercase; }
    .price-body { padding: 24px; }
    .price-row { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 16px; color: #212121; }
    .total-row { display: flex; justify-content: space-between; border-top: 1px dashed #e0e0e0; padding-top: 20px; font-weight: 600; font-size: 18px; color: #212121; }
    
    /* Offers Box */
    .offer-box { background: #fff; padding: 16px; margin-top: 16px; border-radius: 2px; box-shadow: 0 1px 2px 0 rgba(0,0,0,.1); display: flex; justify-content: space-between; align-items: center; }
    .offer-text { color: #26a541; font-weight: 500; font-size: 14px; }
    .offer-sub { font-size: 12px; color: #878787; margin-top: 2px; }
    
    /* Utility */
    .radio-circle { width: 18px; height: 18px; border: 2px solid #878787; border-radius: 50%; display: inline-block; position: relative; margin-right: 15px; }
    .option-header.active .radio-circle { border-color: #2874f0; }
    .option-header.active .radio-circle::after { content: ''; position: absolute; top: 3px; left: 3px; width: 8px; height: 8px; background: #2874f0; border-radius: 50%; }
    
    .secure-badge { background: #f0f0f0; color: #878787; font-size: 12px; padding: 6px 10px; border-radius: 4px; display: inline-flex; align-items: center; gap: 5px; }
</style>
@endpush

@section('content')
<div class="checkout-container"> <!-- Using same wrapper class if defined in master, but CSS above overrides -->
    
    <!-- Top Header Override -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0 fw-bold">Complete Payment</h5>
        </div>
        <div class="secure-badge">
            <i class="bi bi-shield-fill-check"></i> 100% Secure
        </div>
    </div>

    <div class="row">
        <!-- Left: Payment Options -->
        <div class="col-lg-8">
            <form action="{{ route('checkout.process') }}" method="POST" id="paymentForm">
                @csrf
                <input type="hidden" name="payment_method" id="selectedMethod" value="upi">
                
                <div class="bg-white shadow-sm rounded-1">
                    
                    <!-- UPI -->
                    <div class="option-group">
                        <div class="option-header active" onclick="selectOption('upi')">
                            <div class="radio-circle"></div>
                            <div class="flex-grow-1">
                                <div class="option-title">UPI</div>
                                <div class="option-subtitle">Pay by any UPI app</div>
                                <div class="option-offers">Get upto ₹50 cashback • 4 offers available</div>
                            </div>
                        </div>
                        <div class="option-content show" id="content-upi">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="upi_type" id="upi_new" checked>
                                    <label class="form-check-label fw-bold" for="upi_new">Add new UPI ID</label>
                                </div>
                                <div class="upi-input-group ps-4">
                                    <input type="text" class="upi-input" name="upi_id" placeholder="Enter your UPI ID">
                                    <button type="button" class="btn-verify">Verify</button>
                                </div>
                                <div class="ps-4">
                                    <button type="submit" class="btn-pay py-3">Pay ₹{{ number_format($total) }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card -->
                    <div class="option-group border-top">
                        <div class="option-header" onclick="selectOption('card')">
                            <div class="radio-circle"></div>
                            <div class="flex-grow-1">
                                <div class="option-title">Credit / Debit / ATM Card</div>
                                <div class="option-subtitle">Add and secure cards as per RBI guidelines</div>
                                <div class="option-offers">Save upto ₹2,500 • 4 offers available</div>
                            </div>
                            <div class="option-icon"><i class="bi bi-credit-card"></i></div>
                        </div>
                        <div class="option-content" id="content-card">
                            <div class="row g-3" style="max-width: 400px;">
                                <div class="col-12">
                                    <input type="text" class="form-control rounded-0" name="card_no" placeholder="Card Number">
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control rounded-0" name="card_exp" placeholder="MM/YY">
                                </div>
                                <div class="col-6">
                                    <input type="password" class="form-control rounded-0" name="card_cvv" placeholder="CVV">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn-pay py-3">Pay ₹{{ number_format($total) }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- EMI -->
                    <div class="option-group border-top">
                        <div class="option-header" onclick="selectOption('emi')">
                            <div class="radio-circle"></div>
                            <div class="flex-grow-1">
                                <div class="option-title">EMI</div>
                                <div class="option-subtitle">Get Debit and Cardless EMIs on HDFC Bank</div>
                            </div>
                            <div class="option-icon"><i class="bi bi-calendar-check"></i></div>
                        </div>
                        <div class="option-content" id="content-emi">
                            <p class="text-muted small">EMI options will be shown here.</p>
                            <button type="submit" class="btn-pay py-3" style="width:auto; padding: 12px 30px;">Continue</button>
                        </div>
                    </div>

                    <!-- Net Banking -->
                    <div class="option-group border-top">
                        <div class="option-header" onclick="selectOption('netbanking')">
                            <div class="radio-circle"></div>
                            <div class="flex-grow-1">
                                <div class="option-title">Net Banking</div>
                            </div>
                            <div class="option-icon"><i class="bi bi-bank"></i></div>
                        </div>
                        <div class="option-content" id="content-netbanking">
                             <select class="form-select rounded-0 mb-3" name="bank" style="max-width:300px;">
                                 <option>HDFC Bank</option>
                                 <option>SBI</option>
                                 <option>ICICI Bank</option>
                                 <option>Axis Bank</option>
                             </select>
                             <button type="submit" class="btn-pay py-3">Pay ₹{{ number_format($total) }}</button>
                        </div>
                    </div>

                    <!-- COD -->
                    <div class="option-group border-top">
                        <div class="option-header" onclick="selectOption('cod')">
                            <div class="radio-circle"></div>
                            <div class="flex-grow-1">
                                <div class="option-title">Cash on Delivery</div>
                            </div>
                            <div class="option-icon"><i class="bi bi-cash"></i></div>
                        </div>
                        <div class="option-content" id="content-cod">
                             <div class="alert alert-warning small rounded-0 border-0 mb-3 text-dark">
                                 Due to handling costs, a nominal fee of ₹5 will be charged.
                             </div>
                             <button type="submit" class="btn-pay py-3">Confirm Order</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right: Price Summary -->
        <div class="col-lg-4">
            <div class="bg-white shadow-sm rounded-1 mb-3">
                <div class="p-3 border-bottom text-muted fw-bold small text-uppercase">
                    Price Details
                </div>
                <div class="p-3">
                    <div class="d-flex justify-content-between mb-3">
                        <div>Price ({{ count($items) }} item)</div>
                        <div>₹{{ number_format($subtotal) }}</div>
                    </div>
                    @if($discount > 0)
                    <div class="d-flex justify-content-between mb-3 text-success">
                        <div>Discount</div>
                        <div>− ₹{{ number_format($discount) }}</div>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-3">
                        <div>Platform Fee</div>
                        <div>₹{{ number_format($platform_fee) }}</div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>Delivery Charges</div>
                        <div class="text-success">FREE</div>
                    </div>
                    
                    <div class="border-top border-dashed pt-3 mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-bold fs-5">Total Amount</div>
                            <div class="fw-bold fs-5">₹{{ number_format($total) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Promotion Box -->
             <div class="bg-white p-3 shadow-sm rounded-1 d-flex justify-content-between align-items-center">
                 <div>
                     <div class="text-success fw-bold">10% instant discount</div>
                     <div class="small text-muted">Claim now with payment offers</div>
                 </div>
                 <div class="bg-light px-2 py-1 rounded small text-muted">
                     − <i class="bi bi-bank2"></i> +3
                 </div>
             </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function selectOption(id) {
        // Update styling
        document.querySelectorAll('.option-header').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.option-content').forEach(el => el.classList.remove('show'));
        
        // Activate current
        event.currentTarget.classList.add('active');
        const content = document.getElementById('content-' + id);
        if(content) content.classList.add('show');
        
        // Update hidden input
        document.getElementById('selectedMethod').value = id;
    }
</script>
@endpush
