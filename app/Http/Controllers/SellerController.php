<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SellerController extends Controller
{
    /**
     * Display seller dashboard (list of their products).
     */
    public function dashboard()
    {
        $products = Product::where('user_id', Auth::id())->latest()->paginate(10);
        $categories = Category::with('parent')->get();
        return view('seller.dashboard', compact('products', 'categories'));
    }

    /**
     * Show form to create a new product.
     */
    public function create()
    {
        $categories = Category::all();
        return view('seller.products.create', compact('categories'));
    }

    /**
     * Store a new product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'image' => 'required|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'discount_value' => 'nullable|numeric|min:0|max:100',
        ]);

        $path = $request->file('image')->store('products', 'public');
        $category = Category::find($request->category_id);

        Product::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock ?? 10,
            'category' => $category->name,
            'category_id' => $category->id,
            'image' => $path,
            'status' => 'active',
            'is_active' => true,
            'discount_type' => 'percent',
            'discount_value' => $request->discount_value ?? 0,
            'is_discount_active' => $request->has('is_discount_active') ? 1 : 0,
        ]);

        return redirect()->route('seller.dashboard')->with('success', 'Product created successfully!');
    }

    /**
     * Show form to edit a product.
     */
    public function edit($id)
    {
        // Ensure ownership
        $product = Product::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $categories = Category::all();
        return view('seller.products.edit', compact('product', 'categories'));
    }

    /**
     * Update a product.
     */
    public function update(Request $request, $id)
    {
        $product = Product::where('user_id', Auth::id())->where('id', $id)->firstOrFail();

        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
        ];

        // Update category name string for legacy
        $category = Category::find($request->category_id);
        if ($category) {
            $data['category'] = $category->name;
        }

        if ($request->hasFile('image')) {
             $request->validate(['image' => 'image|max:2048']);
             // Delete old image if exists and local
             if ($product->image && !Str::startsWith($product->image, 'http')) {
                 Storage::disk('public')->delete($product->image);
             }
             $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('seller.dashboard')->with('success', 'Product updated successfully!');
    }

    /**
     * Delete a product.
     */
    public function destroy($id)
    {
        $product = Product::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        
        if ($product->image && !Str::startsWith($product->image, 'http')) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return redirect()->route('seller.dashboard')->with('success', 'Product deleted successfully!');
    }

    /**
     * Set discount for entire category
     */
    public function setCategoryDiscount(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'discount_percent' => 'required|numeric|min:0|max:100',
        ]);

        $category = Category::findOrFail($request->category_id);
        $discountPercent = $request->discount_percent;

        // Update category discount
        $category->update(['discount_percent' => $discountPercent]);

        // Get all products in this category (and subcategories if parent)
        $categoryIds = [$category->id];
        
        // If it's a parent category, include all children
        if ($category->parent_id === null) {
            $childIds = Category::where('parent_id', $category->id)->pluck('id')->toArray();
            $categoryIds = array_merge($categoryIds, $childIds);
        }

        // Update all seller's products in these categories
        $affectedCount = Product::where('user_id', Auth::id())
            ->whereIn('category_id', $categoryIds)
            ->update([
                'discount_type' => 'percent',
                'discount_value' => $discountPercent,
                'is_discount_active' => $discountPercent > 0 ? 1 : 0,
            ]);

        $message = $discountPercent > 0 
            ? "Applied {$discountPercent}% discount to {$affectedCount} products in {$category->name}"
            : "Removed discount from {$affectedCount} products in {$category->name}";

        return redirect()->route('seller.dashboard')->with('success', $message);
    }

    /**
     * Show seller profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('seller.profile', compact('user'));
    }

    /**
     * Update seller profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string',
            'address' => 'required|string',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('seller.profile')->with('success', 'Profile updated successfully!');
    }
}
