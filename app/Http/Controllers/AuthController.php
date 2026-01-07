<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Services\OtpService;          
use Illuminate\Support\Facades\Session; 

class AuthController extends Controller
{
    /* -------------------------
        SHOW FORMS
    -------------------------- */
    public function showLoginForm()
    {
        try {
            return view('auth.login');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load login page: '.$e->getMessage());
        }
    }

    public function showRegisterForm()
    {
        try {
            return view('auth.register');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load registration page: '.$e->getMessage());
        }
    }

    public function showProfile()
    {
        try {
            return view('auth.profile');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load profile page: '.$e->getMessage());
        }
    }

    public function editProfile()
    {
        try {
            return view('profile.edit');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load profile edit page: '.$e->getMessage());
        }
    }

    /* -------------------------
        HANDLE REGISTRATION (with OTP)
    -------------------------- */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required|digits:10',
            // 'address' => 'required', // Removed
            'password'   => 'required|confirmed|min:6',
            // 'role'       => 'required|in:buyer,seller',
            'name'       => trim($request->first_name . ' ' . $request->last_name),
        ], [
            'phone.digits' => 'Phone number must be exactly 10 digits.',
        ]);

        $user = User::create([
            'firstname' => $request->first_name,
            'lastname'  => $request->last_name,
            'email'      => $request->email,
            'phoneno'    => $request->phone,
            'address'    => null, // Set to null as it's no longer in form
            'password'   => bcrypt($request->password),
            'role'       => 'buyer', // Default to buyer
            'name'       => trim($request->first_name . ' ' . $request->last_name),
        ]);

        // Generate & Send OTP
        OtpService::generateAndSend($user->email, 'registration', ['role' => $user->role]);

        // Store user temporarily in session
        session(['pending_registration_user_id' => $user->id]);

        // Go to OTP page (NOT login page)
        return redirect()->route('verify.register.otp')
                         ->with('info', 'We sent a 6-digit OTP to your email');
    }

    /* -------------------------
        HANDLE LOGIN (with OTP)
    -------------------------- */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required'
            ]);

            // Perform login attempt
            if (!Auth::attempt($credentials, $request->boolean('remember'))) {
                 return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
            }

            $user = Auth::user();

            // Special case: Admin bypasses OTP (redundant if using new AdminController but harmless)
            if ($user->email === 'admin@store.com') {
                $request->session()->regenerate();
                // Update login time
                 $user->update(['last_login_at' => now()]);
                return redirect()->route('admin.dashboard');
            }
            
            
            // Inactivity Check Logic
            // Use last_login_at if available, else created_at. 
            // If both are missing (unlikely), assume active.
            $lastActive = $user->last_login_at ?? $user->created_at;
            
            // If active within last 2 months (or date is null), SKIP OTP
            if (!$lastActive || $lastActive->gt(now()->subMonths(2))) {
                $request->session()->regenerate();
                $user->update(['last_login_at' => now()]);
                return redirect()->intended(route('products.index')); 
            }

            // Inactive > 2 months -> Require OTP
            OtpService::generateAndSend($user->email, 'login');

            Auth::logout(); // Log them out pending OTP
            session(['pending_login_email' => $request->email]);

            return view('auth.verify-otp', ['email' => $request->email, 'type' => 'login']);
        }
        catch (\Exception $e) {
            return back()->with('error', 'Login failed: ' . $e->getMessage());
        }
    }

    /* -------------------------
        VERIFY OTP (for both login & register)
    -------------------------- */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        if (!OtpService::verify($request->email, $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        // Register flow
        if (session('pending_user_id')) {
            $user = User::find(session('pending_user_id'));
            Auth::login($user);
            session()->forget('pending_user_id');
            
            // Merge Cart
            $this->mergeCart($user);
            
            // Redirect based on role
            return $this->redirectBasedOnRole($user);
        }

        // Login flow
        if (session('pending_login_email')) {
            $user = User::where('email', $request->email)->first();
            Auth::login($user);
            session()->forget('pending_login_email');
            
            $request->session()->regenerate();
            
            // Update Activity Timestamp
            $user->update(['last_login_at' => now()]);
            
            $this->mergeCart($user);

            return $this->redirectBasedOnRole($user);
        }

        return redirect()->route('products.index');
    }

    /* -------------------------
        HELPER: Role Redirect
    -------------------------- */
    protected function redirectBasedOnRole($user)
    {
        // Treat both Admin and Seller (legacy) as Admin
        if ($user->isAdmin() || $user->isSeller()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('products.index');
        }
    }

    /* -------------------------
        LOGOUT
    -------------------------- */
    public function logout(Request $request)
    {
        try {
            // Cart Persistence: Capture cart before invalidating session
            $cart = session('cart', []);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Restore cart for guest view
            if (!empty($cart)) {
                session(['cart' => $cart]);
            }

            return redirect()->route('products.index');
        }
        catch (\Exception $e) {
            return back()->with('error', 'Logout failed: ' . $e->getMessage());
        }
    }

    /* -------------------------
        UPDATE PROFILE
    -------------------------- */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            $data = $request->validate([
                'firstname'     => 'required|string|max:255',
                'lastname'      => 'required|string|max:255',
                'email'         => 'required|email|unique:users,email,' . $user->id,
                'phoneno'       => 'nullable|digits:10',
                'address'       => 'nullable|string|max:500',
                'profile_photo' => 'nullable|image|max:2048',
            ], [
                'phoneno.digits' => 'Phone number must be exactly 10 digits.',
            ]);

            // Optional: Sync 'name' column if it exists in DB but not fillable (won't work with update())
            // Or just rely on firstname/lastname.
            // Let's just rely on the fillable attributes being updated.


            if ($request->hasFile('profile_photo')) {
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                if ($user->profile_photo) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                $data['profile_photo'] = $path;
            }

            $user->update($data);
            return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
        }
        catch (\Exception $e) {
            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function verifyRegisterOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);
    
        $userId = session('pending_registration_user_id');
    
        if (!$userId) {
            return redirect()->route('register')->with('error', 'Session expired. Please register again.');
        }
    
        $user = User::find($userId);
    
        if (OtpService::verify($user->email, $request->otp)) {
            // OTP correct → log the user in automatically
            // OTP correct → log the user in automatically
            Auth::login($user);
            session()->forget('pending_registration_user_id');
            
            // Merge Cart
            $this->mergeCart($user);
    
            return $this->redirectBasedOnRole($user);
        }
    
        return back()->withErrors(['otp' => 'Invalid or expired OTP']);
    }

    /**
     * Resend OTP for registration
     */
    public function resendRegisterOtp()
    {
        $userId = session('pending_registration_user_id');
        
        if (!$userId) {
            return redirect()->route('register')->with('error', 'Session expired. Please register again.');
        }

        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->route('register')->with('error', 'User not found. Please register again.');
        }

        OtpService::generateAndSend($user->email, 'registration', ['role' => $user->role]);
        
        return back()->with('status', 'A new OTP has been sent to your email.');
    }

    /* -------------------------
         FORGOT PASSWORD FLOW
    -------------------------- */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendPasswordResetOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email']);
        }

        OtpService::generateAndSend($user->email, 'password_reset');
        session(['password_reset_email' => $request->email]);

        return view('auth.reset-password-otp', ['email' => $request->email]);
    }

    public function verifyPasswordResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6'
        ]);

        if (!OtpService::verify($request->email, $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        session(['password_reset_verified' => $request->email]);
        
        return view('auth.reset-password', ['email' => $request->email]);
    }

    public function resendPasswordResetOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        OtpService::generateAndSend($request->email, 'password_reset');
        
        return back()->with('status', 'A new OTP has been sent');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6'
        ]);

        if (session('password_reset_verified') !== $request->email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Session expired']);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => bcrypt($request->password)]);

        session()->forget(['password_reset_email', 'password_reset_verified']);

        return redirect()->route('login')->with('success', 'Password reset successfully! Please login.');
    } // End updatePassword

    /* -------------------------
        HELPER: Merge Cart (Session -> DB -> Session)
    -------------------------- */
    protected function mergeCart($user)
    {
        $sessionCart = session('cart', []);

        // 1. Push Session Items to DB
        foreach ($sessionCart as $id => $details) {
            $existing = Cart::where('user_id', $user->id)->where('product_id', $id)->first();
            if ($existing) {
                $existing->increment('qty', $details['qty']);
            } else {
                Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $id,
                    'qty' => $details['qty']
                ]);
            }
        }

        // 2. Pull All Items from DB to Session (Refresh)
        $dbItems = Cart::where('user_id', $user->id)->with('product')->get();
        
        // Safety: If DB is empty but we had session items, something went wrong with the sync.
        // In that case, DO NOT wipe the session cart. Keep the session items.
        if ($dbItems->isEmpty() && !empty($sessionCart)) {
            \Log::warning('Cart merge: DB empty after push. Keeping session data to prevent data loss.');
            return;
        }

        $newCart = [];

        foreach ($dbItems as $item) {
            if (!$item->product) continue; 
            
            $product = $item->product;
            $price = (float) $product->price;

            if (!empty($product->discount_value) && !empty($product->discount_type)) {
                if ($product->discount_type === 'percent') {
                    $discount = $price * ($product->discount_value / 100);
                } else {
                    $discount = (float) $product->discount_value;
                }
                $finalPrice = round($price - $discount, 2);
            } else {
                $finalPrice = $price;
            }

            $newCart[$product->id] = [
                'name'        => $product->name,
                'price'       => $finalPrice,
                'original_price' => $price,
                'qty'         => $item->qty,
                'image'       => $product->image,
                'category'    => $product->category,
                'description' => $product->description,
            ];
        }

        session(['cart' => $newCart]);
    }
}
