<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    public function index()
    {
        try {

            // If you have an `orders` table with a `total` column, use this:
            $today  = now()->toDateString();
            $weekStart  = now()->startOfWeek();
            $monthStart = now()->startOfMonth();

            $revenue = [
                'today' => (float) DB::table('orders')->whereDate('created_at', $today)->sum('total'),
                'week'  => (float) DB::table('orders')->whereBetween('created_at', [$weekStart, now()])->sum('total'),
                'month' => (float) DB::table('orders')->whereBetween('created_at', [$monthStart, now()])->sum('total'),
                'growth' => '+0%', // optional, for your dashboard card
            ];

        } catch (\Throwable $e) {

            // Fallback if no orders table yet
            \Log::error('RevenueController error: '.$e->getMessage());

            $revenue = [
                'today'  => 0,
                'week'   => 0,
                'month'  => 0,
                'growth' => '+0%',
            ];
        }

        return view('admin.revenue', compact('revenue'));
    }
}
