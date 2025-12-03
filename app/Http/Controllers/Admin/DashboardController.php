<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // app/Http/Controllers/Admin/DashboardController.php

public function index()
{
    $stats = [
        'total_users'     => \App\Models\User::count(),
        'sellers'         => \App\Models\User::where('role', 'seller')->count(),
        'total_products'  => \App\Models\Product::count(),
        'pending_sellers' => \App\Models\SellerApplication::where('status', 'pending')->count(),
        'today_revenue'   => 124500,   // ← Replace with real logic later
        'new_today'       => \App\Models\Product::whereDate('created_at', today())->count(),
    ];

    return view('admin.dashboard', compact('stats'));
}
}
