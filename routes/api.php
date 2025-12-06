<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-register-otp', [AuthController::class, 'verifyRegisterOtp']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-login-otp', [AuthController::class, 'verifyLoginOtp']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/verify-password-otp', [AuthController::class, 'verifyPasswordOtp']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('auth:sanctum');
});

// Public Product Routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Protected Routes (Require Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    
    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    
    // Payment & Checkout
    Route::post('/checkout', [PaymentController::class, 'checkout']);
    Route::post('/payment/verify-otp', [PaymentController::class, 'verifyOtp']);
});
