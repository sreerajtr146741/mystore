<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
{
    $stats = [
        'total_users' => \App\Models\User::count(),
        'sellers' => \App\Models\User::where('role', 'seller')->count(),
        'pending_apps' => \App\Models\SellerApplication::where('status', 'pending')->count(),
    ];
    return view('admin.dashboard', compact('stats'));
}
}
