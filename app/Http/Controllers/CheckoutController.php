<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    // Show checkout page
    public function index()
    {
        try {
            return view('checkout');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load checkout page: ' . $e->getMessage());
        }
    }

    // Process checkout form
    public function process(Request $request)
    {
        try {
            $request->validate([
                'full_name' => 'required',
                'address'   => 'required',
                'phone'     => 'required',
                'pincode'   => 'required',
            ]);

            // (Optional) Save order or temporary checkout details
            // Order::create([...]);

            return redirect()->route('checkout.success');

        } catch (\Exception $e) {
            return back()->with('error', 'Checkout failed: ' . $e->getMessage());
        }
    }

    // Success page
    public function success()
    {
        try {
            return view('checkout.success');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load success page: ' . $e->getMessage());
        }
    }

    // Cancel payment
    public function cancel(Request $request)
    {
        try {
            // Cleanup if needed
            // session()->forget('checkout');

            return redirect()->route('products.index')
                ->with('status', 'Payment cancelled.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel payment: ' . $e->getMessage());
        }
    }

    // Continue order placing...
    public function placeOrder(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'phone' => 'required|string',
                'email' => 'required|email',
                'address' => 'required|string',
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Order placement failed: ' . $e->getMessage());
        }
    }
}
