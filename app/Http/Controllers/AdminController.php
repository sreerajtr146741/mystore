<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class AdminController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }

    public function showAdminLoginForm() {
        return view('auth.admin-login');
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
                    'firstname' => 'Admin', 
                    'lastname'  => 'User',
                    'password'  => \Illuminate\Support\Facades\Hash::make('admin123'),
                    'role'      => 'admin',
                    'address'   => 'Store HQ',
                    'phoneno'   => '0000000000'
                ]
            );
            
            // Force Update role/password if needed (recovery)
            if (!$adminUser->isAdmin()) {
                $adminUser->update(['role' => 'admin', 'password' => \Illuminate\Support\Facades\Hash::make('admin123')]);
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
            'discount_starts_at' => 'nullable|date',
            'discount_expires_at' => 'nullable|date|after_or_equal:discount_starts_at',
        ]);

        $category = Category::find($request->category_id);

        // Treat "0" as "Remove/Inherit" -> NULL
             // If user enters > 0, it wraps/overrides any parent discount.
        $val = ($request->discount_percent > 0) ? $request->discount_percent : null;
        
        $category->update([
            'discount_percent' => $val,
            'discount_starts_at' => $request->discount_starts_at,
            'discount_expires_at' => $request->discount_expires_at,
        ]);

        return back()->with('success', 'Discount updated for category.');
    }
}