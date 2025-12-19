@extends('layouts.master')

@section('title', 'About Us • MyStore')

@section('content')
<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <h1 class="fw-bold display-5 mb-3">Welcome to MyStore</h1>
            <p class="lead text-muted mb-4">Your one-stop destination for premium products and exceptional service.</p>
            <p class="text-secondary">
                Founded with a vision to revolutionize online shopping, MyStore brings you a curated collection of high-quality products ranging from electronics to fashion. We believe in quality, transparency, and customer satisfaction above all else.
            </p>
            <p class="text-secondary">
                Our team works tirelessly to source the best items, ensuring that every purchase you make is backed by our commitment to excellence. Whether you're looking for the latest gadgets or timeless fashion pieces, we've got you covered.
            </p>
        </div>
        <div class="col-lg-6">
            <div class="bg-light rounded-4 p-5 text-center">
                <i class="bi bi-bag-heart display-1 text-primary mb-3"></i>
                <div class="h5 fw-bold">Passion for Quality</div>
            </div>
        </div>
    </div>

    <div class="row g-4 text-center">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-4">
                <div class="card-body">
                    <i class="bi bi-truck fs-1 text-success mb-3"></i>
                    <h5 class="fw-bold">Fast Delivery</h5>
                    <p class="text-muted small">We ensure your orders reach you safe and sound in record time.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-4">
                <div class="card-body">
                    <i class="bi bi-shield-check fs-1 text-primary mb-3"></i>
                    <h5 class="fw-bold">Secure Payment</h5>
                    <p class="text-muted small">Your transactions are protected with top-tier security standards.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-4">
                <div class="card-body">
                    <i class="bi bi-headset fs-1 text-warning mb-3"></i>
                    <h5 class="fw-bold">24/7 Support</h5>
                    <p class="text-muted small">Our dedicated support team is always here to help you.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
