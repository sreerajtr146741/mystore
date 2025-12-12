<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;

class PaymentController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }

    public function checkout() {
        return view('checkout.index');
    }

    public function payNow(Request $request) {
        // Validation
        // Store selected method for the final order creation
        session(['payment_method' => $request->payment_method ?? 'cod']);
        
        $cart = session('cart', []);
        
        if(empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Cart is empty');
        }

        $total = 0;
        foreach($cart as $details) {
            $total += $details['price'] * $details['qty'];
        }
        
        // Create Order Immediately
        $order = Order::create([
            'user_id' => auth()->id(),
            'total'   => $total,
            'status'  => 'placed',
            'payment_method' => session('payment_method', 'cod'),
            'payment_status' => 'paid',
            'shipping_address' => auth()->user()->address ?? 'Default Address',
            'delivery_date' => now()->addDays(5),
        ]);

        foreach($cart as $id => $details) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $id, // $id is product_id from session key
                'qty'        => $details['qty'],
                'price'      => $details['price'],
            ]);
        }
        
        // Send Email
        try {
            \Illuminate\Support\Facades\Mail::to(auth()->user()->email)->send(new \App\Mail\OrderStatusUpdated($order));
        } catch(\Exception $e) {
            \Log::error('Order placed email failed: '.$e->getMessage());
        }

        // Return success view directly
        return view('payment.success')->with('message', 'Payment successful! Order #' . $order->id . ' placed.');
    }
}