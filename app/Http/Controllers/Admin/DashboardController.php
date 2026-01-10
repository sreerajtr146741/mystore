<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\SellerApplication;
use App\Models\ContactMessage;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Collect dashboard statistics
            $stats = [
                'total_users'       => User::count(),
                'total_products'    => Product::count(),
                'total_orders'      => Order::count(),
                'total_revenue'     => Order::sum('total'),
                'active_users'      => User::where('status', 'active')->count(),
                'suspended_users'   => User::where('status', 'suspended')->count(),
                'blocked_users'     => User::where('status', 'blocked')->count(),
                'buyers'            => User::where('role', 'buyer')->count(),
                'sellers'           => User::where('role', 'seller')->count(),
                'pending_apps'      => SellerApplication::where('status', 'pending')->count(),
                
                // Order Status Counts
                'orders_placed'     => Order::where('status', 'placed')->count(),
                'orders_processing' => Order::where('status', 'processing')->count(),
                'orders_shipped'    => Order::where('status', 'shipped')->count(),
                'orders_delivered'  => Order::where('status', 'delivered')->count(),
                
                // Today's data
                'new_today'         => Product::whereDate('created_at', today())->count(),
                'today_revenue'     => Order::whereDate('created_at', today())->sum('total'),
            ];

            // Alerts (low stock products)
            $alerts = [
                'low_stock' => Product::where('stock', '<=', 10)->where('status', 'active')->count(),
            ];

            // User Statistics
            $userStats = [
                'buyers'    => User::where('role', 'buyer')->count(),
                'new_today' => User::whereDate('created_at', today())->count(),
                'admins'    => User::where('role', 'admin')->count(),
                'sellers'   => User::where('role', 'seller')->count(),
                'active_30d' => User::where('role', 'buyer')
                    ->where('updated_at', '>=', now()->subDays(30))
                    ->count(),
                'growth'    => '+0%',
            ];

            // Revenue growth (today vs yesterday)
            $yesterdayRevenue = Order::whereDate('created_at', today()->subDay())->sum('total');
            $todayRevenue     = $stats['today_revenue'];
            $revenueGrowth    = $yesterdayRevenue > 0 
                ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 2) 
                : 0;
            $revenue = [
                'growth' => ($revenueGrowth >= 0 ? '+' : '') . $revenueGrowth . '%',
            ];

            // Admin extras
            $adminExtras = [
                'pending_orders'   => Order::whereIn('status', ['placed', 'processing'])->count(),
                'pending_messages' => ContactMessage::doesntHave('replies')->count(),
            ];

            // Recent Orders
            $recentOrders = Order::with('user')
                ->latest()
                ->take(5)
                ->get();

            // Top Products
            $topProducts = Product::withCount('orderItems')
                ->orderBy('order_items_count', 'desc')
                ->take(5)
                ->get();

            // Monthly Revenue (last 6 months)
            $monthlyRevenue = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $monthlyRevenue[] = [
                    'month'   => $month->format('M'),
                    'revenue' => Order::whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->sum('total')
                ];
            }

            // Load latest premium dashboard view
            return view('admin.dashboard', compact(
                'stats', 'recentOrders', 'topProducts', 'monthlyRevenue', 
                'alerts', 'userStats', 'revenue', 'adminExtras'
            ));

        } catch (Throwable $e) {
            Log::error('Admin Dashboard load failed: ' . $e->getMessage());
            return back()->with('error', 'Unable to load Dashboard. Please try again.');
        }
    }
}
