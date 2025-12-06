@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div class="d-flex align-items-center">
            <h2 class="fw-bolder mb-0 text-3xl">My Orders</h2>
            <span class="ms-3 badge bg-primary rounded-pill px-3">{{ $orders->total() }} Orders</span>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold hover-scale">
            <i class="fas fa-shopping-bag me-2"></i> Continue Shopping
        </a>
    </div>
    
    @forelse($orders as $order)
        <div class="card border-0 shadow-lg mb-4 rounded-4 overflow-hidden hover:shadow-2xl transition-all duration-300">
            <div class="card-header bg-white border-bottom-0 p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-indigo-100 text-indigo-700 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-box-open fs-5"></i>
                    </div>
                    <div>
                        <div class="text-xs text-uppercase text-muted fw-bold tracking-wider">Order ID</div>
                        <div class="fw-bold fs-5 text-dark">#{{ $order->id }}</div>
                    </div>
                </div>
                <div>
                   <div class="text-end">
                        @if($order->status == 'delivered')
                            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold border border-success-subtle">
                                <i class="fas fa-check-circle me-1"></i> Delivered
                            </span>
                        @elseif($order->status == 'shipped')
                            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold border border-primary-subtle">
                                <i class="fas fa-truck me-1"></i> Shipped
                            </span>
                        @elseif($order->status == 'processing')
                            <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill fw-bold border border-info-subtle">
                                <i class="fas fa-cog fa-spin me-1"></i> Processing
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-pill fw-bold border border-warning-subtle">
                                <i class="fas fa-clock me-1"></i> {{ ucfirst($order->status) }}
                            </span>
                        @endif
                   </div>
                   <div class="text-xs text-muted text-end mt-1">{{ $order->created_at->format('D, d M Y • h:i A') }}</div>
                </div>
            </div>
            
            <div class="card-body p-4 bg-light bg-opacity-25">
                <div class="row align-items-center g-4">
                    <!-- Product Previews -->
                    <div class="col-md-7">
                        <div class="d-flex align-items-center gap-3 overflow-auto pb-2" style="scrollbar-width: thin;">
                            @foreach($order->items->take(4) as $item)
                                <div class="position-relative" style="min-width: 70px;">
                                    <div class="ratio ratio-1x1 rounded-3 overflow-hidden border bg-white">
                                        <img src="{{ \Illuminate\Support\Str::startsWith($item->product->image, 'http') ? $item->product->image : asset('storage/'.$item->product->image) }}" 
                                             class="object-fit-cover" alt="Product">
                                    </div>
                                    @if($loop->iteration == 4 && $order->items->count() > 4)
                                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-flex align-items-center justify-content-center text-white fw-bold rounded-3">
                                            +{{ $order->items->count() - 3 }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            @if($order->items->count() == 1)
                                <div class="nav flex-column">
                                    <div class="fw-semibold text-dark">{{ $order->items->first()->product->name }}</div>
                                    <div class="small text-muted">Qnt: {{ $order->items->first()->qty }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Meta & Actions -->
                    <div class="col-md-5">
                        <div class="d-flex justify-content-between align-items-center h-100">
                            <div>
                                <div class="small text-muted text-uppercase fw-bold">Total Amount</div>
                                <div class="fs-4 fw-bold text-dark">₹{{ number_format($order->total, 2) }}</div>
                            </div>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm hover-scale">
                                View Order <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-12 bg-white rounded-4 shadow-sm border border-dashed border-secondary border-opacity-25">
            <div class="mb-4 text-primary opacity-50 display-1"><i class="fas fa-box-open"></i></div>
            <h3 class="fw-bold text-gray-800">No orders placed yet</h3>
            <p class="text-gray-500 mb-4 max-w-sm mx-auto">Looks like you haven't bought anything yet. Explore our products and grab the best deals!</p>
            <a href="{{ route('products.index') }}" class="btn btn-lg btn-primary rounded-pill px-5 fw-bold shadow-lg hover:shadow-xl transition-all">
                Start Shopping Now
            </a>
        </div>
    @endforelse

    <div class="mt-5">
        {{ $orders->links() }}
    </div>
</div>

<style>
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: translateY(-2px); }
</style>
@endsection
