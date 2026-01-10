<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class DashboardController extends Controller
{
    /**
     * Get admin dashboard statistics
     */
    public function index()
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'total_products' => Product::count(),
                'total_orders' => Order::count(),
                'total_revenue' => Order::sum('total'),
                'active_users' => User::where('status', 'active')->count(),
                'pending_orders' => Order::whereIn('status', ['placed', 'processing'])->count(),
                'today_revenue' => Order::whereDate('created_at', today())->sum('total'),
                'today_orders' => Order::whereDate('created_at', today())->count(),
            ];

            // Recent orders
            $recentOrders = Order::with('user')
                ->latest()
                ->take(10)
                ->get();

            // Top products
            $topProducts = Product::withCount('orderItems')
                ->orderBy('order_items_count', 'desc')
                ->take(5)
                ->get();

            // Monthly revenue (last 6 months)
            $monthlyRevenue = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $monthlyRevenue[] = [
                    'month' => $month->format('M Y'),
                    'revenue' => Order::whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->sum('total')
                ];
            }

            return ApiResponse::success([
                'stats' => $stats,
                'recent_orders' => $recentOrders,
                'top_products' => $topProducts,
                'monthly_revenue' => $monthlyRevenue
            ]);
        } catch (Throwable $e) {
            Log::error('API Admin Dashboard failed: ' . $e->getMessage());
            return ApiResponse::error('Failed to load dashboard data', 500);
        }
    }
}
