<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'firstname' => $request->first_name,
            'lastname' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phoneno' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'buyer',
            'address' => null,
        ]);

        OtpService::generateAndSend($user->email, 'registration');

        return response()->json([
            'status' => true,
            'message' => 'Registration successful. OTP sent.',
            'email' => $user->email
        ]);
    }

    public function verifyRegisterOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        if (OtpService::verify($request->email, $request->otp)) {
            $user = User::where('email', $request->email)->first();
            // Mark verified if needed, or just return success
            return response()->json([
                'status' => true,
                'message' => 'Registration confirmed.'
            ]);
        }

        return response()->json(['status' => false, 'message' => 'Invalid OTP'], 400);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Admin Bypass
        if ($request->email === 'admin@store.com' && $request->password === 'admin123') {
            $user = User::firstOrCreate(
                ['email' => 'admin@store.com'],
                [
                    'firstname' => 'Admin', 
                    'lastname' => 'User',
                    'name' => 'Admin User',
                    'password' => Hash::make('admin123'),
                    'role' => 'admin',
                    'phoneno' => '0000000000'
                ]
            );
            
            // Ensure proper role/password even if exists
            if (!$user->isAdmin()) {
               $user->update(['role' => 'admin', 'password' => Hash::make('admin123')]);
            }

            $token = $user->createToken('admin-token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Admin login successful',
                'token' => $token,
                'role' => 'admin',
                'user' => $user
            ]);
        }

        // Standard User
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
             return response()->json(['status' => false, 'message' => 'Invalid credentials'], 401);
        }

        OtpService::generateAndSend($user->email, 'login');

        return response()->json([
            'status' => true,
            'message' => 'OTP sent for login.',
            'email' => $user->email
        ]);
    }

    public function verifyLoginOtp(Request $request)
    {
        $request->validate(['email' => 'required|email', 'otp' => 'required']);

        if (OtpService::verify($request->email, $request->otp)) {
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'role' => $user->role,
                'user' => $user
            ]);
        }

        return response()->json(['status' => false, 'message' => 'Invalid OTP'], 400);
    }

    public function resendRegisterOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        OtpService::generateAndSend($request->email, 'registration');
        return response()->json(['status' => true, 'message' => 'OTP resent.']);
    }

    public function resendLoginOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        OtpService::generateAndSend($request->email, 'login');
        return response()->json(['status' => true, 'message' => 'OTP resent.']);
    }
}
