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

    public function index(Request $request)
    {
            // View Orders - List
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product'])
            ->latest()
            ->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('orders.partials.card', compact('orders'))->render(),
                'next_url' => $orders->nextPageUrl()
            ]);
        }
            
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

    public function downloadInvoice($id)
    {
        $order = Order::where('user_id', auth()->id())
            ->with(['user', 'items.product'])
            ->findOrFail($id);

        if ($order->status !== 'delivered') {
            abort(403, 'Invoice is available only after delivery.');
        }
            
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.orders.invoice', compact('order'));
        return $pdf->download('invoice-INV-'.$order->id.'.pdf');
    }

    public function cancel($id)
    {
        $order = Order::where('user_id', auth()->id())->findOrFail($id);

        if (!in_array($order->status, ['placed', 'processing'])) {
            return back()->with('error', 'Order cannot be cancelled at this stage.');
        }

        $order->update(['status' => 'cancelled']);
        return back()->with('success', 'Order has been cancelled successfully.');
    }

    public function requestReturn($id)
    {
        $order = Order::where('user_id', auth()->id())->findOrFail($id);

        if ($order->status !== 'delivered') {
            return back()->with('error', 'Return can only be requested for delivered orders.');
        }

        $order->update(['status' => 'return_requested']);
        return back()->with('success', 'Return request has been submitted.');
    }
}
