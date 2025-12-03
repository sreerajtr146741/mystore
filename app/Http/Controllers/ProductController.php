<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Simple Categories
    private array $categories = [
        'Mobile Phones', 'Laptops', 'Tablets', 'Smart Watches',
        'Headphones', 'Cameras', 'TVs', 'Gaming',
        'Fashion', 'Shoes', 'Bags', 'Watches',
        'Furniture', 'Home Decor', 'Kitchen',
        'Sports', 'Gym & Fitness',
        'Vehicles', 'Cars', 'Bikes', 'Accessories',
        'Fruits', 'Vegetables', 'Groceries',
        'Books', 'Toys', 'Other'
    ];

    public function index(Request $request)
    {
        try {
            $products = Product::where('user_id', Auth::id())
                ->when($request->search, function ($q) use ($request) {
                    // group name/description search together
                    $q->where(function ($qq) use ($request) {
                        $qq->where('name', 'like', "%{$request->search}%")
                           ->orWhere('description', 'like', "%{$request->search}%");
                    });
                })
                ->when($request->category, fn($q) => $q->where('category', $request->category))
                ->when($request->name, fn($q) => $q->where('name', 'like', "%{$request->name}%"))
                ->latest()
                ->get();

            return view('products.index', compact('products'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to load products: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('products.create', ['categories' => $this->categories]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to open create page: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name'        => 'required|string|max:255',
                'price'       => 'required|numeric|min:1',
                'category'    => 'required|in:' . implode(',', $this->categories),
                'description' => 'nullable|string',
                'image'       => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            ]);

            $path = $request->file('image')->store('products', 'public');

            Product::create([
                'user_id'     => Auth::id(),
                'name'        => $request->name,
                'price'       => $request->price,
                'category'    => $request->category,
                'description' => $request->description,
                'image'       => $path,
            ]);

            // optional: if you track this on users table
            optional(Auth::user())->increment('products_count');

            return redirect()->route('products.index')
                ->with('success', 'Product added successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to add product: ' . $e->getMessage());
        }
    }

    public function edit(Product $product)
    {
        try {
            $this->authorizeOwner($product);
            $categories = $this->categories;

            return view('products.edit', compact('product', 'categories'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to load edit page: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Product $product)
    {
        try {
            $this->authorizeOwner($product);

            $request->validate([
                'name'        => 'required|string|max:255',
                'price'       => 'required|numeric|min:1',
                'category'    => 'required|in:' . implode(',', $this->categories),
                'description' => 'nullable|string',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            ]);

            $data = $request->only(['name', 'price', 'category', 'description']);

            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update($data);

            return redirect()->route('products.index')->with('success', 'Product updated successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        try {
            $this->authorizeOwner($product);
            return view('products.show', compact('product'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to load product: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            $this->authorizeOwner($product);

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();

            optional(Auth::user())->decrement('products_count');

            return back()->with('success', 'Product deleted!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Deletion failed: ' . $e->getMessage());
        }
    }

    private function authorizeOwner(Product $product): void
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Add to Cart (Session)
     * Cart structure: [ productId => [id, name, price, image, qty, category, description] ]
     */
    public function addToCart(Product $product)
    {
        try {
            $cart = session('cart', []);

            if (isset($cart[$product->id])) {
                $cart[$product->id]['qty'] = (int)($cart[$product->id]['qty'] ?? 1) + 1;
            } else {
                $cart[$product->id] = [
                    'id'          => $product->id,
                    'name'        => $product->name,
                    'price'       => (float) $product->price,
                    'image'       => $product->image,
                    'qty'         => 1,
                    'category'    => $product->category,
                    'description' => $product->description,
                ];
            }

            session(['cart' => $cart]);

            return back()->with('success', "'{$product->name}' added to cart!");
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to add to cart: ' . $e->getMessage());
        }
    }

    public function removeFromCart($id)
    {
        try {
            $cart = session('cart', []);
            if (isset($cart[$id])) {
                unset($cart[$id]);
                session(['cart' => $cart]);
            }

            return back()->with('success', 'Item removed from cart!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to remove item: ' . $e->getMessage());
        }
    }

    /**
     * Buy Now: checkout a single product (qty=1) using the same checkout flow.
     * We temporarily set the session cart to just this item, then show checkout page.
     * (If you want to preserve the old cart, you can stash it under another key.)
     */
    public function checkoutSingle($id)
{
    try {
        $product = \App\Models\Product::findOrFail($id);

        $checkoutItems = [[
            'id'          => $product->id,
            'name'        => $product->name,
            'price'       => (float) $product->price,
            'qty'         => 1,
            'image'       => $product->image,
            'category'    => $product->category,
            'description' => $product->description,
        ]];

        // put into stash used by CheckoutController@index/process
        session(['checkout_items' => $checkoutItems]);

        // go to payment page
        return redirect()->route('checkout.index'); // <-- correct route name
    } catch (\Throwable $e) {
        return back()->with('error', 'Failed to load checkout: ' . $e->getMessage());
    }
}

    /**
     * Checkout full cart.
     */
    public function checkout()
    {
        try {
            $cart = session('cart', []);

            if (empty($cart)) {
                return redirect()->route('products.index')->with('error', 'Your cart is empty');
            }

            // Normalize items for the view; CheckoutController will recompute server-side totals again
            $items = collect($cart)->map(function ($item) {
                $qty = (int)($item['qty'] ?? 1);
                $price = (float)($item['price'] ?? 0);
                return array_merge($item, [
                    'qty'        => $qty,
                    'price'      => $price,
                    'line_total' => $qty * $price,
                ]);
            })->values()->all();

            return view('checkout.index', compact('items'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to load checkout page: ' . $e->getMessage());
        }
    }
}
