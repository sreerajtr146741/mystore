<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SellerApplication;
use Exception;
use Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Collect dashboard statistics
            $stats = [
                'total_users'  => User::count(),
                'sellers'      => User::where('role', 'seller')->count(),
                'pending_apps' => SellerApplication::where('status', 'pending')->count(),
            ];

            // Load dashboard view
            return view('admin.manage', compact('stats'));

        } catch (Exception $e) {

            // Log error for debugging
            Log::error('Dashboard load failed', [
                'error' => $e->getMessage(),
            ]);

            // Show friendly error message
            return back()->with(
                'error',
                'Unable to load Dashboard. Please try again.'
            );
        }
    }
}
