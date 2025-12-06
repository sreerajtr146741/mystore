<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class AdminController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }

    public function adminLogin(Request $request) {
        $request->validate(['email' => 'required', 'password' => 'required']);

        if ($request->email === 'admin@store.com' && $request->password === 'admin123') {
            session(['is_admin' => true]);
            return redirect()->route('admin.dashboard');
        }
        return back()->withErrors(['email' => 'Not an admin or incorrect credentials']);
    }

    public function manageProducts() {
        if (!session('is_admin')) abort(403);
        $products = Product::with('category')->get();
        $categories = Category::with('children')->get();
        return view('admin.products', compact('products', 'categories'));
    }

    public function setCategoryDiscount(Request $request) {
        if (!session('is_admin')) abort(403);
        $request->validate(['category_id' => 'required|exists:categories,id', 'discount_percent' => 'required|numeric|min:0|max:100']);

        $category = Category::find($request->category_id);
        $category->update(['discount_percent' => $request->discount_percent]);

        // Apply to child models (e.g., BMW models)
        if ($category->children->count()) {
            $category->children->each(function ($child) use ($request) {
                $child->update(['discount_percent' => $request->discount_percent]);
            });
        }

        // Recalc product prices
        Product::whereHas('category', function ($q) use ($category) {
            $q->where('id', $category->id)->orWhereIn('parent_id', [$category->id]);
        })->get()->each(function ($product) {
            $product->update(['discounted_price' => $product->getDiscountedPriceAttribute()]);
        });

        return back()->with('success', 'Discount applied to category and models');
    }
}