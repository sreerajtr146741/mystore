<?php

namespace App\Http\Controllers;

use App\Models\User;
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

    /* -------------------------
        HANDLE REGISTRATION (with OTP)
    -------------------------- */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required',
            'address'    => 'required',
            'password'   => 'required|confirmed|min:6',
            'role'       => 'required|in:buyer,seller', // Validate Role
            'name'       => trim($request->first_name . ' ' . $request->last_name),
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'address'    => $request->address,
            'password'   => bcrypt($request->password),
            'role'       => $request->role, // Save Role
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

            if (!Auth::attempt($credentials, $request->boolean('remember'))) {
                return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
            }

            // Special case: Admin bypasses OTP
            if (Auth::user()->email === 'admin@store.com') {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }

            // Regular users: send OTP
            OtpService::generateAndSend(Auth::user()->email, 'login');

            // Force OTP screen instead of direct login
            Auth::logout(); // We'll log them in only after OTP
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
            
            // Redirect based on role
            return $this->redirectBasedOnRole($user);
        }

        // Login flow
        if (session('pending_login_email')) {
            $user = User::where('email', $request->email)->first();
            Auth::login($user);
            session()->forget('pending_login_email');
            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user);
        }

        return redirect()->route('products.index');
    }

    /* -------------------------
        HELPER: Role Redirect
    -------------------------- */
    protected function redirectBasedOnRole($user)
    {
        if ($user->isAdmin() || $user->email === 'admin@store.com') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isSeller()) {
            return redirect()->route('seller.dashboard');
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
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
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
                'name'          => 'required|string|max:255',
                'email'         => 'required|email|unique:users,email,' . $user->id,
                'phone'         => 'nullable|string|max:10',
                'address'       => 'nullable|string|max:500',
                'password'      => 'nullable|confirmed|min:6',
                'profile_photo' => 'nullable|image|max:2048',
            ]);

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            if ($request->hasFile('profile_photo')) {
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                if ($user->profile_photo) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                $data['profile_photo'] = $path;
            }

            $user->update($data);
            return back()->with('success', 'Profile updated successfully!');
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
            Auth::login($user);
            session()->forget('pending_registration_user_id');
    
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
    }
}
