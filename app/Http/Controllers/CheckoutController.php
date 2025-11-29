<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function cancel(Request $request)
    {
        // Clean up any pending checkout state (keep this idempotent)
        // session()->forget('checkout');

        return redirect()->route('products.index')
            ->with('status', 'Payment cancelled.');
    }
}
