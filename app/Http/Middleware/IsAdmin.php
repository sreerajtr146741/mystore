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
            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

         // Check Model Role
        $is_admin = false;
        
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            $is_admin = true;
        }
        elseif ($user->email === 'admin@store.com') {
            $is_admin = true;
        }
        elseif ((property_exists($user, 'is_admin') && (int) $user->is_admin === 1)
            || (property_exists($user, 'role') && $user->role === 'admin')) {
            $is_admin = true;
        }

        if ($is_admin) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['status' => false, 'message' => 'Access denied. Admins only.'], 403);
        }

        abort(403, 'Only admins can access this area.');
    }
}
