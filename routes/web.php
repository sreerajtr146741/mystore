<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Auth\RedirectController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

// --------------------
// ROOT
// --------------------
Route::get('/', fn () => auth()->check() ? redirect()->route('home') : redirect()->route('login'))
    ->name('root');

// --------------------
// GUEST ROUTES
// --------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// --------------------
// AUTHENTICATED ROUTES
// --------------------
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // After login redirect
    Route::get('/home', RedirectController::class)->name('home');

    // ------------------------
    // BUYER PRODUCT ROUTES
    // ------------------------
    Route::resource('products', ProductController::class);

    // ------------------------
    // CART ROUTES
    // ------------------------
    Route::post('/cart/add/{product}', [ProductController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove/{id}', [ProductController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/cart', fn () => view('cart.index'))->name('cart.index');

    // ------------------------
    // CHECKOUT ROUTES
    // ------------------------
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/product/{id}', [ProductController::class, 'checkoutSingle'])->name('checkout.single');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

    // ------------------------
    // PROFILE
    // ------------------------
    Route::get('/profile/edit', fn () => view('profile.edit'))->name('profile.edit');
    Route::put('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');

    // ------------------------
    // SELLER PORTAL
    // ------------------------
    Route::prefix('seller')->name('seller.')->middleware('seller')->group(function () {
        Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', ProductController::class)->except(['index', 'show']);
    });

    // Optional seller product list
    Route::get('/my-products', [ProductController::class, 'index'])->name('my-products');

    // ------------------------
    // ADMIN PANEL
    // ------------------------
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // User management
        Route::get('/users', [UserManagementController::class, 'index'])->name('users');
        Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.updateRole');

        // Seller Applications
        Route::get('/applications', [\App\Http\Controllers\Admin\SellerApplicationController::class, 'index'])->name('applications');
        Route::patch('/applications/{application}', [\App\Http\Controllers\Admin\SellerApplicationController::class, 'approve'])->name('applications.approve');
    });
});
