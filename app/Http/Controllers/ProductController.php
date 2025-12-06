<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Master categories list (shown in forms)
     */
    private array $categories = [
        'Mobile Phones','Laptops','Tablets','Smart Watches',
        'Headphones','Cameras','TVs','Gaming',
        'Fashion','Shoes','Bags','Watches',
        'Furniture','Home Decor','Kitchen',
        'Sports','Gym & Fitness',
        'Vehicles','Cars','Bikes','Accessories',
        'Fruits','Vegetables','Groceries',
        'Books','Toys','Other'
    ];

    /* --------------------------------------------------------------------
     | PUBLIC CATALOG
     |---------------------------------------------------------------------*/
    public function index(Request $request)
    {
        try {
            $category = $request->query('category');
            $query = Product::query();

            if (Schema::hasColumn('products', 'is_active')) {
                $query->where('is_active', true);
            } elseif (Schema::hasColumn('products', 'status')) {
                $query->where('status', 'active');
            }

            if ($category) {
                $query->where('category', $category);
            }

            $products = $query->latest()->paginate(12);

            // Add discounted price to each product for display
            foreach ($products as $product) {
                $product->final_price = $this->calculateFinalPrice($product);
            }

            return view('products.index', compact('products'));
        } catch (\Throwable $e) {
            \Log::error('Product index error: '.$e->getMessage());
            return back()->with('error', 'Unable to load products.');
        }
    }

    public function show(Product $product)
    {
        try {
            if (Schema::hasColumn('products', 'is_active')) {
                abort_unless((bool)($product->is_active ?? false), 404);
            } elseif (Schema::hasColumn('products', 'status')) {
                abort_unless(($product->status ?? '') === 'active', 404);
            }

            $product->final_price = $this->calculateFinalPrice($product);

            return view('products.show', compact('product'));
        } catch (\Throwable $e) {
            \Log::error('Product show error: '.$e->getMessage());
            return back()->with('error', 'Unable to load product.');
        }
    }

    /* --------------------------------------------------------------------
     | HELPER: Calculate final price with discount
     |---------------------------------------------------------------------*/
    private function calculateFinalPrice($product)
    {
        $price = (float) $product->price;

        if (!empty($product->discount_value) && !empty($product->discount_type)) {
            if ($product->discount_type === 'percent') {
                $discount = $price * ($product->discount_value / 100);
            } else { // flat
                $discount = (float) $product->discount_value;
            }
            return round($price - $discount, 2);
        }

        return $price;
    }

    /* --------------------------------------------------------------------
     | CART + CHECKOUT (GUEST CAN SEE CART, BUT ADD REQUIRES LOGIN)
     |---------------------------------------------------------------------*/
    public function addToCart(Request $request, Product $product)
    {
        // FORCE LOGIN IF NOT AUTHENTICATED
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('warning', 'Please login to add items to your cart.')
                ->with('intended', route('products.show', $product));
        }

        try {
            $qty = max(1, (int) $request->input('qty', 1));
            $cart = session('cart', []);

            // Use final discounted price in cart
            $finalPrice = $this->calculateFinalPrice($product);

            if (isset($cart[$product->id])) {
                $cart[$product->id]['qty'] += $qty;
            } else {
                $cart[$product->id] = [
                    'name'        => $product->name,
                    'price'       => $finalPrice,           // discounted price
                    'original_price' => (float) $product->price, // for display
                    'qty'         => $qty,
                    'image'       => $product->image,
                    'category'    => $product->category,
                    'description' => $product->description,
                ];
            }

            session(['cart' => $cart]);
            $totalQty = collect($cart)->sum('qty');

            return redirect()->route('cart.index')
                ->with('success', "{$product->name} added to cart. ({$qty} added, {$totalQty} total items)");
        } catch (\Throwable $e) {
            \Log::error('Add to cart error: '.$e->getMessage());
            return back()->with('error', 'Unable to add item to cart.');
        }
    }

    public function decrementCart(Request $request, $id)
    {
        try {
            $cart = session('cart', []);
            if (isset($cart[$id])) {
                if ($cart[$id]['qty'] > 1) {
                    $cart[$id]['qty']--;
                    session(['cart' => $cart]);
                } else {
                    unset($cart[$id]);
                    session(['cart' => $cart]);
                }
            }
            return back()->with('success', 'Cart updated.');
        } catch (\Throwable $e) {
            \Log::error('Decrement cart error: '.$e->getMessage());
            return back()->with('error', 'Unable to update cart.');
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
            \Log::error('Remove cart error: '.$e->getMessage());
            return back()->with('error', 'Unable to remove item from cart.');
        }
    }

    public function checkoutSingle(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('intended', route('checkout.single', $id));
        }

        try {
            $product = Product::findOrFail($id);
            $qty = max(1, (int) $request->query('qty', 1));
            $finalPrice = $this->calculateFinalPrice($product);

            session(['checkout_items' => [[
                'id'             => $product->id,
                'name'           => $product->name,
                'price'          => $finalPrice,
                'original_price' => (float) $product->price,
                'qty'            => $qty,
                'image'          => $product->image,
                'category'       => $product->category,
                'description'    => $product->description,
            ]]]);

            return redirect()->route('checkout.index');
        } catch (\Throwable $e) {
            \Log::error('Checkout single error: '.$e->getMessage());
            return back()->with('error', 'Unable to open checkout.');
        }
    }

    public function checkout()
    {
        try {
            $cart = session('cart', []);
            if (empty($cart)) {
                return redirect()->route('products.index')->with('error', 'Your cart is empty');
            }

            $items = collect($cart)->map(function ($item) {
                $qty   = (int)($item['qty'] ?? 1);
                $price = (float)($item['price'] ?? 0);
                return array_merge($item, [
                    'qty'        => $qty,
                    'price'      => $price,
                    'line_total' => $qty * $price,
                ]);
            })->values()->all();

            return view('checkout.index', compact('items'));
        } catch (\Throwable $e) {
            \Log::error('Checkout error: '.$e->getMessage());
            return back()->with('error', 'Unable to load checkout.');
        }
    }

    /* --------------------------------------------------------------------
     | ADMIN PRODUCT MANAGEMENT (100% UNCHANGED – your original code below)
     |---------------------------------------------------------------------*/
    public function adminManage(Request $request)
    {
        try {
            $q = $request->input('q');
            $query = Product::query()->with('user:id,name');
            if ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('sku', 'like', "%{$q}%")
                      ->orWhere('id', $q);
                });
            }
            $products   = $query->latest()->paginate(12);
            $categories = $this->categories;
            return view('admin.products.index', compact('products', 'categories', 'q'));
        } catch (\Throwable $e) {
            \Log::error('Admin manage error: '.$e->getMessage());
            return back()->with('error', 'Unable to load admin products.');
        }
    }

    // ... ALL YOUR ADMIN METHODS BELOW REMAIN 100% UNCHANGED ...
    // adminCreate, adminStore, adminEdit, adminUpdate, adminDestroy
    // (I didn't paste them again to save space — they are exactly as you wrote)

    public function adminCreate()
    {
        try {
            $categories = $this->categories;
            return view('admin.products.create', compact('categories'));
        } catch (\Throwable $e) {
            \Log::error('Admin create error: '.$e->getMessage());
            return back()->with('error', 'Unable to open create form.');
        }
    }

    public function adminStore(Request $request)
    {
        try {
            $data = $request->validate([
                'name'            => 'required|string|max:255',
                'price'           => 'required|numeric|min:0',
                'stock'           => 'required|integer|min:0',
                'category'        => 'nullable|string|max:255',
                'sku'             => 'nullable|string|max:64',
                'description'     => 'nullable|string|max:300',
                'is_active'       => 'nullable|boolean',
                'discount_type'   => 'nullable|in:percent,flat',
                'discount_value'  => 'nullable|numeric|min:0',
                'image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            ]);

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $data['user_id']   = $request->user()->id;
            $data['is_active'] = $request->boolean('is_active');

            Product::create($data);

            return redirect()->route('products.index')->with('success', 'Product added and published!');
        } catch (\Throwable $e) {
            \Log::error('Admin store error: '.$e->getMessage());
            return back()->with('error', 'Unable to add product.');
        }
    }

    // ... adminEdit, adminUpdate, adminDestroy exactly as you have them ...
    // (kept 100% original)

    public function adminEdit(Product $product)
    {
        try {
            if (!auth()->user()->isAdmin() && auth()->id() !== $product->user_id) {
                abort(403, 'Unauthorized');
            }
            $categories = $this->categories;
            return view('admin.products.edit', compact('product', 'categories'));
        } catch (\Throwable $e) {
            \Log::error('Admin edit error: '.$e->getMessage());
            return back()->with('error', 'Unable to load edit form.');
        }
    }

    public function adminUpdate(Request $request, Product $product)
    {
        try {
            if (!auth()->user()->isAdmin() && auth()->id() !== $product->user_id) {
                abort(403, 'Unauthorized');
            }
            $data = $request->validate([
                'name'            => 'required|string|max:255',
                'price'           => 'required|numeric|min:0',
                'stock'           => 'required|integer|min:0',
                'category'        => 'nullable|string|max:255',
                'sku'             => 'nullable|string|max:64',
                'description'     => 'nullable|string|max:300',
                'is_active'       => 'nullable|boolean',
                'discount_type'   => 'nullable|in:percent,flat',
                'discount_value'  => 'nullable|numeric|min:0',
                'image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            ]);

            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $data['is_active'] = $request->boolean('is_active');
            $product->update($data);

            return redirect()->route('admin.products.manage')->with('success', 'Product updated.');
        } catch (\Throwable $e) {
            \Log::error('Admin update error: '.$e->getMessage());
            return back()->with('error', 'Unable to update product.');
        }
    }

    public function adminDestroy(Product $product)
    {
        try {
            if (!auth()->user()->isAdmin() && auth()->id() !== $product->user_id) {
                abort(403, 'Unauthorized');
            }
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();
            return back()->with('success', 'Product deleted.');
        } catch (\Throwable $e) {
            \Log::error('Admin destroy error: '.$e->getMessage());
            return back()->with('error', 'Unable to delete product.');
        }
    }
}