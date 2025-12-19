@extends('layouts.master')

@section('title', 'My Orders • MyStore')

@push('styles')
<style>
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: translateY(-2px); }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div class="d-flex align-items-center">
            <h2 class="fw-bolder mb-0 text-3xl">My Orders</h2>
            <span class="ms-3 badge bg-primary rounded-pill px-3">{{ $orders->total() }} Orders</span>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold hover-scale">
            <i class="bi bi-bag me-2"></i> Continue Shopping
        </a>
    </div>
    
    <div id="order-list">
        @include('orders.partials.card', ['orders' => $orders])
    </div>
    
    @if($orders->isEmpty())
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-dashed border-secondary border-opacity-25 mt-4">
            <div class="mb-4 text-primary opacity-50 display-1"><i class="bi bi-box2"></i></div>
            <h3 class="fw-bold text-gray-800">No orders placed yet</h3>
            <p class="text-muted mb-4 mx-auto" style="max-width: 400px;">Looks like you haven't bought anything yet. Explore our products and grab the best deals!</p>
            <a href="{{ route('products.index') }}" class="btn btn-lg btn-primary rounded-pill px-5 fw-bold shadow hover-scale">
                Start Shopping Now
            </a>
        </div>
    @endif

    @if($orders->hasMorePages())
        <div id="scroll-sentinel" class="d-flex justify-content-center my-4">
            <div class="spinner-border text-primary d-none" role="status" id="loading-spinner">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let nextPageUrl = "{{ $orders->nextPageUrl() }}";
        const sentinel = document.getElementById('scroll-sentinel');
        const spinner = document.getElementById('loading-spinner');
        const list = document.getElementById('order-list');
        let isLoading = false;

        if (sentinel && nextPageUrl) {
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && !isLoading && nextPageUrl) {
                    loadMoreOrders();
                }
            }, { rootMargin: '200px' });

            observer.observe(sentinel);

            function loadMoreOrders() {
                isLoading = true;
                spinner.classList.remove('d-none');

                fetch(nextPageUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    spinner.classList.add('d-none');
                    if (data.html) {
                        const temp = document.createElement('div');
                        temp.innerHTML = data.html;
                        list.append(...temp.children);
                        
                        nextPageUrl = data.next_url;
                        if (!nextPageUrl) {
                            observer.disconnect();
                            sentinel.remove();
                        }
                        isLoading = false;
                    } else {
                        observer.disconnect();
                        sentinel.remove();
                    }
                })
                .catch(() => {
                    spinner.classList.add('d-none');
                    isLoading = false; 
                });
            }
        }
    });
</script>
@endpush
