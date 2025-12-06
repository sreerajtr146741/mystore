@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white/30 backdrop-blur-md p-10 rounded-2xl shadow-xl border border-white/20">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Verify Payment
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                We sent a code to {{ $email ?? 'your email' }}
            </p>
        </div>
        
        @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ $errors->first() }}</span>
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('verify.payment.otp') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $email ?? auth()->user()->email }}">
            
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="otp" class="sr-only">OTP Code</label>
                    <input id="otp" name="otp" type="text" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-4 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-lg text-center tracking-wider font-bold text-2xl" 
                           placeholder="Enter 6-digit Code" maxlength="6" pattern="\d{6}" title="Please enter exactly 6 digits">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform hover:scale-105 transition-all duration-200 shadow-lg">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-lock text-indigo-200"></i>
                    </span>
                    Verify & Pay
                </button>
            </div>
        </form>

        <div class="mt-4 text-center">
            <form action="{{ route('payment.otp.resend') }}" method="POST">
                @csrf
                <p class="text-sm text-gray-600">
                    Didn't receive the code? 
                    <button type="submit" class="font-medium text-indigo-600 hover:text-indigo-500 underline bg-transparent border-0 p-0 cursor-pointer">
                        Resend OTP
                    </button>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
