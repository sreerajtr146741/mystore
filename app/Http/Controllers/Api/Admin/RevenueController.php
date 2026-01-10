<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class RevenueController extends Controller
{
    public function index()
    {
        $totalRevenue = Order::sum('total');
        $revenueByDay = Order::selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();

        return response()->json([
            'status' => true,
            'data' => [
                'total_revenue' => $totalRevenue,
                'daily_revenue' => $revenueByDay
            ]
        ]);
    }
}
