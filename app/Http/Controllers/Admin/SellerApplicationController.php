<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SellerApplication;
class SellerApplicationController extends Controller {
    public function index() {
        $applications = SellerApplication::with('user')->latest()->get();
        return view('admin.seller-applications', compact('applications'));
    }
}