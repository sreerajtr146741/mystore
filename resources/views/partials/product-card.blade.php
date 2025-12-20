@php
    use Illuminate\Support\Str;
    $img = function($path) {
        if (!$path) return null;
        if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
        try { return Storage::url($path); } catch (\Throwable $e) { return $path; }
    };
    
    $photo = $img($p->image);
    $finalPrice  = $p->discounted_price ?? $p->final_price ?? $p->price;
    $hasDiscount = $finalPrice < $p->price;
    $saveAmt = $p->price - $finalPrice;
    $savePct = $p->price > 0 ? round(($saveAmt / $p->price) * 100) : 0;
    $stock = $p->stock ?? null;
    $href = route('products.show', $p);
@endphp

<div class="card card-prod h-100" data-href="{{ $href }}">
    {{-- Discount Ribbon --}}
    @if($hasDiscount)
        <div class="ribbon">{{ $savePct }}% OFF</div>
    @endif

    {{-- Image --}}
    @if($photo)
        <img class="img-fit" src="{{ $photo }}" alt="{{ $p->name }}">
    @else
        <div class="bg-light d-flex align-items-center justify-content-center" style="aspect-ratio:4/3;">
            <i class="bi bi-image fs-1 text-muted"></i>
        </div>
    @endif

    <div class="card-body d-flex flex-column">
        <h5 class="fw-bold mb-1 text-truncate" title="{{ $p->name }}">{{ $p->name }}</h5>

        <div class="mb-2">
            @if($p->category)
                <span class="badge badge-cat">{{ $p->category }}</span>
            @endif
        </div>



        <div class="mt-auto">
            @if(!is_null($stock))
                <div class="small {{ $stock > 0 ? 'text-success' : 'text-danger' }} mb-2">
                    <span class="stock-dot" style="background:{{ $stock > 0 ? '#22c55e' : '#ef4444' }}"></span>
                    {{ $stock > 0 ? $stock.' in stock' : 'Out of stock' }}
                </div>
            @endif

            <div>
                @if($hasDiscount)
                    <div>
                        <span class="strike muted">₹{{ number_format($p->price, 2) }}</span>
                        <span class="price text-success">₹{{ number_format($finalPrice, 2) }}</span>
                    </div>
                @else
                    <span class="price">₹{{ number_format($p->price, 2) }}</span>
                @endif
            </div>
        </div>
    </div>
</div>
