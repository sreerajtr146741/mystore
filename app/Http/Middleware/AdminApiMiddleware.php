<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\ApiResponse;

class AdminApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return ApiResponse::unauthorized('Authentication required');
        }

        // Check if user has admin role
        if ($request->user()->role !== 'admin') {
            return ApiResponse::forbidden('Admin access required');
        }

        // Check if user account is active
        if (in_array($request->user()->status, ['suspended', 'blocked'])) {
            return ApiResponse::forbidden('Your account has been ' . $request->user()->status);
        }

        return $next($request);
    }
}
