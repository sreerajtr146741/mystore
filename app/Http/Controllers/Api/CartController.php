<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get user's cart
     */
    public function index(Request $request)
    {
        $cartItems = Cart::where('user_id', $request->user()->id)
            ->with('product')
            ->get();

        $subtotal = $cartItems->sum(function($item) {
            return $item->product->final_price * $item->quantity;
        });

        // Apply any discounts if applicable
        $discount = 0;
        $total = $subtotal - $discount;

        return ApiResponse::success([
            'items' => $cartItems,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'count' => $cartItems->count()
        ]);
    }

    /**
     * Add item to cart
     */
    public function store(AddToCartRequest $request)
    {
        $product = Product::find($request->product_id);

        if ($product->stock < $request->quantity) {
            return ApiResponse::error('Insufficient stock', 400);
        }

        if ($product->status !== 'active') {
            return ApiResponse::error('Product is not available', 400);
        }

        $cartItem = Cart::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id
            ],
            ['quantity' => $request->quantity]
        );

        return ApiResponse::created(
            ['cart_item' => $cartItem->load('product')],
            'Product added to cart'
        );
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = Cart::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$cartItem) {
            return ApiResponse::notFound('Cart item not found');
        }

        $product = $cartItem->product;

        if ($product->stock < $request->quantity) {
            return ApiResponse::error('Insufficient stock', 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return ApiResponse::success(
            ['cart_item' => $cartItem->fresh()->load('product')],
            'Cart updated successfully'
        );
    }

    /**
     * Remove item from cart
     */
    public function destroy(Request $request, $id)
    {
        $cartItem = Cart::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$cartItem) {
            return ApiResponse::notFound('Cart item not found');
        }

        $cartItem->delete();

        return ApiResponse::success(null, 'Item removed from cart');
    }

    /**
     * Clear entire cart
     */
    public function clear(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();

        return ApiResponse::success(null, 'Cart cleared successfully');
    }
}
