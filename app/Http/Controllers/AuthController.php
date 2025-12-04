<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        HANDLE REGISTRATION
    -------------------------- */

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return redirect()->route('login')
                             ->with('success', 'Registration successful! Please login.');
        } 
        catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /* -------------------------
        HANDLE LOGIN
    -------------------------- */

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required'
            ]);

            if (!Auth::attempt($credentials, $request->boolean('remember'))) {
                return back()->withErrors(['email' => 'Invalid credentials'])
                             ->onlyInput('email');
            }

            $request->session()->regenerate();

            if (Auth::user()->email === 'admin@store.com') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('products.index');
        } 
        catch (\Exception $e) {
            return back()->with('error', 'Login failed: ' . $e->getMessage());
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

            return redirect()->route('register');
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

    /* -------------------------
        API — SANCTUM TOKEN
    -------------------------- */

    public function apiRegister(Request $request)
    {
        try {
            $data = $request->validate([
                'name'        => 'required',
                'email'       => 'required|email|unique:users,email',
                'password'    => 'required|min:6',
                'device_name' => 'required',
            ]);

            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $token = $user->createToken($data['device_name'])->plainTextToken;

            return response()->json([
                'message' => 'Registered successfully',
                'token'   => $token,
                'user'    => $user,
            ], 201);
        } 
        catch (\Exception $e) {
            return response()->json(['message' => 'Registration failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function apiLogin(Request $request)
    {
        try {
            $data = $request->validate([
                'email'       => 'required|email',
                'password'    => 'required',
                'device_name' => 'required',
            ]);

            $user = User::where('email', $data['email'])->first();

            if (!$user || !Hash::check($data['password'], $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 422);
            }

            $token = $user->createToken($data['device_name'])->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token'   => $token,
                'user'    => $user,
            ]);
        } 
        catch (\Exception $e) {
            return response()->json(['message' => 'Login failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function apiMe(Request $request)
    {
        try {
            return response()->json($request->user());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch user', 'error' => $e->getMessage()], 500);
        }
    }

    public function apiLogout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Token revoked']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Logout failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function apiLogoutAll(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'All tokens revoked']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Logout all failed', 'error' => $e->getMessage()], 500);
        }
    }
}
