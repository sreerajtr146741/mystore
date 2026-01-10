<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * REGISTER – JSON API
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required|digits:10',
            'password'   => 'required|confirmed|min:6'
        ]);

        // Create User
        $user = User::create([
            'firstname' => $request->first_name,
            'lastname'  => $request->last_name,
            'name'      => trim($request->first_name . ' ' . $request->last_name),
            'email'     => $request->email,
            'phoneno'   => $request->phone,
            'address'   => null,
            'role'      => 'buyer',        // DEFAULT ROLE
            'password'  => Hash::make($request->password)
        ]);

        // Send OTP
        OtpService::generateAndSend($user->email, 'registration', [
            'role' => $user->role
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Registration successful. OTP sent to email.',
            'email'   => $user->email
        ]);
    }

    /**
     * VERIFY REGISTER OTP – JSON API
     */
    public function verifyRegisterOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6'
        ]);

        if (!OtpService::verify($request->email, $request->otp)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        return response()->json([
            'status'  => true,
            'message' => 'OTP verified. Registration complete.'
        ]);
    }

    /**
     * LOGIN – JSON API
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // -------- ADMIN LOGIN BYPASS ----------
        if ($request->email === 'admin@store.com' && $request->password === 'admin123') {

            // Create admin if not exists
            $admin = User::firstOrCreate(
                ['email' => 'admin@store.com'],
                [
                    'firstname' => 'Admin',
                    'lastname'  => 'User',
                    'name'      => 'Admin User',
                    'phoneno'   => '0000000000',
                    'password'  => Hash::make('admin123'),
                    'role'      => 'admin'
                ]
            );

            $token = $admin->createToken('admin_token')->plainTextToken;

            return response()->json([
                'status'  => true,
                'message' => 'Admin login successful',
                'token'   => $token,
                'role'    => 'admin'
            ]);
        }
        // ---------------------------------------

        // Normal User
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email or password'
            ], 400);
        }

        // Send OTP for login
        OtpService::generateAndSend($user->email, 'login');

        return response()->json([
            'status'  => true,
            'message' => 'OTP sent to email',
            'email'   => $user->email
        ]);
    }

    /**
     * VERIFY LOGIN OTP – JSON API
     */
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6'
        ]);

        if (!OtpService::verify($request->email, $request->otp)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => $user,
            'role'    => $user->role
        ]);
    }

    /**
     * RESEND OTP – JSON API
     */
    public function resendRegisterOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        OtpService::generateAndSend($request->email, 'registration');

        return response()->json([
            'status'  => true,
            'message' => 'New OTP sent'
        ]);
    }

    public function resendLoginOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        OtpService::generateAndSend($request->email, 'login');

        return response()->json([
            'status'  => true,
            'message' => 'Login OTP resent'
        ]);
    }
}
