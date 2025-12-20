@extends('layouts.master')

@section('title', 'Order Placed')

@section('content')
<div class="container py-5 text-center">
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 500px;">
        <div class="card-body p-5">
            <div class="mb-4">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
            </div>
            <h2 class="mb-3 fw-bold">Order Placed Successfully!</h2>
            <p class="text-muted mb-4">
                {{ session('success') ?? 'Your order has been confirmed.' }}
            </p>
            <div class="d-grid gap-2">
                <a href="{{ route('orders.index') }}" class="btn btn-primary py-2 fw-bold" style="background-color: #2874f0; border: none;">VIEW ORDERS</a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary py-2 fw-bold">CONTINUE SHOPPING</a>
            </div>
        </div>
    </div>
</div>
@endsection