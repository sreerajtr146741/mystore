<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class OrderManagementController extends Controller
{
    /**
     * List all orders with filters
     */
    public function index(Request $request)
    {
        $query = Order::with('user');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search by order ID or user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', $search)
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate($request->get('per_page', 15));

        return ApiResponse::success($orders);
    }

    /**
     * Get order details
     */
    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->find($id);

        if (!$order) {
            return ApiResponse::notFound('Order not found');
        }

        return ApiResponse::success(['order' => $order]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:placed,processing,shipped,delivered,cancelled,return_requested,returned'
        ]);

        $order = Order::find($id);

        if (!$order) {
            return ApiResponse::notFound('Order not found');
        }

        $order->update([
            'status' => $request->status,
            $request->status . '_at' => now()
        ]);

        return ApiResponse::success(
            ['order' => $order],
            'Order status updated successfully'
        );
    }
}
