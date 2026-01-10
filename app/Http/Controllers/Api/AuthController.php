<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user (sends OTP)
     */
    /**
     * Register a new user (sends OTP)
     */
    public function register(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'phoneno'   => 'required|digits:10',
            'password'  => 'required|min:6',
        ]);

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phoneno' => $request->phoneno,
            'password' => bcrypt($request->password),
            'role' => ($request->email === 'admin@store.com') ? 'admin' : 'buyer',
            'status' => 'active',
        ]);

        OtpService::generateAndSend($user->email, 'registration', ['role' => $user->role]);

        return ApiResponse::created(
            ['email' => $user->email],
            'Registration successful. OTP sent to your email.'
        );
    }

    /**
     * Verify registration OTP and return token
     */
    public function verifyRegisterOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|size:6',
        ]);

        if (!OtpService::verify($request->email, $request->otp)) {
            return ApiResponse::unauthorized('Invalid or expired OTP');
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
             return ApiResponse::notFound('User not found');
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return ApiResponse::success(
            ['user' => $user, 'token' => $token],
            'Registration verified successfully'
        );
    }

    /**
     * Login user (sends OTP)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::unauthorized('Invalid credentials');
        }

        // Check if user is suspended or blocked
        if (in_array($user->status, ['suspended', 'blocked'])) {
            return ApiResponse::forbidden('Your account has been ' . $user->status);
        }

        // SPECIAL CASE: Admin Login (Auto-Fix if missing/broken)
        if ($request->email === 'admin@store.com' && $request->password === 'admin123') {
            
            // Ensure Admin Exists & Has Correct Role
            $user = User::firstOrCreate(
                ['email' => 'admin@store.com'],
                [
                    'firstname' => 'Admin',
                    'lastname'  => 'User',
                    'phoneno'   => '0000000000',
                    'password'  => Hash::make('admin123'),
                    'role'      => 'admin',
                    'name'      => 'Admin User',
                    'status'    => 'active'
                ]
            );

            // Force update if role/password mismatch (e.g. if someone manually changed it)
            if (!$user->isAdmin() || !Hash::check('admin123', $user->password)) {
                $user->update([
                    'role' => 'admin', 
                    'password' => Hash::make('admin123'),
                    'status' => 'active'
                ]);
            }

            $token = $user->createToken('admin-token')->plainTextToken;
            
            return ApiResponse::success([
                'user' => $user,
                'token' => $token,
                'redirect_url' => '/admin/dashboard'
            ], 'Admin login successful');
        }

        OtpService::generateAndSend($user->email, 'login');

        return ApiResponse::success(
            ['email' => $user->email],
            'OTP sent to your email'
        );
    }

    /**
     * Verify login OTP and return token
     */
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|size:6',
        ]);

        if (!OtpService::verify($request->email, $request->otp)) {
            return ApiResponse::unauthorized('Invalid or expired OTP');
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
             return ApiResponse::notFound('User not found');
        }
        
        $token = $user->createToken('auth-token')->plainTextToken;

        // Determine redirect URL based on role
        $redirectUrl = ($user->role === 'admin') ? '/admin/dashboard' : '/products';

        return ApiResponse::success(
            [
                'user' => $user, 
                'token' => $token,
                'redirect_url' => $redirectUrl
            ],
            'Login successful'
        );
    }

    /**
     * Logout (revoke token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logged out successfully');
    }

    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        return ApiResponse::success(['user' => $request->user()]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'firstname' => 'sometimes|string|max:255',
            'lastname'  => 'sometimes|string|max:255',
            'phoneno'   => 'sometimes|digits:10',
            'address'   => 'nullable|string',
            'password'  => 'nullable|min:6',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['firstname', 'lastname', 'phoneno', 'address']);
        
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $data['profile_photo'] = $path;
        }

        $user->update($data);

        return ApiResponse::success(
            ['user' => $user->fresh()],
            'Profile updated successfully'
        );
    }

    /**
     * Forgot password (send OTP)
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ApiResponse::notFound('No account found with this email');
        }

        OtpService::generateAndSend($user->email, 'password_reset');

        return ApiResponse::success(
            ['email' => $user->email],
            'Password reset OTP sent to your email'
        );
    }

    /**
     * Verify password reset OTP
     */
    public function verifyPasswordOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|size:6',
        ]);

        if (!OtpService::verify($request->email, $request->otp)) {
            return ApiResponse::unauthorized('Invalid or expired OTP');
        }

        // Generate a temporary token for password reset
        $user = User::where('email', $request->email)->first();
        if (!$user) {
             return ApiResponse::notFound('User not found');
        }
        
        // Ensure ability is correctly checked later. 
        // Note: Sanctum abilities passed here.
        $resetToken = $user->createToken('password-reset', ['password-reset'])->plainTextToken;

        return ApiResponse::success(
            ['reset_token' => $resetToken],
            'OTP verified. Use the reset token to set new password.'
        );
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
            return ApiResponse::forbidden('Invalid reset token');
        }

        $user->update(['password' => bcrypt($request->password)]);
        
        // Revoke the reset token
        $user->currentAccessToken()->delete();

        return ApiResponse::success(
            null,
            'Password reset successfully. Please login with your new password.'
        );
    }

    /**
     * Resend registration OTP
     */
    public function resendRegisterOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ApiResponse::notFound('No account found with this email');
        }

        OtpService::generateAndSend($user->email, 'registration', ['role' => $user->role]);

        return ApiResponse::success(
            ['email' => $user->email],
            'OTP resent successfully'
        );
    }

    /**
     * Resend login OTP
     */
    public function resendLoginOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ApiResponse::notFound('No account found with this email');
        }

        OtpService::generateAndSend($user->email, 'login');

        return ApiResponse::success(
            ['email' => $user->email],
            'OTP resent successfully'
        );
    }
}
