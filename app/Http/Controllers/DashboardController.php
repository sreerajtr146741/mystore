<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Admin Dashboard Statistics
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::sum('total'),
            'active_users' => User::where('status', 'active')->count(),
            'suspended_users' => User::where('status', 'suspended')->count(),
            'blocked_users' => User::where('status', 'blocked')->count(),
            'buyers' => User::where('role', 'buyer')->count(),
            'sellers' => User::where('role', 'seller')->count(),
        ];

        // Recent Orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Top Products (by order count)
        $topProducts = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();

        // Monthly Revenue (last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyRevenue[] = [
                'month' => $month->format('M'),
                'revenue' => Order::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('total')
            ];
        }

        return view('admin.dashboard', compact('stats', 'recentOrders', 'topProducts', 'monthlyRevenue'));
    }
}
