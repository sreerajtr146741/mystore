<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);

        $recentOrders = Order::where('user_id', $user->id)->latest()->take(5)->get();

        return response()->json([
            'status' => true,
            'data' => [
                'user' => $user,
                'recent_orders' => $recentOrders,
                // Add more user-specific dashboard data here
            ]
        ]);
    }
}
