<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\SellerApplication;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {

            $user  = $request->user();
            $today = now()->toDateString();

            // ========= ADMIN METRICS (only if admin) =========
            $stats = [];
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                $stats = [
                    'total_users'    => User::count(),
                    'total_products' => Product::count(),
                    'new_today'      => Product::whereDate('created_at', $today)->count(),
                    'sellers'        => User::where('role', 'seller')->count(),
                    'today_revenue'  => 0, // TODO: replace with Orders::whereDate(...)->sum('amount')
                ];
            }

            // ========= SELLER METRICS (only if seller) =========
            $sellerStats    = [];
            $sellerProducts = collect();
            if (method_exists($user, 'isSeller') && $user->isSeller()) {
                $sellerProducts = Product::where('user_id', $user->id)
                    ->latest()
                    ->get(['id','name','price','stock','created_at','updated_at']);

                $sellerStats = [
                    'count'       => $sellerProducts->count(),
                    // If your price is per-unit, multiply by stock if you want inventory value.
                    'total_value' => $sellerProducts->sum('price'),
                    'low_stock'   => $sellerProducts->where('stock', '<=', 5)->count(),
                ];
            }

            // ========= COMMON DASHBOARD BLOCKS =========
            $userStats = [
                'total'     => User::count(),
                'admins'    => User::where('role','admin')->count(),
                'sellers'   => User::where('role','seller')->count(),
                'buyers'    => User::whereIn('role',['user','buyer'])->count(),
                'new_today' => User::whereDate('created_at', $today)->count(),
                'growth'    => '+12%', // TODO: compute vs previous period
            ];

            $revenue = [
                'today'  => $stats['today_revenue'] ?? 0,
                'week'   => 0, // TODO
                'month'  => 0, // TODO
                'growth' => '+28%', // demo
            ];

            $adminExtras = [
                'pending_apps' => SellerApplication::where('status','pending')->count(),
            ];

            $alerts = [
                // For admins: global low stock; for sellers: only their products
                'low_stock' => (method_exists($user, 'isSeller') && $user->isSeller())
                    ? Product::where('user_id', $user->id)->where('stock','<=',5)->count()
                    : Product::where('stock','<=',5)->count(),
            ];

            return view(
                'dashboard.index',
                compact('stats','sellerStats','sellerProducts','userStats','revenue','adminExtras','alerts')
            );

        } catch (\Throwable $e) {

            \Log::error('DashboardController index error: '.$e->getMessage());
            return back()->with('error', 'Unable to load dashboard.');
        }
    }
}
