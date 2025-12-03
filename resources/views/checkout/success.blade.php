@extends('layouts.app', ['title' => 'Order Success'])

@section('content')
<div class="container py-5 text-center">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <h1 class="mb-3">Payment Successful 🎉</h1>
    <p>We’ve emailed you a receipt.</p>
    <a class="btn btn-primary mt-3" href="{{ route('products.index') }}">Continue Shopping</a>
</div>
@endsection
