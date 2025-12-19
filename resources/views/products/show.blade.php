@extends('layouts.master')

@section('title', $product->name . ' • MyStore')

@php
    use Illuminate\Support\Facades\Storage;
    $p = $product;

    // Image helper
    $img = function($path){
        if(!$path) return null;
        if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
        if (Storage::disk('public')->exists($path)) return asset('storage/'.$path);
        return $path;
    };

    $photo = $img($p->image);

    // Pricing logic
    $final = $p->discounted_price ?? $p->final_price ?? $p->price;
    $hasDisc = $final < $p->price;

    // Fallback logic check shouldn't be needed if controller is correct, but keeping safe
    if (!$hasDisc && ($p->discount_type && $p->discount_value > 0 && ($p->is_discount_active ?? false))) {
        $final = $p->discount_type === 'percent'
            ? max(0, $p->price - ($p->price * ($p->discount_value/100)))
            : max(0, $p->price - $p->discount_value);
        $hasDisc = true;
    }

    $saveAmt = max(0, $p->price - $final);
    $savePct = $p->price > 0 ? round(($saveAmt / $p->price) * 100) : 0;
    $stock   = $p->stock ?? $p->qty ?? null;
@endphp

@push('styles')
<style>
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
@endpush

@section('content')
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
                </div>
            </div>
        </div>
    </div>
    
    {{-- Description & Services Row --}}
    <div class="row mt-4">
        <div class="col-lg-7 mb-4 mb-lg-0">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Description</h5>
                    <p class="mb-0 lh-lg text-secondary" style="white-space: pre-wrap;">{{ $p->description ?: 'No description available.' }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Available Services</h5>
                    
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class="bi bi-arrow-counterclockwise fs-4 text-primary"></i>
                        <div>
                            <div class="fw-bold small">7 Days Replacement Policy</div>
                            <div class="small text-muted">Returns accepted within 7 days</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class="bi bi-cash-coin fs-4 text-primary"></i>
                        <div>
                            <div class="fw-bold small">Cash on Delivery Available</div>
                            <div class="small text-muted">Pay securely at your doorstep</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-3">
                        <i class="bi bi-shield-check fs-4 text-primary"></i>
                        <div>
                            <div class="fw-bold small">1 Year Warranty</div>
                            <div class="small text-muted">Detailed warranty info inside box</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Similar Products Section --}}
    @if(isset($similarProducts) && $similarProducts->count() > 0)
    <div class="row mt-5">
        <div class="col-12 mb-4">
            <h3 class="fw-bold">Similar Products</h3>
        </div>
        @foreach($similarProducts as $sp)
            @php
                // Calc similar product price
                $sFinal = $sp->discounted_price ?? $sp->final_price ?? $sp->price;
                $sHasDisc = $sFinal < $sp->price;
                
                // Image handling
                $sImage = $sp->image;
                if ($sImage && !filter_var($sImage, FILTER_VALIDATE_URL)) {
                   $sImage = asset('storage/' . $sImage);
                }
            @endphp
            <div class="col-6 col-md-3 mb-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative product-card">
                    <a href="{{ route('products.show', $sp->id) }}" class="text-decoration-none">
                        <div class="position-relative" style="padding-top: 100%;">
                            @if($sp->image)
                                <img src="{{ $sImage }}" alt="{{ $sp->name }}" 
                                     class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover p-3">
                            @else
                                <div class="position-absolute top-0 start-0 w-100 h-100 bg-light d-flex align-items-center justify-content-center text-muted">
                                    <i class="bi bi-image fs-1"></i>
                                </div>
                            @endif
                        </div>
                    </a>
                    <div class="card-body p-3 d-flex flex-column">
                        <h6 class="card-title text-truncate fw-bold mb-1">
                            <a href="{{ route('products.show', $sp->id) }}" class="text-dark text-decoration-none">{{ $sp->name }}</a>
                        </h6>
                        
                        <div class="mb-3">
                            @if($sHasDisc)
                                <small class="text-decoration-line-through text-muted me-1">₹{{ number_format($sp->price,0) }}</small>
                                <span class="fw-bold text-success">₹{{ number_format($sFinal,0) }}</span>
                            @else
                                <span class="fw-bold text-dark">₹{{ number_format($sFinal,0) }}</span>
                            @endif
                        </div>

                        <div class="mt-auto d-flex gap-2">
                            {{-- Add to Cart --}}
                            @auth
                                <form action="{{ route('cart.add', $sp->id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="qty" value="1">
                                    <button type="submit" class="btn btn-sm btn-outline-warning w-100 fw-bold border-2 rounded-pill">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                </form>
                                {{-- Buy Now --}}
                                <form action="{{ route('checkout.single', $sp->id) }}" method="GET" class="flex-fill">
                                    <input type="hidden" name="qty" value="1">
                                    <button type="submit" class="btn btn-sm btn-outline-success w-100 fw-bold border-2 rounded-pill">
                                        Buy
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary w-100 rounded-pill">Login to Buy</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif
</div>
@endsection

@push('scripts')
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

        if(qtyInput) {
            qtyInput.addEventListener('input', updateTotal);
            updateTotal();
        }
    })();
</script>
@endpush