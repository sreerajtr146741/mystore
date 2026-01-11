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
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /* -------------------------
        HANDLE REGISTRATION
    -------------------------- */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required|digits:10',
            'password'   => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'firstname' => $request->first_name,
            'lastname'  => $request->last_name,
            'name'      => $request->first_name . ' ' . $request->last_name,
            'email'     => $request->email,
            'phoneno'   => $request->phone,
            'password'  => Hash::make($request->password),
            'role'      => 'buyer',
        ]);

        // Generate & Send OTP
        OtpService::generateAndSend($user->email, 'registration');

        // Store user temporarily in session
        session(['pending_registration_user_id' => $user->id]);

        return redirect()->route('verify.register.otp', ['email' => $user->email])
                         ->with('info', 'We sent a 6-digit OTP to your email');
    }

    public function verifyRegisterOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
            'email' => 'required|email'
        ]);
    
        if (OtpService::verify($request->email, $request->otp)) {
            $user = User::where('email', $request->email)->first();
            Auth::login($user);
            session()->forget('pending_registration_user_id');
            return redirect()->route('home')->with('success', 'Welcome!');
        }
    
        return back()->withErrors(['otp' => 'Invalid or expired OTP']);
    }

    /* -------------------------
        HANDLE LOGIN
    -------------------------- */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Admin Bypass
        if ($request->email === 'admin@store.com' && $request->password === 'admin123') {
             $user = User::firstOrCreate(['email' => 'admin@store.com'], [
                'name' => 'Administrator', 'role' => 'admin', 'password' => Hash::make('admin123'),
                'firstname' => 'Admin', 'lastname' => 'User', 'phoneno' => '0000000000'
             ]);
             Auth::login($user);
             return redirect()->route('dashboard'); // Admin Dashboard
        }

        if (!Auth::validate($credentials)) {
             return back()->withErrors(['email' => 'Invalid credentials']);
        }

        $user = User::where('email', $request->email)->first();

        // Send OTP
        OtpService::generateAndSend($user->email, 'login');
        session(['pending_login_email' => $request->email]);

        return view('auth.verify-otp', ['email' => $request->email, 'type' => 'login']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        if (OtpService::verify($request->email, $request->otp)) {
            $user = User::where('email', $request->email)->first();
            Auth::login($user);
            session()->forget('pending_login_email');
            
            if ($user->isAdmin()) {
                return redirect()->route('dashboard'); // Admin Dashboard
            }
            return redirect()->route('home');
        }

        return back()->withErrors(['otp' => 'Invalid OTP']);
    }

    /* -------------------------
        LOGOUT
    -------------------------- */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    /* -------------------------
        PROFILE
    -------------------------- */
    public function editProfile()
    {
        return view('profile.edit');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phoneno' => 'required',
        ]);
        
        $user->update($data);
        return back()->with('success', 'Profile updated');
    }

    /* -------------------------
        PASSWORD RESET
    -------------------------- */
    public function showForgotPasswordForm() { return view('auth.forgot-password'); }
    
    public function sendPasswordResetOtp(Request $request) {
        $request->validate(['email'=>'required|email']);
        $user = User::where('email',$request->email)->first();
        if(!$user) return back()->withErrors(['email'=>'Not found']);
        
        OtpService::generateAndSend($request->email, 'password_reset');
        return view('auth.reset-password-otp', ['email'=>$request->email]);
    }

    public function verifyPasswordResetOtp(Request $request) {
        if(OtpService::verify($request->email, $request->otp)) {
             return view('auth.reset-password', ['email'=>$request->email]);
        }
        return back()->withErrors(['otp'=>'Invalid OTP']);
    }
    
    public function resendPasswordResetOtp(Request $request) {
        OtpService::generateAndSend($request->email, 'password_reset');
        return back()->with('status', 'Resent');
    }

    public function updatePassword(Request $request) {
        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);
        return redirect()->route('login')->with('success', 'Password reset');
    }

     public function resendRegisterOtp(Request $request)
    {
        $email = $request->email ?? session('pending_registration_user_id') ? User::find(session('pending_registration_user_id'))->email : null;
        if($email) OtpService::generateAndSend($email, 'registration');
        return back()->with('message', 'OTP Resent');
    }
}
