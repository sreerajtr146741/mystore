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

        // 1. Check hardcoded credentials for legacy/failsafe, but perform REAL auth
        if ($request->email === 'admin@store.com' && $request->password === 'admin123') {
            // Ensure Admin User Exists in DB (Auto-seed if missing)
            $adminUser = \App\Models\User::firstOrCreate(
                ['email' => 'admin@store.com'],
                [
                    'name' => 'Administrator',
                    'first_name' => 'Admin', 
                    'last_name' => 'User',
                    'password' => bcrypt('admin123'),
                    'role' => 'admin',
                    'address' => 'Store HQ',
                    'phone' => '0000000000'
                ]
            );
            
            // Force Update role/password if needed (recovery)
            if (!$adminUser->isAdmin()) {
                $adminUser->update(['role' => 'admin', 'password' => bcrypt('admin123')]);
            }
            
            // Log them in natively
            \Illuminate\Support\Facades\Auth::login($adminUser);
            $adminUser->update(['last_login_at' => now()]);
            $request->session()->regenerate();
            
            return redirect()->route('admin.dashboard');
        }

        // 2. Standard Attempt (for other admins if any)
        if (\Illuminate\Support\Facades\Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            
            // Update Activity
            $request->user()->update(['last_login_at' => now()]);

            if ($request->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            
            // If they validly logged in but are NOT admin
            \Illuminate\Support\Facades\Auth::logout();
            return back()->withErrors(['email' => 'Access denied. Admins only.']);
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function manageProducts() {
        // middleware('admin') handled
        $products = Product::with('linkedCategory')->get();
        $categories = Category::with('children')->get();
        return view('admin.products', compact('products', 'categories'));
    }

    public function setCategoryDiscount(Request $request) {
        $request->validate([
            'category_id' => 'required|exists:categories,id', 
            'discount_percent' => 'required|numeric|min:0|max:100',
            'discount_expires_at' => 'nullable|date|after:now',
        ]);

        $category = Category::find($request->category_id);

        // Treat "0" as "Remove/Inherit" -> NULL
        // If user enters > 0, it wraps/overrides any parent discount.
        $val = ($request->discount_percent > 0) ? $request->discount_percent : null;
        
        $category->update([
            'discount_percent' => $val,
            'discount_expires_at' => $request->discount_expires_at,
        ]);

        // NO recursive update to children anymore. 
        // We rely on Product model to check Parent if Child is null.

        return back()->with('success', 'Discount updated for category.');
    }
}