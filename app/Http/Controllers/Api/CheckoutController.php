<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OtpService;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CheckoutController extends Controller
{
    /**
     * Create checkout session
     */
    public function createSession(Request $request)
    {
        $cartItems = Cart::where('user_id', $request->user()->id)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return ApiResponse::error('Cart is empty', 400);
        }

        // Validate stock availability
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return ApiResponse::error(
                    "Insufficient stock for {$item->product->name}",
                    400
                );
            }
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->product->final_price * $item->quantity;
        });

        $discount = 0; // Calculate any applicable discounts
        $tax = $subtotal * 0; // Add tax if needed
        $shipping = 0; // Calculate shipping if needed
        $total = $subtotal - $discount + $tax + $shipping;

        return ApiResponse::success([
            'items' => $cartItems,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total
        ]);
    }

    /**
     * Process checkout and send payment OTP
     */
    public function initiatePayment(CheckoutRequest $request)
    {
        $cartItems = Cart::where('user_id', $request->user()->id)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return ApiResponse::error('Cart is empty', 400);
        }

        // Calculate total
        $total = $cartItems->sum(function($item) {
            return $item->product->final_price * $item->quantity;
        });

        // Store checkout data in cache for later processing (15 min expiry)
        Cache::put("checkout_{$request->user()->id}", [
            'user_id' => $request->user()->id,
            'shipping_address' => $request->shipping_address,
            'payment_method' => $request->payment_method,
            'total' => $total,
            'items' => $cartItems->map(function($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->final_price
                ];
            })
        ], now()->addMinutes(15));

        // Send payment OTP
        OtpService::generateAndSend($request->user()->email);

        return ApiResponse::success(
            ['email' => $request->user()->email],
            'Payment OTP sent to your email'
        );
    }

    /**
     * Verify payment OTP and create order
     */
    public function verifyPaymentAndCreateOrder(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $user = $request->user();
        
        if (!OtpService::verify($user->email, $request->otp)) {
            return ApiResponse::unauthorized('Invalid or expired OTP');
        }

        $checkoutData = Cache::get("checkout_{$user->id}");

        if (!$checkoutData) {
            return ApiResponse::error('Checkout session expired. Please initiate payment again.', 400);
        }

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'total' => $checkoutData['total'],
                'status' => 'placed',
                'shipping_address' => $checkoutData['shipping_address'],
                'payment_method' => $checkoutData['payment_method'],
                'payment_status' => $checkoutData['payment_method'] === 'cod' ? 'pending' : 'paid'
            ]);

            // Create order items and update product stock
            foreach ($checkoutData['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['quantity'],
                    'price' => $item['price']
                ]);

                // Decrement product stock
                Product::find($item['product_id'])->decrement('stock', $item['quantity']);
            }

            // Clear cart
            Cart::where('user_id', $user->id)->delete();

            // Clear checkout cache
            Cache::forget("checkout_{$user->id}");

            DB::commit();

            return ApiResponse::created(
                ['order' => $order->load('items.product')],
                'Order placed successfully'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('Failed to create order: ' . $e->getMessage(), 500);
        }
    }
}
