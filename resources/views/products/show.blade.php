@extends('layouts.master')

@section('title', $product->name . ' • MyStore')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
    $p = $product;
    
    // Image Helper
    $img = function($path){
        if(!$path) return null;
        if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
        if (Storage::disk('public')->exists($path)) return asset('storage/'.$path);
        return asset('storage/'.$path); // fallback
    };
    
    // Pricing
    $final = $p->discounted_price ?? $p->final_price ?? $p->price;
    $hasDisc = $final < $p->price;
    $saveAmt = max(0, $p->price - $final);
    $savePct = $p->price > 0 ? round(($saveAmt / $p->price) * 100) : 0;
    
    // Gallery Images (Main + Banners as gallery for now)
    $gallery = collect();
    if($p->image) $gallery->push($img($p->image));
    if($p->banners) {
        foreach($p->banners as $b) {
            $gallery->push($img($b->image));
        }
    }
    // If no images at all
    if($gallery->isEmpty()) $gallery->push(asset('images/placeholder.png')); // simplified
    
    $mainImage = $gallery->first();
@endphp

<style>
    body { background-color: #f1f3f6; font-family: Roboto, Arial, sans-serif; }
    .product-container { background: #fff; padding: 16px; box-shadow: 0 1px 1px 0 rgba(0,0,0,.16); }
    
    /* Left Column */
    .left-col { position: sticky; top: 20px; }
    .gallery-wrapper { display: flex; flex-direction: row; gap: 10px; }
    .thumbnails { display: flex; flex-direction: column; gap: 5px; width: 64px; }
    .thumb-box { 
        width: 64px; height: 64px; border: 1px solid #f0f0f0; 
        cursor: pointer; overflow: hidden; padding: 2px;
        transition: border-color .2s;
    }
    .thumb-box:hover, .thumb-box.active { border-color: #2874f0; }
    .thumb-box img { width: 100%; height: 100%; object-fit: contain; }
    
    .main-image-box { 
        flex-grow: 1; height: 500px; display: flex; align-items: center; justify-content: center; 
        border: 1px solid #f0f0f0; position: relative;
    }
    .main-image-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
    
    .action-btns { margin-top: 15px; display: flex; gap: 10px; }
    .btn-fk { 
        flex: 1; padding: 18px 8px; border: none; color: #fff; 
        font-weight: 600; font-size: 16px; border-radius: 2px; 
        text-transform: uppercase; box-shadow: 0 1px 2px 0 rgba(0,0,0,.2);
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: box-shadow .2s;
    }
    .btn-fk:hover { box-shadow: 0 4px 8px 0 rgba(0,0,0,.2); color: #fff !important; opacity: 0.95; }
    .btn-cart { background: #ff9f00; }
    .btn-buy { background: #fb641b; }
    .btn-cart:hover { background: #f29700; }
    .btn-buy:hover { background: #ee5b16; }

    /* Right Column */
    .breadcrumb { font-size: 12px; color: #878787; margin-bottom: 5px; }
    .breadcrumb a { color: #878787; text-decoration: none; }
    .breadcrumb a:hover { color: #2874f0; }
    
    .product-title { font-size: 18px; color: #212121; margin-bottom: 5px; }
    
    .rating-badge { 
        background-color: #388e3c; color: #fff; font-size: 12px; padding: 2px 6px; 
        border-radius: 3px; font-weight: 500; display: inline-flex; align-items: center; vertical-align: middle; gap: 2px;
    }
    
    .price-block { display: flex; align-items: baseline; gap: 10px; margin: 10px 0; }
    .final-price { font-size: 28px; font-weight: 500; color: #212121; }
    .original-price { font-size: 16px; color: #878787; text-decoration: line-through; }
    .discount-pct { font-size: 16px; color: #388e3c; font-weight: 500; }
    
    .offers-list { list-style: none; padding: 0; font-size: 14px; margin-top: 10px; }
    .offers-list li { margin-bottom: 8px; display: flex; gap: 8px; color: #212121; }
    .tag-icon { color: #16bd49; flex-shrink: 0; margin-top: 2px; }
    
    .section-head { font-size: 16px; font-weight: 500; color: #212121; display: flex; width: 110px; flex-shrink: 0; }
    .row-section { display: flex; margin-top: 24px; }
    .row-content { flex-grow: 1; }
    
    .highlights-list { list-style: none; padding: 0; font-size: 14px; color: #212121; margin: 0; }
    .highlights-list li { margin-bottom: 5px; display: flex; gap: 8px; }
    .highlights-list li::before { content: "•"; color: #c2c2c2; }
    
    .specs-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .specs-table td { padding: 8px 0; vertical-align: top; }
    .col-key { color: #878787; width: 33%; }
    .col-val { color: #212121; }
    .spec-cat-title { font-size: 18px; color: #000; margin-top: 20px; border-bottom: 1px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 10px; }
    
    .desc-text { font-size: 14px; color: #212121; line-height: 1.5; white-space: pre-wrap; }
    
    /* Responsive */
    @media(max-width: 768px){
        .left-col { position: static; }
        .gallery-wrapper { flex-direction: column-reverse; }
        .thumbnails { flex-direction: row; width: 100%; overflow-x: auto; }
        .section-head { width: 100%; margin-bottom: 10px; }
        .row-section { flex-direction: column; }
    }
</style>

<div class="container-fluid mt-2 mb-4" style="max-width: 1400px;">
    <div class="product-container row g-0">
        
        {{-- LEFT COLUMN: IMAGES & BUTTONS --}}
        <div class="col-md-5 col-lg-4 p-3 left-col">
            <div class="position-relative">
                <div class="gallery-wrapper">
                    {{-- Thumbnails --}}
                    @if($gallery->count() > 1)
                    <div class="thumbnails">
                        @foreach($gallery as $i => $src)
                            <div class="thumb-box {{ $loop->first ? 'active' : '' }}" onclick="changeImage('{{ $src }}', this)">
                                <img src="{{ $src }}" alt="Thumb">
                            </div>
                        @endforeach
                    </div>
                    @endif
                    
                    {{-- Main Image --}}
                    <div class="main-image-box">
                        {{-- Wishlist Icon (decorative) --}}
                        <div class="position-absolute top-0 end-0 p-3">
                            <i class="bi bi-heart-fill text-secondary fs-4" style="opacity: 0.3;"></i>
                        </div>
                        <img id="mainImage" src="{{ $mainImage }}" alt="{{ $p->name }}">
                    </div>
                </div>
            
                <div class="action-btns">
                    @auth
                        <form action="{{ route('cart.add', $p) }}" method="POST" class="flex-fill d-flex">
                            @csrf
                            <input type="hidden" name="qty" value="1">
                            <button class="btn btn-fk btn-cart flex-fill">
                                <i class="bi bi-cart-fill"></i> ADD TO CART
                            </button>
                        </form>
                        <form action="{{ route('checkout.single', $p->id) }}" method="GET" class="flex-fill d-flex">
                            <input type="hidden" name="qty" value="1">
                            <button class="btn btn-fk btn-buy flex-fill">
                                <i class="bi bi-lightning-fill"></i> BUY NOW
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-fk btn-cart"><i class="bi bi-cart-fill"></i> ADD TO CART</a>
                        <a href="{{ route('login') }}" class="btn btn-fk btn-buy"><i class="bi bi-lightning-fill"></i> BUY NOW</a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: DETAILS --}}
        <div class="col-md-7 col-lg-8 p-3 ps-md-4">
            
            {{-- Breadcrumb --}}
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Home</a> &nbsp;›&nbsp; 
                <a href="{{ route('products.index') }}">Products</a> &nbsp;›&nbsp; 
                @if($p->category) <a href="{{ route('products.index', ['category'=>$p->category]) }}">{{ $p->category }}</a> &nbsp;›&nbsp; @endif
                <span>{{ $p->name }}</span>
            </div>

            <h1 class="product-title fw-normal">{{ $p->name }}</h1>
            
            {{-- Rating removed as per request --}}
            {{-- <div class="d-flex align-items-center gap-2 mb-2">
                <div class="rating-badge">
                    4.6 <i class="bi bi-star-fill" style="font-size: 10px;"></i>
                </div>
                <span class="text-secondary fw-medium" style="font-size: 14px; color: #878787;">11 Ratings & 0 Reviews</span>
            </div> --}}

            <div class="price-block">
                <span class="final-price">₹{{ number_format($final, 0) }}</span>
                @if($hasDisc)
                    <span class="original-price">₹{{ number_format($p->price, 0) }}</span>
                    <span class="discount-pct">{{ $savePct }}% off</span>
                @endif
            </div>

            {{-- Offers --}}
            <div class="mb-3">
                <div class="fw-bold fs-6 mb-2">Available offers</div>
                <ul class="offers-list">
                    <li><i class="bi bi-tag-fill tag-icon"></i> <span><strong>Bank Offer</strong> 5% Unlimited Cashback on Flipkart Axis Bank Credit Card <a href="#" class="text-primary text-decoration-none">T&C</a></span></li>
                    <li><i class="bi bi-tag-fill tag-icon"></i> <span><strong>Bank Offer</strong> 10% off on SBI Credit Card, up to ₹1,500. On orders of ₹5,000 and above <a href="#" class="text-primary text-decoration-none">T&C</a></span></li>
                    <li><i class="bi bi-tag-fill tag-icon"></i> <span><strong>Partner Offer</strong> Sign up for Flipkart Pay Later and get Flipkart Gift Card worth up to ₹500* <a href="#" class="text-primary text-decoration-none">Know More</a></span></li>
                </ul>
            </div>

            {{-- Delivery --}}
            <div class="row-section">
                <div class="section-head text-secondary">Delivery</div>
                <div class="row-content">
                    <div class="fw-medium mb-1">Delivery by {{ now()->addDays(4)->format('d M, l') }} <span class="text-secondary">|</span> <span class="text-success">Free</span></div>
                    <div class="small text-secondary">
                         if ordered before {{ now()->addHours(5)->format('h:i A') }}
                    </div>
                </div>
            </div>

            {{-- Highlights --}}
            @if(!empty($p->highlights))
            <div class="row-section">
                <div class="section-head text-secondary">Highlights</div>
                <div class="row-content">
                    <ul class="highlights-list">
                        @foreach($p->highlights as $hl)
                            <li>{{ $hl }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- Description --}}
            @if($p->description)
            <div class="mt-4 border p-3 rounded">
                <div class="fs-5 fw-bold mb-3">Description</div>
                <div class="desc-text text-secondary">{{ $p->description }}</div>
            </div>
            @endif

            {{-- Specifications --}}
            @if(!empty($p->specifications))
            <div class="mt-4 border rounded p-3">
                <div class="fs-5 fw-bold mb-3">Specifications</div>
                
                @foreach($p->specifications as $cat => $items)
                    <div class="mb-3">
                        <div class="spec-cat-title fs-6">{{ $cat }}</div>
                        <table class="specs-table">
                            @foreach($items as $spec)
                                <tr>
                                    <td class="col-key">{{ $spec['key'] }}</td>
                                    <td class="col-val">{{ $spec['value'] }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endforeach
            </div>
            @endif

        </div>
    </div>
    
    {{-- Similar Products --}}
    @if(isset($similarProducts) && $similarProducts->count() > 0)
        <div class="card mt-2 border-0 shadow-sm p-3">
            <h5 class="fw-bold mb-3">Similar Products</h5>
            <div class="row g-3">
            @foreach($similarProducts as $sp)
                <div class="col-6 col-md-3">
                     <div class="p-2 border rounded text-center h-100">
                         <a href="{{ route('products.show', $sp->id) }}" class="text-decoration-none text-dark">
                             <img src="{{ $img($sp->image) }}" class="img-fluid mb-2" style="max-height: 150px;">
                             <div class="text-truncate fw-medium">{{ $sp->name }}</div>
                             <div class="text-success fw-bold">₹{{ number_format($sp->final_price ?? $sp->price, 0) }}</div>
                         </a>
                     </div>
                </div>
            @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function changeImage(src, el) {
        document.getElementById('mainImage').src = src;
        document.querySelectorAll('.thumb-box').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
    }
</script>
@endpush