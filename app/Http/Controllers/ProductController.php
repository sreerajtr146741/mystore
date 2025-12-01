<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Simple & Clean Categories
    private $categories = [
        'Mobile Phones', 'Laptops', 'Tablets', 'Smart Watches',
        'Headphones', 'Cameras', 'TVs', 'Gaming',
        'Fashion', 'Shoes', 'Bags', 'Watches',
        'Furniture', 'Home Decor', 'Kitchen',
        'Sports', 'Gym & Fitness',
        'Vehicles', 'Cars','Bikes','Accessories',
        'Fruits', 'Vegetables', 'Groceries',
        'Books', 'Toys', 'Other'
    ];

    public function index(Request $request)
    {
        try {
            $products = Product::where('user_id', Auth::id())
                ->when($request->search, function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                        ->orWhere('description', 'like', "%{$request->search}%");
                })
                ->when($request->category, fn($q) => $q->where('category', $request->category))
                ->when($request->name, fn($q) => $q->where('name', 'like', "%{$request->name}%"))
                ->latest()
                ->get();

            return view('products.index', compact('products'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load products: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('products.create', ['categories' => $this->categories]);
        } catch (\Exception $e) {
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

            Auth::user()->increment('products_count');

            return redirect()->route('products.index')
                ->with('success', 'Product added successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add product: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Product $product)
    {
        try {
            if ($product->user_id !== Auth::id()) {
                abort(403);
            }

            $request->validate([
                'name'        => 'required|string|max:255',
                'price'       => 'required|numeric|min:1',
                'category'    => 'required|string',
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
        } catch (\Exception $e) {
            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            $this->authorizeUser($product);

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();

            Auth::user()->decrement('products_count');

            return back()->with('success', 'Product deleted!');
        } catch (\Exception $e) {
            return back()->with('error', 'Deletion failed: ' . $e->getMessage());
        }
    }

    private function authorizeUser($product)
    {
        if ($product->user_id !== Auth::id()) abort(403);
    }

    // Add to Cart (Simple Session Based)
    public function addToCart(Product $product)
    {
        try {
            $cart = session('cart', []);

            if (isset($cart[$product->id])) {
                $cart[$product->id]['quantity'] = ($cart[$product->id]['quantity'] ?? 1) + 1;
            } else {
                $cart[$product->id] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image,
                    'quantity' => 1,
                ];
            }

            session(['cart' => $cart]);

            return back()->with('success', " '{$product->name}' added to cart!");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add to cart: ' . $e->getMessage());
        }
    }

    public function removeFromCart($id)
    {
        try {
            $cart = session('cart', []);
            unset($cart[$id]);
            session(['cart' => $cart]);

            return back()->with('success', 'Item removed from cart!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove item: ' . $e->getMessage());
        }
    }

    public function edit(Product $product)
    {
        try {
            if ($product->user_id !== Auth::id()) {
                abort(403);
            }

            $categories = ['Mobile Phones','Laptops','Fashion','Sports','Fruits','Bikes','Furniture','Other'];

            return view('products.edit', compact('product', 'categories'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load edit page: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        try {
            if ($product->user_id !== Auth::id()) {
                abort(403, 'This is not your product!');
            }

            return view('products.show', compact('product'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load product: ' . $e->getMessage());
        }
    }

    public function checkoutSingle($id)
    {
        try {
            $product = Product::findOrFail($id);

            $item = [
                'id'    => $product->id,
                'name'  => $product->name,
                'price' => $product->price,
                'image' => $product->image,
            ];

            $cart = session('cart', []);
            unset($cart[$id]);
            session(['cart' => $cart]);

            return view('checkout.index', ['items' => [$item]]);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load checkout: ' . $e->getMessage());
        }
    }

    public function checkout()
    {
        try {
            $cart = session('cart', []);

            if (empty($cart)) {
                return redirect()->route('products.index')->with('error', 'Your cart is empty');
            }

            $items = [];
            foreach ($cart as $id => $item) {
                $items[] = [
                    'id'    => $id,
                    'name'  => $item['name'],
                    'price' => $item['price'],
                    'image' => $item['image'] ?? null,
                ];
            }

            return view('checkout.index', compact('items'));

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load checkout page: ' . $e->getMessage());
        }
    }
}
