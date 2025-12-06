<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OtpService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Initiate checkout (send OTP)
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:credit_card,debit_card,upi,cod',
            'payment_details' => 'required|array',
        ]);

        $user = $request->user();
        $cartItems = Cart::where('user_id', $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        // Calculate total
        $total = $cartItems->sum(function($item) {
            return $item->product->final_price * $item->quantity;
        });

        // Store checkout data in session (for OTP verification)
        session([
            'api_checkout_data' => [
                'user_id' => $user->id,
                'payment_method' => $request->payment_method,
                'payment_details' => $request->payment_details,
                'total' => $total,
                'cart_items' => $cartItems->toArray()
            ]
        ]);

        // Send OTP
        OtpService::generateAndSend($user->email);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email for payment verification',
            'data' => [
                'total' => $total,
                'items_count' => $cartItems->count()
            ]
        ]);
    }

    /**
     * Verify payment OTP and complete order
     */
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $user = $request->user();

        if (!OtpService::verify($user->email, $request->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 401);
        }

        $checkoutData = session('api_checkout_data');

        if (!$checkoutData || $checkoutData['user_id'] != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Checkout session expired. Please start checkout again.'
            ], 400);
        }

        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'total' => $checkoutData['total'],
            'status' => 'completed',
            'payment_method' => $checkoutData['payment_method'],
            'payment_status' => 'paid',
        ]);

        // Create order items
        foreach ($checkoutData['cart_items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['product']['final_price'],
            ]);
        }

        // Clear cart
        Cart::where('user_id', $user->id)->delete();

        // Clear session
        session()->forget('api_checkout_data');

        return response()->json([
            'success' => true,
            'message' => 'Payment successful! Order placed.',
            'data' => ['order' => $order->load('items.product')]
        ], 201);
    }
}
