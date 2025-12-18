@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="fw-bold mb-0">Order Details</h2>
        <div class="d-flex align-items-center gap-3">
             <span class="text-muted me-2">Order ID: #{{ $order->id }}</span>
             @if(in_array($order->status, ['placed', 'processing']))
                <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">Cancel Order</button>
                </form>
            @endif
            @if($order->status == 'delivered')
                <form action="{{ route('orders.return', $order->id) }}" method="POST" onsubmit="return confirm('Request a return for this order?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning btn-sm rounded-pill px-3 fw-bold">Return Order</button>
                </form>
            @endif
        </div>
    </div>

    <!-- Tracker -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
        <h5 class="fw-bold mb-4">Delivery Status</h5>
        
        @if($order->status == 'cancelled')
            <div class="alert alert-danger d-flex align-items-center mb-0">
                <i class="fas fa-times-circle fs-4 me-3"></i>
                <div>
                    <div class="fw-bold">Order Cancelled</div>
                    <div class="small">This order has been cancelled. Please contact support for help.</div>
                </div>
            </div>
        @elseif($order->status == 'returned')
            <div class="alert alert-secondary d-flex align-items-center mb-0">
                <i class="fas fa-undo-alt fs-4 me-3"></i>
                <div>
                    <div class="fw-bold">Order Returned</div>
                    <div class="small">This order has been returned and refunded.</div>
                </div>
            </div>
        @else
            <div class="position-relative mx-3 my-4">
                {{-- Progress Bar Background --}}
                <div class="progress position-absolute top-50 start-0 w-100 translate-middle-y" style="height: 4px; z-index: 1;">
                    <div class="progress-bar bg-success" role="progressbar" 
                        style="width: {{ 
                            match($order->status) {
                                'placed' => '0%',
                                'processing' => '33%',
                                'shipped' => '66%',
                                'delivered' => '100%',
                                'return_requested' => '100%',
                                default => '0%'
                            }
                        }};"></div>
                </div>

                <div class="d-flex justify-content-between position-relative" style="z-index: 2;">
                    {{-- Placed --}}
                    <div class="text-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 text-white {{ in_array($order->status, ['placed', 'processing', 'shipped', 'delivered', 'return_requested', 'out_for_delivery']) ? 'bg-success' : 'bg-secondary' }}" 
                             style="width: 40px; height: 40px; border: 4px solid #fff; box-shadow: 0 0 0 1px #dee2e6;">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="small fw-bold {{ in_array($order->status, ['placed', 'processing', 'shipped', 'delivered', 'return_requested', 'out_for_delivery']) ? 'text-dark' : 'text-muted' }}">Placed</div>
                    </div>

                    {{-- Processing --}}
                    <div class="text-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 text-white {{ in_array($order->status, ['processing', 'shipped', 'delivered', 'return_requested', 'out_for_delivery']) ? 'bg-success' : 'bg-secondary' }}" 
                             style="width: 40px; height: 40px; border: 4px solid #fff; box-shadow: 0 0 0 1px #dee2e6;">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="small fw-bold {{ in_array($order->status, ['processing', 'shipped', 'delivered', 'return_requested', 'out_for_delivery']) ? 'text-dark' : 'text-muted' }}">Processing</div>
                    </div>

                    {{-- Shipped --}}
                    <div class="text-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 text-white {{ in_array($order->status, ['shipped', 'delivered', 'return_requested', 'out_for_delivery']) ? 'bg-success' : 'bg-secondary' }}" 
                             style="width: 40px; height: 40px; border: 4px solid #fff; box-shadow: 0 0 0 1px #dee2e6;">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="small fw-bold {{ in_array($order->status, ['shipped', 'delivered', 'return_requested', 'out_for_delivery']) ? 'text-dark' : 'text-muted' }}">Shipped</div>
                    </div>

                    {{-- Delivered --}}
                    <div class="text-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 text-white {{ in_array($order->status, ['delivered', 'return_requested']) ? 'bg-success' : 'bg-secondary' }}" 
                             style="width: 40px; height: 40px; border: 4px solid #fff; box-shadow: 0 0 0 1px #dee2e6;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="small fw-bold {{ in_array($order->status, ['delivered', 'return_requested']) ? 'text-dark' : 'text-muted' }}">Delivered</div>
                    </div>
                </div>
            </div>

            @if($order->status == 'processing')
                <div class="alert alert-info py-2 small d-inline-block mt-3"><i class="fas fa-info-circle me-1"></i> We are packing your order.</div>
            @elseif($order->status == 'shipped')
                <div class="alert alert-primary py-2 small d-inline-block mt-3"><i class="fas fa-truck-moving me-1"></i> Your order is on the way!</div>
            @elseif($order->status == 'return_requested')
                <div class="alert alert-warning py-2 small d-inline-block mt-3"><i class="fas fa-clock me-1"></i> You have requested a return. Waiting for approval.</div>
            @endif
        @endif
        

        @if($order->delivery_date && !in_array($order->status, ['cancelled', 'return_requested', 'returned', 'delivered']))
            <div class="mt-4 pt-3 border-top d-flex align-items-center text-muted">
                <i class="far fa-calendar-alt me-2 fs-5"></i>
                <div>
                    <span class="small text-uppercase fw-bold">Expected Delivery</span><br>
                    <span class="text-dark fw-bold">{{ $order->delivery_date->format('D, d M Y') }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Items & Address -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 fw-bold">Items Ordered</div>
                <div class="card-body p-0">
                    @foreach($order->items as $item)
                        <div class="d-flex p-3 border-bottom">
                            <div style="width: 80px; height: 80px;" class="flex-shrink-0 bg-light rounded overflow-hidden">
                                <img src="{{ \Illuminate\Support\Str::startsWith($item->product->image, 'http') ? $item->product->image : asset('storage/'.$item->product->image) }}" 
                                     class="w-100 h-100 object-fit-cover" alt="Product">
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <h6 class="fw-bold mb-1">{{ $item->product->name }}</h6>
                                <div class="small text-muted">{{ $item->qty }} x ₹{{ number_format($item->price, 2) }}</div>
                            </div>
                            <div class="fw-bold text-end">
                                <div>₹{{ number_format($item->price * $item->qty, 2) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer bg-light p-3 text-end fw-bold">
                    Total: ₹{{ number_format($order->total, 2) }}
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 fw-bold">Shipping Details</div>
                <div class="card-body">
                    <h6 class="fw-bold">{{ auth()->user()->name }}</h6>
                    <p class="mb-0 text-muted small">{{ $order->shipping_address }}</p>
                    <hr>
                    <div class="small text-muted">Phone: {{ auth()->user()->phone ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
