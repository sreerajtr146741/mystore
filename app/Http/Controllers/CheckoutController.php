<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    // Show checkout page
    public function index()
    {
        return view('checkout');
    }

    // Process checkout form
    public function process(Request $request)
    {
        $request->validate([
            'full_name' => 'required',
            'address'   => 'required',
            'phone'     => 'required',
            'pincode'   => 'required',
        ]);

        // (Optional) Save order or temporary checkout details
        // Order::create([...]);

        return redirect()->route('checkout.success');
    }

    // Success page
    public function success()
    {
        return view('checkout.success');
    }

    // Cancel payment
    public function cancel(Request $request)
    {
        // Cleanup if needed
        // session()->forget('checkout');

        return redirect()->route('products.index')
            ->with('status', 'Payment cancelled.');
    }
     // Continue order placing...
    public function placeOrder(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'phone' => 'required|string',
        'email' => 'required|email',
        'address' => 'required|string',
    ]);
}

}
