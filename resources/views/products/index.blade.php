@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $img = function($path) {
        if (!$path) return null;
        if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
        try { return Storage::url($path); } catch (\Throwable $e) { return $path; }
    };
@endphp

@extends('layouts.master')

@section('title', 'Shop • MyStore')

@push('styles')
<style>
    /* Page specific styles */
    .hero{
        background: linear-gradient(120deg,#6d28d9 0%, #4c1d95 45%, #3b82f6 100%);
        color:#fff; border-radius:16px; padding:28px 22px;
        box-shadow:0 18px 40px rgba(76,29,149,.25);
    }
    .search-card{ margin-top:-22px; border:0; border-radius:16px; }
    .search-input-page, .search-select{ height:52px; border-radius:14px; }
    .btn-go{
        height:52px; border-radius:14px; font-weight:700; border:0; color:#07101a;
        background:linear-gradient(135deg,#22d3ee,#60a5fa);
        box-shadow:0 10px 22px rgba(96,165,250,.25);
    }
    .card-prod{
        position:relative; border:0; border-radius:16px; overflow:hidden; transition:.25s; background:#fff;
        box-shadow:0 6px 20px rgba(2,6,23,.06); cursor:pointer;
    }
    .card-prod:hover{ transform:translateY(-6px); box-shadow:0 16px 32px rgba(2,6,23,.12); }
    .img-fit{ width:100%; aspect-ratio:4/3; object-fit:cover; display:block; }
    .price{ font-weight:800; }
    .strike{ text-decoration: line-through; opacity:.7; margin-right:.4rem; }
    .badge-cat{ background:#fff3c4; color:#7c2d12; border:1px solid #fde68a; }
    .muted{ color:#64748b; }
    .ribbon{
        --c:#22c55e;
        position:absolute; top:12px; left:-40px; background:var(--c); color:#fff;
        padding:6px 60px; transform:rotate(-35deg); font-weight:700; box-shadow:0 6px 16px rgba(34,197,94,.3);
        letter-spacing:.5px; font-size:.85rem;
    }
    .stock-dot{ width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:.4rem; }
    .pagination-container{ display:none; }
    
    /* Standard Banner Size */
    .banner-container {
        width: 100%;
        aspect-ratio: 5/1; /* Further reduced height (very thin banner) */
        overflow: hidden;
        position: relative;
    }
    
    .standard-banner {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top center; /* Changed from center to top to handle top-heavy content in short banner */
    }
    
    /* Mobile optimization for banners */
    @media (max-width: 768px) {
        .banner-container {
            aspect-ratio: 2.5/1; /* Adjusted for mobile to not be too thin */
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4" style="background-color: #f1f3f6;">
    <div class="row g-3">
        {{-- Sidebar Filters --}}
        <div class="col-lg-3 col-xl-2" style="position: sticky; top: 80px; height: fit-content; z-index: 1;">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">Filters</h5>
                </div>
                <div class="card-body p-0">
                    {{-- Search Filter --}}
                    <div class="p-3 border-bottom">
                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Search</h6>
                        <form action="{{ route('products.index') }}" method="GET">
                            {{-- Removed hidden category input to make sidebar search global per user request --}}
                            <div class="input-group">
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search products..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                            </div>
                        </form>
                    </div>

                    {{-- Categories Filter --}}
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="text-uppercase text-muted small fw-bold mb-0">Categories</h6>
                            @if(request('category'))
                                <a href="{{ route('products.index') }}" class="small text-decoration-none text-danger">Clear</a>
                            @endif
                        </div>
                        <div class="d-flex flex-column gap-2">
                            @foreach(['Mobile Phones','Laptops','Tablets','Smart Watches','Headphones','Cameras','TVs','Gaming','Fashion','Shoes','Bags','Watches','Furniture','Home Decor','Kitchen','Sports','Gym & Fitness','Vehicles','Cars','Bikes','Accessories','Fruits','Vegetables','Groceries','Books','Toys','Other'] as $cat)
                                <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $cat])) }}" 
                                   class="text-decoration-none {{ request('category') === $cat ? 'fw-bold text-primary' : 'text-dark' }}"
                                   style="font-size: 0.95rem;">
                                   <div class="d-flex align-items-center">
                                       @if(request('category') === $cat)
                                        <i class="bi bi-check2 me-2"></i>
                                       @else
                                        <i class="bi bi-chevron-right me-2 text-muted" style="font-size: 0.75rem;"></i>
                                       @endif
                                       {{ $cat }}
                                   </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Grid --}}
        <div class="col-lg-9 col-xl-10">
            
            {{-- Carousel Slider (Always Visible) --}}
            <div id="homeCarousel" class="carousel slide mb-4 shadow-sm rounded-4 overflow-hidden" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="0" class="active"></button>
                    @if(isset($carouselSlides))
                        @foreach($carouselSlides as $idx => $slide)
                            <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="{{ $idx + 1 }}"></button>
                        @endforeach
                    @endif
                </div>
                <div class="carousel-inner">
                    {{-- Static Slide 1: Home Banner --}}
                    <div class="carousel-item active">
                        <div class="banner-container">
                            <img src="https://placehold.co/1200x250/6d28d9/ffffff?text=Super+Sale+Starts+Now" class="d-block standard-banner" alt="Super Sale">
                        </div>
                    </div>

                    {{-- Dynamic Slides (New Arrivals Style) --}}
                    @if(isset($carouselSlides))
                        @foreach($carouselSlides as $slide)
                            <div class="carousel-item">
                                <a href="{{ $slide['link'] }}" class="d-block position-relative banner-container">
                                    <img src="{{ $slide['image'] }}" class="d-block standard-banner" alt="{{ $slide['title'] }}">
                                    

                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>

            {{-- Sort Header --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body py-2 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="fw-bold fs-5">MyStore</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="me-2 small fw-bold text-muted">Sort By</span>
                        <select class="form-select form-select-sm border-0" style="width: auto; font-weight: 500; cursor: pointer;" onchange="updateSort(this.value)">
                            <option value="relevance" {{ request('sort') == 'relevance' ? 'selected' : '' }}>Relevance</option>
                            <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Popularity</option>
                            <option value="price_low_high" {{ request('sort') == 'price_low_high' ? 'selected' : '' }}>Price -- Low to High</option>
                            <option value="price_high_low" {{ request('sort') == 'price_high_low' ? 'selected' : '' }}>Price -- High to Low</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        </select>
                    </div>
                </div>
            </div>

            <script>
                function updateSort(value) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('sort', value);
                    url.searchParams.delete('page'); // Reset to page 1
                    window.location.href = url.toString();
                }
            </script>

            @if($products->count())
                <div class="row g-2" id="product-grid">
                     {{-- Using standard card layout, slightly compact for 'shop' feel --}}
                    @include('partials.product-list', ['products' => $products])
                </div>

                {{-- Loader for Infinite Scroll --}}
                <div id="loading-spinner" class="text-center py-4 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                
                {{-- Sentinel --}}
                <div id="sentinel" style="height: 10px;"></div>

                {{-- Hidden Pagination Data --}}
                @if($products->hasMorePages())
                    <div id="pagination-data" data-next-url="{{ $products->nextPageUrl() }}" style="display:none;"></div>
                @endif
            @else
                <div class="card border-0 shadow-sm py-5 text-center">
                    <div class="card-body">
                        <img src="https://static-assets-web.flixcart.com/fk-p-linchpin-web/fk-cp-zion/img/error-no-search-results_2353c5.png" alt="No Results" class="mb-4" style="max-width: 200px;">
                        <h4>Sorry, no results found!</h4>
                        <p class="text-muted">Please check the spelling or try searching for something else</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Infinite Scroll Logic
        let nextUrl = document.getElementById('pagination-data')?.dataset.nextUrl;
        const sentinel = document.getElementById('sentinel');
        const spinner = document.getElementById('loading-spinner');
        const grid = document.getElementById('product-grid');
        let isLoading = false;

        if (sentinel && nextUrl) {
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && !isLoading && nextUrl) {
                    loadMoreProducts();
                }
            }, { rootMargin: '200px' });

            observer.observe(sentinel);

            function loadMoreProducts() {
                isLoading = true;
                spinner.classList.remove('d-none');

                fetch(nextUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    spinner.classList.add('d-none');
                    if (html.trim().length > 0) {
                        grid.insertAdjacentHTML('beforeend', html);
                        
                        const currentUrl = new URL(nextUrl);
                        const currentPage = parseInt(currentUrl.searchParams.get('page') || 1);
                        currentUrl.searchParams.set('page', currentPage + 1);
                        nextUrl = currentUrl.toString();

                        isLoading = false;
                    } else {
                        observer.disconnect();
                        sentinel.remove();
                    }
                })
                .catch(err => {
                    console.error('Scroll Error:', err);
                    spinner.classList.add('d-none');
                    isLoading = false;
                });
            }
        }

        // 2. Card Click Logic (Global handler or specific)
        document.body.addEventListener('click', function(e) {
            const card = e.target.closest('.card-prod');
            if (card) {
                if (e.target.closest('.stop-click, button, input, a')) return;
                const href = card.getAttribute('data-href');
                if (href) window.location.href = href;
            }
        });
    });
</script>
@endpush