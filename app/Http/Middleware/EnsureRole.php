<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    /**
     * Use as: ->middleware('role:admin') or 'role:seller' or 'role:admin,seller'
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        if (count($roles) === 1 && str_contains($roles[0], ',')) {
            $roles = array_map('trim', explode(',', $roles[0]));
        }

        $userRole = $user->role ?? null;

        foreach ($roles as $role) {
            if ($userRole === $role) {
                return $next($request);
            }
            if ($role === 'admin'  && method_exists($user, 'isAdmin')  && $user->isAdmin())  return $next($request);
            if ($role === 'seller' && method_exists($user, 'isSeller') && $user->isSeller()) return $next($request);
            if ($role === 'user'   && method_exists($user, 'isUser')   && $user->isUser())   return $next($request);
        }

        abort(403, 'You do not have permission to access this resource.');
    }
}
