<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;

/*
|--------------------------------------------------------------------------
| GUEST ROUTES
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
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // HOME + PRODUCTS
    Route::get('/', [ProductController::class, 'index'])->name('home');
    Route::resource('products', ProductController::class);

    // CART
    Route::post('/cart/add/{product}', [ProductController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove/{id}', [ProductController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/cart', fn() => view('cart.index'))->name('cart.index');

    /*
    |--------------------------------------------------------------------------
    | CHECKOUT ROUTES
    |--------------------------------------------------------------------------
    */

    // Show checkout page (cart checkout)
    Route::get('/checkout', [ProductController::class, 'checkout'])
        ->name('checkout');

    // Single product checkout
    Route::get('/checkout/product/{id}', [ProductController::class, 'checkoutSingle'])
        ->name('checkout.single')
        ->where('id', '[0-9]+');

    // PROCESS PAYMENT (must be POST)
    Route::post('/checkout/pay', [CheckoutController::class, 'pay'])
        ->name('payment.process');

    // SUCCESS PAGE
    Route::get('/checkout/success', [CheckoutController::class, 'success'])
        ->name('checkout.success');

    // CANCEL PAGE
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])
        ->name('checkout.cancel');

    // PROFILE SETTINGS
    Route::get('/profile/edit', fn() => view('profile.edit'))->name('profile.edit');
    Route::put('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
});
