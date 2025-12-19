<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CartController;        // ← added
use App\Http\Controllers\PaymentController;     // ← added
use App\Http\Controllers\AdminController;       // ← added

// Admin controllers (already exist)
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\ProductManageController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\MailTestController;

/*
|--------------------------------------------------------------------------
| ROOT → Always show products first (even for guests)
|--------------------------------------------------------------------------
*/
Route::get('/', [ProductController::class, 'index'])->name('products.index');

/*
|--------------------------------------------------------------------------
| PUBLIC CATALOG (no auth required)
|--------------------------------------------------------------------------
*/
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');

/*
|--------------------------------------------------------------------------
| CART (guest can view, but add/buy/checkout requires login + OTP)
|--------------------------------------------------------------------------
*/
Route::get('/cart', fn () => view('cart.index'))->name('cart.index');

// Add to cart → force login if not authenticated
Route::post('/cart/add/{product}', [ProductController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/decrement/{id}', [ProductController::class, 'decrementCart'])->name('cart.decrement');

Route::delete('/cart/{id}', [ProductController::class, 'removeFromCart'])->name('cart.remove');

/*
|--------------------------------------------------------------------------
| CHECKOUT & PAYMENT (require auth + OTP)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/checkout/product/{id}', [ProductController::class, 'checkoutSingle'])
        ->name('checkout.single');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');

    // Pay Now → Send OTP
    // Pay Now
    Route::post('/pay-now', [PaymentController::class, 'payNow'])->name('pay.now');

    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
    Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon.apply');
    Route::delete('/checkout/coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.coupon.remove');
});

/*
|--------------------------------------------------------------------------
| GUEST AUTH (Login / Register with OTP)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// OTP Verification (common for login & register)
// routes/web.php
Route::get('/verify-otp/register', function () {
    $userId = session('pending_registration_user_id');
    $email = $userId ? \App\Models\User::find($userId)?->email : null;
    return view('auth.verify-register-otp', ['email' => $email]);
})->name('verify.register.otp');

Route::post('/verify-otp/register', [AuthController::class, 'verifyRegisterOtp'])
     ->name('verify.register.otp.post');

// Resend Registration OTP
Route::post('/register/otp/resend', [AuthController::class, 'resendRegisterOtp'])
     ->name('register.otp.resend');

// Login OTP Verification
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetOtp'])->name('password.email');
Route::post('/password/verify-otp', [AuthController::class, 'verifyPasswordResetOtp'])->name('password.verify.otp');
Route::post('/password/resend-otp', [AuthController::class, 'resendPasswordResetOtp'])->name('password.resend.otp');
Route::post('/password/reset', [AuthController::class, 'updatePassword'])->name('password.update');
/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile/edit', fn () => view('profile.edit'))->name('profile.edit');
    Route::put('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Special Admin Login from Profile Dropdown (admin@store.com / admin123)
    Route::get('/admin-login', fn() => view('auth.admin-login'))->name('admin.login.form');
    Route::post('/admin-login', [AdminController::class, 'adminLogin'])->name('admin.login.submit');

    // My Orders (User)
    
    // Orders (User)
    Route::resource('orders', \App\Http\Controllers\OrderController::class)->only(['index', 'show']);
    Route::get('/orders/{id}/download', [\App\Http\Controllers\OrderController::class, 'downloadInvoice'])->name('orders.download');
    Route::post('/orders/{id}/cancel', [\App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{id}/return', [\App\Http\Controllers\OrderController::class, 'requestReturn'])->name('orders.return');
});

/*
|--------------------------------------------------------------------------
| ADMIN PANEL (kept exactly as you have + added category discount route)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users Management
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show'); // Added show route
    Route::patch('/users/{id}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/download', [OrderController::class, 'downloadInvoice'])->name('orders.download');
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.update_status');

    // Global Discounts
    Route::get('/discounts/global', [DiscountController::class, 'edit'])->name('discounts.global.edit');
    Route::post('/discounts/global', [DiscountController::class, 'update'])->name('discounts.global.update');

    // Products CRUD (admin)
    Route::get('/products/manage', function() { return redirect()->route('admin.products.list'); });
    Route::get('/products', [ProductManageController::class, 'index'])->name('products.list');
    Route::get('/products/create', [ProductManageController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductManageController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductManageController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductManageController::class, 'update'])->name('products.update');
    Route::post('/products/{product}/banner', [ProductManageController::class, 'updateBanner'])->name('products.banner.update');
    Route::delete('/products/banner/{productBanner}', [ProductManageController::class, 'destroyBanner'])->name('products.banner.destroy');
    Route::patch('/products/{product}/toggle', [ProductManageController::class, 'toggleStatus'])->name('products.toggle');
    Route::delete('/products/{product}', [ProductManageController::class, 'destroy'])->name('products.destroy');

    // Revenue
    Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue');

    // ← NEW: Category-based discount (BMW example)
    Route::post('/set-category-discount', [AdminController::class, 'setCategoryDiscount'])
        ->name('discount.category');
});
Route::get('/test-mail', [MailTestController::class, 'send']);
