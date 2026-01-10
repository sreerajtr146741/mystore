<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellerApplication;

class SellerApplicationController extends Controller
{
    public function index()
    {
        $applications = SellerApplication::with('user')->where('status', 'pending')->get();
        return response()->json(['status' => true, 'data' => $applications]);
    }

    public function approve($id)
    {
        $application = SellerApplication::find($id);
        if (!$application) return response()->json(['status' => false, 'message' => 'Application not found'], 404);

        $application->update(['status' => 'approved']);
        $application->user->update(['role' => 'seller']);

        return response()->json(['status' => true, 'message' => 'Application approved']);
    }

    public function reject($id)
    {
        $application = SellerApplication::find($id);
        if (!$application) return response()->json(['status' => false, 'message' => 'Application not found'], 404);

        $application->update(['status' => 'rejected']);

        return response()->json(['status' => true, 'message' => 'Application rejected']);
    }
}
