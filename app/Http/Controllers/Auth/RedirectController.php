<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class RedirectController extends Controller
{
    public function __invoke()
    {
        try {

            return match (auth()->user()->role) {
                'admin'  => redirect()->route('admin.dashboard'),
                'seller' => redirect()->route('seller.dashboard'),
                default  => redirect()->route('products.index'), // buyer
            };

        } catch (\Throwable $e) {

            \Log::error('RedirectController error: '.$e->getMessage());

            // Safe fallback redirect
            return redirect()->route('products.index')
                ->with('error', 'Redirect failed. You were sent to home.');
        }
    }
}
