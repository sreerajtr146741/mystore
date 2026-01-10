<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderManagementController extends Controller
{
    public function index()
    {
        return response()->json(['status' => true, 'data' => Order::with('items')->get()]);
    }

    public function show($id)
    {
        return response()->json(['status' => true, 'data' => Order::with('items')->find($id)]);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);
        if ($order) {
            $order->update(['status' => $request->status]);
        }
        return response()->json(['status' => true, 'message' => 'Order status updated']);
    }
}
