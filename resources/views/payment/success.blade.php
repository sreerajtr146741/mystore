@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg w-full text-center space-y-8 bg-white p-10 rounded-2xl shadow-xl">
        <div class="flex justify-center">
            <div class="rounded-full bg-green-100 p-6">
                <i class="fas fa-check-circle text-6xl text-green-600"></i>
            </div>
        </div>
        
        <h2 class="text-3xl font-extrabold text-gray-900">
            Payment Successful!
        </h2>
        
        <p class="text-xl text-gray-600">
            {{ $message ?? 'Thank you for your purchase.' }}
        </p>

        <div class="mt-8">
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg hover:shadow-lg transition-all duration-300">
                Continue Shopping
            </a>
            <br>
            <a href="{{ route('orders.index') }}" class="inline-block mt-4 text-indigo-600 hover:text-indigo-500">
                View Orders
            </a>
        </div>
    </div>
</div>
@endsection
