<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class RedirectController extends Controller
{
    public function __invoke()
    {
        return match (auth()->user()->role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'seller' => redirect()->route('seller.dashboard'),
            default  => redirect()->route('products.index'), // buyer
        };
    }
}

