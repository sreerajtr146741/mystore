<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API Controllers
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ContactController;

// Admin API Controllers
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\UserManagementController;
use App\Http\Controllers\Api\Admin\ProductManagementController;
use App\Http\Controllers\Api\Admin\OrderManagementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| All routes automatically prefixed with /api
| All routes use 'api' middleware (rate limiting, etc.)
*/

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (No authentication required)
|--------------------------------------------------------------------------
*/

// Product Catalog
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// Contact
// Contact
Route::post('/contact', [ContactController::class, 'store']);

// Authentication (Public Shortcuts)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-login', [AuthController::class, 'verifyLoginOtp']);

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    // Registration Flow
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-register', [AuthController::class, 'verifyRegisterOtp']);
    Route::post('/resend-register-otp', [AuthController::class, 'resendRegisterOtp']);
    
    // Login Flow
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-login', [AuthController::class, 'verifyLoginOtp']);
    Route::post('/resend-login-otp', [AuthController::class, 'resendLoginOtp']);
    
    // Password Reset Flow
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/verify-password-otp', [AuthController::class, 'verifyPasswordOtp']);
    
    // Protected Auth Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });
});

/*
|--------------------------------------------------------------------------
| PROTECTED USER ROUTES (Require Sanctum Token)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    
    // User Profile
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::match(['put', 'post'], '/profile', [AuthController::class, 'updateProfile']); // Allow POST too
    // Route::post('/profile/photo', ...); // Redundant if above handles it, but keeping for safety
    
    // Cart Management
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);
    
    // Checkout & Payment
    Route::post('/checkout/session', [CheckoutController::class, 'createSession']);
    Route::post('/checkout/payment', [CheckoutController::class, 'initiatePayment']);
    Route::post('/checkout/verify-payment', [CheckoutController::class, 'verifyPaymentAndCreateOrder']);
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{id}/return', [OrderController::class, 'requestReturn']);
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Require Admin Role)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'admin.api'])->prefix('admin')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    
    // User Management
    Route::get('/users', [UserManagementController::class, 'index']);
    Route::get('/users/{id}', [UserManagementController::class, 'show']);
    Route::patch('/users/{id}/status', [UserManagementController::class, 'toggleStatus']);
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy']);
    
    // Product Management
    Route::get('/products', [ProductManagementController::class, 'index']);
    Route::get('/products/{id}', [ProductManagementController::class, 'show']);
    Route::post('/products', [ProductManagementController::class, 'store']);
    Route::put('/products/{id}', [ProductManagementController::class, 'update']);
    Route::patch('/products/{id}/toggle', [ProductManagementController::class, 'toggleStatus']);
    Route::delete('/products/{id}', [ProductManagementController::class, 'destroy']);
    
    // Order Management
    Route::get('/orders', [OrderManagementController::class, 'index']);
    Route::get('/orders/{id}', [OrderManagementController::class, 'show']);
    Route::patch('/orders/{id}/status', [OrderManagementController::class, 'updateStatus']);
});
