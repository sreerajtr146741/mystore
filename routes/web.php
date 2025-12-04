<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;

// Admin controllers
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\ProductManageController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('products.index')
        : redirect()->route('register');
})->name('root');

/*
|--------------------------------------------------------------------------
| PUBLIC CATALOG (no auth)
|--------------------------------------------------------------------------
*/
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

/*
|--------------------------------------------------------------------------
| CART + CHECKOUT (ALLOW GUESTS)
|--------------------------------------------------------------------------
*/
Route::get('/cart', fn () => view('cart.index'))->name('cart.index');
Route::post('/cart/add/{product}', [ProductController::class, 'addToCart'])->name('cart.add');
Route::delete('/cart/{id}', [ProductController::class, 'removeFromCart'])->name('cart.remove');

Route::get('/checkout/product/{id}', [ProductController::class, 'checkoutSingle'])->name('checkout.single');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::post('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon.apply');
Route::delete('/checkout/coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.coupon.remove');

/*
|--------------------------------------------------------------------------
| GUEST AUTH
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile/edit', fn () => view('profile.edit'))->name('profile.edit');
    Route::put('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');

    /*
    |--------------------------------------------------------------------------
    | ADMIN PANEL
    |--------------------------------------------------------------------------
    | If you use Spatie roles, change to ->middleware(['role:admin'])
    */
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Users (admin view)
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.status');
        Route::delete('/users/{user}',       [UserController::class, 'destroy'])->name('users.destroy');

        // Orders
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');

        // Global Discounts
        Route::get('/discounts/global',  [DiscountController::class, 'edit'])->name('discounts.global.edit');
        Route::post('/discounts/global', [DiscountController::class, 'update'])->name('discounts.global.update');

        // Products (ADMIN CRUD)
        Route::get   ('/products/manage',         [ProductManageController::class, 'index'])->name('products.manage');
        Route::get   ('/products/create',         [ProductManageController::class, 'create'])->name('products.create'); // used by Blade
        Route::post  ('/products',                [ProductManageController::class, 'store'])->name('products.store');
        Route::get   ('/products/{product}/edit', [ProductManageController::class, 'edit'])->name('products.edit');
        Route::put   ('/products/{product}',      [ProductManageController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}',      [ProductManageController::class, 'destroy'])->name('products.destroy');

        // Revenue
        Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue');
    });
});
