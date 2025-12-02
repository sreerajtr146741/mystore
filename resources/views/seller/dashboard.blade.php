@extends('layouts.app')
@section('content')
<div class="container py-5">
    <h1 class="display-5 fw-bold text-indigo-700">Seller Dashboard</h1>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card p-4 shadow">
                <h3>Total Products</h3>
                <h2 class="text-primary">{{ $productsCount }}</h2>
            </div>
        </div>
    </div>
    <a href="{{ route('seller.my-products') }}" class="btn btn-primary mt-600 mt-4">Manage Products →</a>
</div>
@endsection