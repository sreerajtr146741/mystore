<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;


//SMART ROOT


Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('products.index')
        : redirect()->route('login');
})->name('root');


 //GUEST ROUTES

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});


// AUTH ROUTES


Route::middleware('auth')->group(function () {

    // LOGOUT 
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // PRODUCTS
    Route::resource('products', ProductController::class)->parameters([
        'products' => 'product'
    ]);

    // CART
    Route::post('/cart/add/{product}', [ProductController::class, 'addToCart'])
        ->whereNumber('product')
        ->name('cart.add');

    Route::post('/cart/remove/{id}', [ProductController::class, 'removeFromCart'])
        ->whereNumber('id')
        ->name('cart.remove');

    Route::get('/cart', fn () => view('cart.index'))->name('cart.index');

    
    // CHECKOUT ROUTES
  
    // Show checkout (cart)
    Route::get('/checkout', [ProductController::class, 'checkout'])->name('checkout');

    // Single product checkout
    Route::get('/checkout/product/{id}', [ProductController::class, 'checkoutSingle'])
        ->whereNumber('id')
        ->name('checkout.single');

    // PROCESS PAYMENT 
    Route::post('/checkout/process', [CheckoutController::class, 'pay'])
        ->name('checkout.process');

    // SUCCESS & CANCEL
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel',  [CheckoutController::class, 'cancel'])->name('checkout.cancel');

    // PROFILE
    Route::get('/profile/edit', fn () => view('profile.edit'))->name('profile.edit');
    Route::put('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
});

//OPTIONAL: Fallback to root


// Route::fallback(function () {
//     return redirect()->route('root');
// });
