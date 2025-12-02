{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="min-vh-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container py-5">
        <!-- Header -->
        <div class="text-center text-white mb-5 pt-4">
            <h1 class="display-4 fw-bold mb-3">
                Welcome back, <span class="text-warning">{{ auth()->user()->name }}</span>
            </h1>
            <p class="lead opacity-90">Here's what's happening in your store today</p>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <!-- Total Users -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-lg h-100 bg-white bg-opacity-10 backdrop-blur-lg text-white border-white border-opacity-20">
                    <div class="card-body text-center p-5">
                        <i class="bi bi-people-fill fs-1 text-info mb-3"></i>
                        <h2 class="display-5 fw-bold mb-1">{{ $stats['total_users'] }}</h2>
                        <p class="mb-0 opacity-90">Total Users</p>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-lg h-100 bg-white bg-opacity-10 backdrop-blur-lg text-white border-white border-opacity-20">
                    <div class="card-body text-center p-5">
                        <i class="bi bi-box-seam-fill fs-1 text-success mb-3"></i>
                        <h2 class="display-5 fw-bold mb-1">{{ $stats['total_products'] ?? 0 }}</h2>
                        <p class="mb-0 opacity-90">Total Products</p>
                    </div>
                </div>
            </div>

            <!-- Pending Seller Applications -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-lg h-100 bg-white bg-opacity-10 backdrop-blur-lg text-white border-white border-opacity-20">
                    <div class="card-body text-center p-5">
                        <i class="bi bi-person-check-fill fs-1 text-warning mb-3"></i>
                        <h2 class="display-5 fw-bold mb-1">{{ $stats['pending_sellers'] ?? 0 }}</h2>
                        <p class="mb-0 opacity-90">Pending Sellers</p>
                    </div>
                </div>
            </div>

            <!-- Total Orders (if you have) -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-lg h-100 bg-white bg-opacity-10 backdrop-blur-lg text-white border-white border-opacity-20">
                    <div class="card-body text-center p-5">
                        <i class="bi bi-cart-check-fill fs-1 text-danger mb-3"></i>
                        <h2 class="display-5 fw-bold mb-1">{{ $stats['total_orders'] ?? 0 }}</h2>
                        <p class="mb-0 opacity-90">Total Orders</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="text-center">
            <h3 class="text-white mb-4 fw-light">Quick Actions</h3>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="{{ route('admin.users') }}" 
                   class="btn btn-light btn-lg px-5 py-3 shadow-lg rounded-pill fw-bold">
                    Manage Users
                </a>
                <a href="{{ route('admin.products') }}" 
                   class="btn btn-outline-light btn-lg px-5 py-3 shadow-lg rounded-pill fw-bold">
                    All Products
                </a>
                <a href="{{ route('admin.seller-applications') }}" 
                   class="btn btn-warning btn-lg px-5 py-3 shadow-lg rounded-pill fw-bold text-dark">
                    Review Sellers ({{ $stats['pending_sellers'] ?? 0 }}) Applications
                </a>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="text-center mt-5">
            <p class="text-white-50 small">
                Last updated: {{ now()->format('d M Y, h:i A') }} | 
                <i class="bi bi-shield-lock"></i> Admin Access Only
            </p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .card {
        transition: all 0.3s ease;
        border-radius: 20px !important;
    }
    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.3) !important;
    }
    .btn {
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    }
    .backdrop-blur-lg {
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
</style>
@endpush