<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
        // Use as: ->middleware('role:admin') or ->middleware('role:admin,seller')
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if (!$user) abort(401);

        $role = $user->role ?? null;
        if (!$role || !in_array($role, $roles, true)) abort(403, 'Unauthorized.');

        return $next($request);
    }
}
