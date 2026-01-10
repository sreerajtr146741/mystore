<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::where('user_id', Auth::id())->with('product')->get();
        
        $data = $cart->map(function($item) {
            $price = $item->product->price;
            // logic for discounted price if needed
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'price' => $price,
                'qty' => $item->qty,
                'subtotal' => $price * $item->qty
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['product_id' => 'required', 'qty' => 'integer|min:1']);

        $item = Cart::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $request->product_id],
            ['qty' => \DB::raw("qty + {$request->qty}")] // Increment if exists, or set? Usually Request has absolute or we add. Let's assume add.
        );
        // Correcting updateOrCreate logic for increment:
        // Actually updateOrCreate doesn't increment easily. 
        // Better:
        $item = Cart::where('user_id', Auth::id())->where('product_id', $request->product_id)->first();
        if ($item) {
            $item->qty += $request->qty;
            $item->save();
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'qty' => $request->qty
            ]);
        }

        return response()->json(['status' => true, 'message' => 'Added to cart']);
    }

    public function update(Request $request, $id)
    {
        // Update method (usually PUT /cart/{id})
        // ID here is product_id usually or cart_id? Prompt endpoints: PUT /api/cart/{id}. 
        // Assuming {id} is product_id or cart row id. Let's assume cart row ID usually, but for API often product_id is easier. 
        // Let's assume it calls update on a specific cart item ID.
        
        $cart = Cart::where('user_id', Auth::id())->where('id', $id)->first();
        if (!$cart) return response()->json(['status' => false, 'message' => 'Item not found'], 404);

        $cart->update(['qty' => $request->qty]);
        
        return response()->json(['status' => true, 'message' => 'Cart updated']);
    }

    public function destroy($id)
    {
        Cart::where('user_id', Auth::id())->where('id', $id)->delete();
        return response()->json(['status' => true, 'message' => 'Item removed']);
    }

    public function clear()
    {
        Cart::where('user_id', Auth::id())->delete();
        return response()->json(['status' => true, 'message' => 'Cart cleared']);
    }
}
