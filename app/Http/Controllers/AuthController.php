<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show Register Form
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Handle Registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Success message + redirect to login (NOT auto-login)
        return redirect()->route('login')
                         ->with('success', 'Registration successful! Please login to continue.');
    }

    // Show Login Form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Optional: Create Sanctum token on login
            $user = Auth::user();
            // $user->tokens()->delete();
            // $user->createToken('auth_token')->plainTextToken;

            return redirect()->intended(route('products.index'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function showProfile()
{
    return view('auth.profile');
}
//update profile
public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,'.$user->id,
            'phone'  => 'nullable|string|max:10',
            'address'=> 'nullable|string|max:500',
            'password' => 'nullable|confirmed|min:6',
            'profile_photo' => 'nullable|image|max:2048', // <— IMPORTANT
        ]);

        // Handle password (optional)
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Handle profile photo (new upload)
        if ($request->hasFile('profile_photo')) {
            // store to storage/app/public/profile_photos
            $path = $request->file('profile_photo')->store('profile_photos', 'public');

            // Delete old file if exists (optional cleanup)
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $data['profile_photo'] = $path;  // <— Save relative path like "profile_photos/abc.jpg"
        }

        $user->update($data);

        return back()->with('success', 'Profile updated!');
    }
}
