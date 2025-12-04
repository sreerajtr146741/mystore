<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerApplication;

class SellerApplicationController extends Controller
{
    public function index()
    {
        try {

            $applications = SellerApplication::with('user')->latest()->get();
            return view('admin.seller-applications', compact('applications'));

        } catch (\Throwable $e) {

            \Log::error('SellerApplication index error: '.$e->getMessage());

            return back()->with('error', 'Unable to load seller applications.');
        }
    }
}
