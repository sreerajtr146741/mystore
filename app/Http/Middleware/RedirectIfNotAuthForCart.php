<?php
// Create with php artisan make:middleware
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAuthForCart {
    public function handle(Request $request, Closure $next) {
        if ($request->isMethod('post') && !Auth::check()) { // For add/buy
            return redirect()->route('login');
        }
        return $next($request);
    }
}