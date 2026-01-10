<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => [
                'total_users' => User::count(),
                'total_orders' => Order::count(),
                'total_sales' => Order::sum('total'),
                'total_products' => Product::count(),
            ]
        ]);
    }
}
