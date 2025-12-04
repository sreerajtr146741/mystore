<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        try {

            return view('auth.login');

        } catch (\Throwable $e) {

            \Log::error('Login view error: '.$e->getMessage());
            return back()->with('error', 'Unable to load login page.');
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        try {

            // Validate login fields
            $request->validate([
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ]);

            // Attempt to login
            if (! Auth::attempt(
                $request->only('email', 'password'),
                $request->boolean('remember')
            )) {
                throw ValidationException::withMessages([
                    'email' => __('auth.failed'),
                ]);
            }

            // Regenerate session
            $request->session()->regenerate();

            // Default redirect
            return redirect()->intended('/dashboard');

        } catch (ValidationException $e) {

            // Re-throw validation errors normally
            throw $e;

        } catch (\Throwable $e) {

            \Log::error('Login error: '.$e->getMessage());

            return back()->with('error', 'Login failed. Please try again.');
        }
    }

    /**
     * Destroy an authenticated session (logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {

            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');

        } catch (\Throwable $e) {

            \Log::error('Logout error: '.$e->getMessage());

            return back()->with('error', 'Unable to logout. Please try again.');
        }
    }
}
