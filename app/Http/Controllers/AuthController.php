<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // (required for delete old photo)

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
        try {
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

            return redirect()->route('login')
                             ->with('success', 'Registration successful! Please login to continue.');

        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    // Show Login Form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle Login
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required','email'],
                'password' => ['required'],
            ]);

            $remember = (bool) $request->boolean('remember');

            if (auth()->attempt($credentials, $remember)) {
                $request->session()->regenerate();
                return redirect()->intended(route('products.index'));
            }

            return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');

        } catch (\Exception $e) {
            return back()->with('error', 'Login failed: ' . $e->getMessage());
        }
    }

    // Logout
    public function logout(Request $request)
    {
        try {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login');

        } catch (\Exception $e) {
            return back()->with('error', 'Logout failed: ' . $e->getMessage());
        }
    }

    public function showProfile()
    {
        return view('auth.profile');
    }

    // Update profile
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            $data = $request->validate([
                'name'   => 'required|string|max:255',
                'email'  => 'required|email|unique:users,email,' . $user->id,
                'phone'  => 'nullable|string|max:10',
                'address'=> 'nullable|string|max:500',
                'password' => 'nullable|confirmed|min:6',
                'profile_photo' => 'nullable|image|max:2048',
            ]);

            // Handle password
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            // Profile photo upload
            if ($request->hasFile('profile_photo')) {

                $path = $request->file('profile_photo')->store('profile_photos', 'public');

                if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }

                $data['profile_photo'] = $path;
            }

            $user->update($data);

            return back()->with('success', 'Profile updated!');

        } catch (\Exception $e) {
            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }



    //  Sanctum Token APIs

// Token Register 
public function apiRegister(Request $request)
{
    try {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:6',
            'device_name' => 'required|string'
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // optional: set first login time as now if you want
        // $user->forceFill(['last_login_at' => now()])->save();

        $token = $user->createToken($data['device_name'], ['*'])->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully',
            'token'   => $token,
            'user'    => [
                'id'    => $user->getKey(),
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ], 201);

    } catch (\Exception $e) {
        return response()->json(['message' => 'Registration failed', 'error' => $e->getMessage()], 500);
    }
}

// Token Login 
public function apiLogin(Request $request)
{
    try {
        $data = $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string',
            'device_name' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        // (Optional) track last login time — add column if you want
        // $user->forceFill(['last_login_at' => now()])->save();

        $token = $user->createToken($data['device_name'], ['*'])->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'token'   => $token,
            'user'    => [
                'id'    => $user->getKey(),
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);

    } catch (\Exception $e) {
        return response()->json(['message' => 'Token issue failed', 'error' => $e->getMessage()], 500);
    }
}

// Current user 
public function apiMe(Request $request)
{
    try {
        $user = $request->user();
        return response()->json([
            'id'    => $user->getKey(),
            'name'  => $user->name,
            'email' => $user->email,
            //'last_login_at' => $user->last_login_at ?? null,
            'created_at'   => $user->created_at,
        ]);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to fetch user', 'error' => $e->getMessage()], 500);
    }
}


//  Revoke current token (logout this device)
 
public function apiLogout(Request $request)
{
    try {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Current token revoked']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Revoke failed', 'error' => $e->getMessage()], 500);
    }
}


  //Revoke all tokens (logout all devices)
 
 public function apiLogoutAll(Request $request)
{
    try {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'All tokens revoked']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Revoke all failed', 'error' => $e->getMessage()], 500);
    }
}

}
