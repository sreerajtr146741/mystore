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
            // Order Status Counts
            'orders_placed' => Order::where('status', 'placed')->count(),
            'orders_processing' => Order::where('status', 'processing')->count(),
            'orders_shipped' => Order::where('status', 'shipped')->count(),
            'orders_delivered' => Order::where('status', 'delivered')->count(),
            // Today's data
            'new_today' => Product::whereDate('created_at', today())->count(),
            'today_revenue' => Order::whereDate('created_at', today())->sum('total'),
        ];

        // Alerts (low stock products)
        $alerts = [
            'low_stock' => Product::where('stock', '<=', 10)->where('status', 'active')->count(),
        ];

        // User Statistics
        $userStats = [
            'buyers' => User::where('role', 'buyer')->count(),
            'new_today' => User::whereDate('created_at', today())->count(),
            'admins' => User::where('role', 'admin')->count(),
            'sellers' => User::where('role', 'seller')->count(),
            'active_30d' => User::where('role', 'buyer')
                ->where('updated_at', '>=', now()->subDays(30))
                ->count(),
            'growth' => '+0%', // You can calculate actual growth if needed
        ];

        // Revenue growth (comparing today vs yesterday)
        $yesterdayRevenue = Order::whereDate('created_at', today()->subDay())->sum('total');
        $todayRevenue = $stats['today_revenue'];
        $revenueGrowth = $yesterdayRevenue > 0 
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 2) 
            : 0;
        $revenue = [
            'growth' => ($revenueGrowth >= 0 ? '+' : '') . $revenueGrowth . '%',
        ];

        // Admin extras
        $adminExtras = [
            'pending_orders' => Order::whereIn('status', ['placed', 'processing'])->count(),
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

        // For Buyers/Sellers visiting the general /dashboard
        // We use layouts.index which is a multi-role dashboard.
        
        $sellerStats = [];
        $sellerProducts = [];
        
        if ($user && $user->isSeller()) {
             $sellerProducts = Product::where('user_id', $user->id)->get();
             $sellerStats = [
                 'count'       => $sellerProducts->count(),
                 'total_value' => $sellerProducts->sum('price'),
                 'low_stock'   => $sellerProducts->where('stock', '<=', 5)->count(),
             ];
        }

        return view('layouts.index', compact('stats', 'sellerStats', 'sellerProducts'));
    }
}
