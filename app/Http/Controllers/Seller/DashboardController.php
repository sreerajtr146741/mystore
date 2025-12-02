<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $productsCount = auth()->user()->products()->count();
        $totalSales = auth()->user()->products()->sum('price'); // placeholder

        return view('seller.dashboard', compact('productsCount', 'totalSales'));
    }
}