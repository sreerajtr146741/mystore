<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check session override first (Requested Feature)
        if (session('is_admin')) {
            return $next($request);
        }

        // Allow admin@store.com email directly
        if ($user->email === 'admin@store.com') {
            return $next($request);
        }

        // Adjust to your schema:
        // either boolean 'is_admin' (1) OR role column equals 'admin'
        if ((property_exists($user, 'is_admin') && (int) $user->is_admin === 1)
            || (property_exists($user, 'role') && $user->role === 'admin')) {
            return $next($request);
        }

        abort(403, 'Only admins can access this area.');
    }
}
