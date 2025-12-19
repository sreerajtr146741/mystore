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
</style>
@endpush

@section('content')
<div class="container py-4">
    {{-- Hero Removed as per request --}}


    {{-- Advanced Filter --}}
    <form method="GET" action="{{ route('products.index') }}" class="card search-card shadow p-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control search-input-page" placeholder="Detailed search..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select search-select">
                    <option value="">All Categories</option>
                    @foreach(['Mobile Phones','Laptops','Tablets','Smart Watches','Headphones','Cameras','TVs','Gaming','Fashion','Shoes','Bags','Watches','Furniture','Home Decor','Kitchen','Sports','Gym & Fitness','Vehicles','Cars','Bikes','Accessories','Fruits','Vegetables','Groceries','Books','Toys','Other'] as $cat)
                        <option value="{{ $cat }}" {{ request('category')===$cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-go">Filter</button>
            </div>
        </div>
    </form>

    @if($products->count())
        <div class="row g-4 mt-1" id="product-grid">
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
        <div class="text-center text-muted py-5">
            <p class="mt-3">No products found.</p>
        </div>
    @endif
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