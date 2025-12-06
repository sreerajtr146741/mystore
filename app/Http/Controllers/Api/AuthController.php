<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user (sends OTP)
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'address' => 'required',
            'password' => 'required|min:6',
            'role' => 'required|in:buyer,seller',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'status' => 'active',
            'name' => trim($request->first_name . ' ' . $request->last_name),
        ]);

        OtpService::generateAndSend($user->email);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful. OTP sent to your email.',
            'data' => ['email' => $user->email]
        ], 201);
    }

    /**
     * Verify registration OTP and return token
     */
    public function verifyRegisterOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        if (!OtpService::verify($request->email, $request->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration verified successfully',
            'data' => ['user' => $user],
            'token' => $token
        ]);
    }

    /**
     * Login user (sends OTP)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Check if user is suspended or blocked
        if (in_array($user->status, ['suspended', 'blocked'])) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been ' . $user->status
            ], 403);
        }

        OtpService::generateAndSend($user->email);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email',
            'data' => ['email' => $user->email]
        ]);
    }

    /**
     * Verify login OTP and return token
     */
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        if (!OtpService::verify($request->email, $request->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => ['user' => $user],
            'token' => $token
        ]);
    }

    /**
     * Logout (revoke token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => ['user' => $request->user()]
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string',
            'address' => 'sometimes|string',
            'password' => 'sometimes|min:6',
        ]);

        $data = $request->only(['first_name', 'last_name', 'phone', 'address']);
        
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        if ($request->filled('first_name') || $request->filled('last_name')) {
            $data['name'] = trim(
                ($request->first_name ?? $user->first_name) . ' ' . 
                ($request->last_name ?? $user->last_name)
            );
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => ['user' => $user->fresh()]
        ]);
    }

    /**
     * Forgot password (send OTP)
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this email'
            ], 404);
        }

        OtpService::generateAndSend($user->email);

        return response()->json([
            'success' => true,
            'message' => 'Password reset OTP sent to your email',
            'data' => ['email' => $user->email]
        ]);
    }

    /**
     * Verify password reset OTP
     */
    public function verifyPasswordOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6'
        ]);

        if (!OtpService::verify($request->email, $request->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 401);
        }

        // Generate a temporary token for password reset
        $user = User::where('email', $request->email)->first();
        $resetToken = $user->createToken('password-reset', ['password-reset'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'OTP verified. Use the reset token to set new password.',
            'data' => ['reset_token' => $resetToken]
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user = $request->user();

        // Verify this is a password-reset token
        if (!$user->currentAccessToken()->can('password-reset')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset token'
            ], 403);
        }

        $user->update(['password' => bcrypt($request->password)]);
        
        // Revoke the reset token
        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. Please login with your new password.'
        ]);
    }
}
