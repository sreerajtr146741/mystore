<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Get user's order history
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return ApiResponse::success($orders);
    }

    /**
     * Get order details
     */
    public function show(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with('items.product')
            ->find($id);

        if (!$order) {
            return ApiResponse::notFound('Order not found');
        }

        return ApiResponse::success(['order' => $order]);
    }

    /**
     * Cancel an order
     */
    public function cancel(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)->find($id);

        if (!$order) {
            return ApiResponse::notFound('Order not found');
        }

        // Only allow cancellation for placed or processing orders
        if (!in_array($order->status, ['placed', 'processing'])) {
            return ApiResponse::error('Order cannot be cancelled at this stage', 400);
        }

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        // Restore product stock
        foreach ($order->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        return ApiResponse::success(
            ['order' => $order->fresh()],
            'Order cancelled successfully'
        );
    }

    /**
     * Request order return
     */
    public function requestReturn(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $order = Order::where('user_id', $request->user()->id)->find($id);

        if (!$order) {
            return ApiResponse::notFound('Order not found');
        }

        // Only allow return for delivered orders
        if ($order->status !== 'delivered') {
            return ApiResponse::error('Only delivered orders can be returned', 400);
        }

        // Check if order is within return period (e.g., 7 days)
        $deliveredAt = $order->delivered_at ?? $order->updated_at;
        $returnDeadline = $deliveredAt->addDays(7);

        if (now()->greaterThan($returnDeadline)) {
            return ApiResponse::error('Return period has expired', 400);
        }

        $order->update([
            'status' => 'return_requested',
            'return_reason' => $request->reason,
            'return_requested_at' => now()
        ]);

        return ApiResponse::success(
            ['order' => $order->fresh()],
            'Return request submitted successfully'
        );
    }
}
