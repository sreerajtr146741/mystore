<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\DashboardController as UserDashboardController;

// Admin Controllers
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\UserManagementController;
use App\Http\Controllers\Api\Admin\ProductManagementController;
use App\Http\Controllers\Api\Admin\OrderManagementController;
use App\Http\Controllers\Api\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Api\Admin\RevenueController;
use App\Http\Controllers\Api\Admin\SellerApplicationController;
use App\Http\Controllers\Api\Admin\CategoryDiscountController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- AUTHENTICATION ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-register', [AuthController::class, 'verifyRegisterOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-login', [AuthController::class, 'verifyLoginOtp']);
Route::post('/resend-register-otp', [AuthController::class, 'resendRegisterOtp']);
Route::post('/resend-login-otp', [AuthController::class, 'resendLoginOtp']);

// --- PUBLIC ---
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/contact', [ContactController::class, 'store']);

// --- PROTECTED (User) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // User Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index']); // GET /api/dashboard

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::match(['put', 'patch'], '/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{id}/return', [OrderController::class, 'requestReturn']);

    // Checkout
    Route::post('/checkout/session', [CheckoutController::class, 'createSession']);
    Route::post('/checkout/payment', [CheckoutController::class, 'initiatePayment']);
    
    // --- ADMIN PANEL ---
    Route::middleware('is_admin')->prefix('admin')->group(function () {
        
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);
        
        // Users
        Route::get('/users', [UserManagementController::class, 'index']);
        Route::get('/users/{id}', [UserManagementController::class, 'show']);
        Route::patch('/users/{id}/status', [UserManagementController::class, 'toggleStatus']);
        Route::delete('/users/{id}', [UserManagementController::class, 'destroy']);
        
        // Products
        Route::get('/products', [ProductManagementController::class, 'index']);
        Route::post('/products', [ProductManagementController::class, 'store']);
        Route::put('/products/{id}', [ProductManagementController::class, 'update']);
        Route::delete('/products/{id}', [ProductManagementController::class, 'destroy']);
        
        // Orders
        Route::get('/orders', [OrderManagementController::class, 'index']);
        Route::get('/orders/{id}', [OrderManagementController::class, 'show']);
        Route::patch('/orders/{id}/status', [OrderManagementController::class, 'updateStatus']);
        
        // Revenue
        Route::get('/revenue', [RevenueController::class, 'index']);

        // Messages
        Route::get('/messages', [AdminContactMessageController::class, 'index']);
        Route::post('/messages/{id}/reply', [AdminContactMessageController::class, 'reply']);
        Route::delete('/messages/{message}', [AdminContactMessageController::class, 'destroy']);

        // Seller Applications
        Route::get('/seller-applications', [SellerApplicationController::class, 'index']);
        Route::post('/seller-applications/{id}/approve', [SellerApplicationController::class, 'approve']);
        Route::post('/seller-applications/{id}/reject', [SellerApplicationController::class, 'reject']);

        // Category Discounts
        Route::post('/category-discount', [CategoryDiscountController::class, 'store']);
    });
});
