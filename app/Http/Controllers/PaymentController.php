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
        
        // 1. Determine items source (Buy Now vs Cart)
        $chkItems = session('checkout_items');
        if (!empty($chkItems)) {
            $cart = $chkItems;
            $source = 'checkout_items';
        } else {
            $cart = session('cart', []);
            $source = 'cart';
        }
        
        \Log::info('PayNow Init: User ' . auth()->id() . ' | Item Count: ' . count($cart));
        
        if(empty($cart)) {
            \Log::warning('PayNow Redirect: Cart is empty for User ' . auth()->id());
            return redirect()->route('products.index')->with('error', 'Your cart is empty. Please add products before paying.');
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
            // "Buy Now" items structure might differ slightly (index vs product_id key)
            // But CheckoutController normalizes it. Here we have raw session data.
            // Cart: [product_id => [...], ...]
            // Checkout Items: [[id=>..., ...]] (Indexed array of arrays)
            
            $prodId = $details['id'] ?? $id; // Handle both structures
            
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $prodId,
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

        // Clear the used session data
        if ($source === 'checkout_items') {
            session()->forget('checkout_items');
        } else {
            session()->forget('cart');
        }
        session()->forget(['discount_percent','discount_amount','coupon_code','free_shipping']);

        // Return success view directly
        return view('payment.success')->with('message', 'Payment successful! Order #' . $order->id . ' placed.');
    }
}