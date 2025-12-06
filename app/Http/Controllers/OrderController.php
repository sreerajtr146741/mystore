<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // View Orders - List
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product'])
            ->latest()
            ->paginate(10);
            
        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        // View Orders - Details (Flipkart style)
        $order = Order::where('user_id', auth()->id())
            ->with(['items.product'])
            ->findOrFail($id);
            
        return view('orders.show', compact('order'));
    }
}
