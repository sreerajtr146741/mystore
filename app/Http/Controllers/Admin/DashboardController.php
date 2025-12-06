<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $stats = [
                'total_users' => \App\Models\User::count(),
                'sellers' => \App\Models\User::where('role', 'seller')->count(),
                'pending_apps' => \App\Models\SellerApplication::where('status', 'pending')->count(),
            ];

            return view('admin.manage', compact('stats'));

        } catch (\Exception $e) {

            // You can log the error
            \Log::error('Dashboard load failed: '.$e->getMessage());

            // Return fallback view or redirect with error message
            return back()->with('error', 'Unable to load Dashboard. Please try again.');
        }
    }
}
