<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;

// GUEST ROUTES
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// AUTHENTICATED ROUTES
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [ProductController::class, 'index'])->name('home');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::resource('products', ProductController::class);

    // CART
    Route::post('/cart/add/{product}', [ProductController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove/{id}', [ProductController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/cart', fn() => view('cart.index'))->name('cart.index');

    // CHECKOUT
    Route::get('/checkout', [ProductController::class, 'checkout'])->name('checkout');
    Route::get('/checkout/{id}', [ProductController::class, 'checkoutSingle'])
        ->name('checkout.single')
        ->where('id', '[0-9]+');

    // PAYMENT (must be POST)
    Route::post('/checkout/pay', function () {
        session()->forget('cart');
        return redirect()->route('checkout.success')->with('paid', true);
    })->name('payment.process');

   

    // SUCCESS PAGE
    Route::get('/checkout/success', fn() => view('checkout.success'))->name('checkout.success');

    // PROFILE
    Route::get('/profile/edit', fn() => view('profile.edit'))->name('profile.edit');
    Route::put('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
});
Route::middleware(['web','auth'])->group(function () {
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])
        ->name('checkout.cancel');
});