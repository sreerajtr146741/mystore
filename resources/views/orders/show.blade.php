@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="fw-bold mb-0">Order Details</h2>
        <span class="text-muted">Order ID: #{{ $order->id }}</span>
    </div>

    <!-- Tracker -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
        <h5 class="fw-bold mb-4">Delivery Status</h5>
        <div class="position-relative m-4">
            <div class="progress" style="height: 4px;">
                @php
                    $status = $order->status;
                    $val = 0;
                    if($status == 'placed') $val = 25;
                    if($status == 'processing') $val = 50;
                    if($status == 'shipped') $val = 75;
                    if($status == 'delivered') $val = 100;
                @endphp
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $val }}%;" aria-valuenow="{{ $val }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="d-flex justify-content-between position-absolute top-0 w-100" style="margin-top: -10px;">
                <span class="bg-{{ $val >= 25 ? 'success' : 'secondary' }} rounded-circle d-block" style="width: 20px; height: 20px;"></span>
                <span class="bg-{{ $val >= 50 ? 'success' : 'secondary' }} rounded-circle d-block" style="width: 20px; height: 20px;"></span>
                <span class="bg-{{ $val >= 75 ? 'success' : 'secondary' }} rounded-circle d-block" style="width: 20px; height: 20px;"></span>
                <span class="bg-{{ $val >= 100 ? 'success' : 'secondary' }} rounded-circle d-block" style="width: 20px; height: 20px;"></span>
            </div>
            <div class="d-flex justify-content-between mt-2 small fw-bold text-muted text-uppercase" style="margin-left: -15px; margin-right: -15px;">
                <div class="{{ $val >= 25 ? 'text-success' : '' }}">Order Placed</div>
                <div class="{{ $val >= 50 ? 'text-success' : '' }}">Processing</div>
                <div class="{{ $val >= 75 ? 'text-success' : '' }}">Shipped</div>
                <div class="{{ $val >= 100 ? 'text-success' : '' }}">Delivered</div>
            </div>
        </div>
        
        @if($order->delivery_date)
            <div class="alert alert-info mt-4 mb-0 d-flex align-items-center">
                <i class="fas fa-truck me-3 fs-4"></i>
                <div>
                    <div class="small text-uppercase fw-bold opacity-75">Expected Delivery</div>
                    <div class="fw-bold fs-5">{{ $order->delivery_date->format('D, d M Y') }}</div>
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
