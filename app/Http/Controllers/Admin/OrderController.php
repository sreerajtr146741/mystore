<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusUpdated;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = Order::with(['user', 'items.product'])->latest();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $idSearch = str_ireplace('INV-', '', $search);
            $query->where(function($q) use ($search, $idSearch) {
                $q->where('id', 'like', "%$idSearch%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                  });
            });
        }

        // Filter by Status (if not 'all')
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(12)->withQueryString();

        if ($request->ajax()) {
            return view('admin.orders.partials.row', compact('orders'))->render();
        }

        // Calculate Counts
        $counts = [
            'all' => Order::count(),
            'placed' => Order::where('status', 'placed')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'return_requested' => Order::where('status', 'return_requested')->count(),
            'returned' => Order::where('status', 'returned')->count(),
        ];

        return view('admin.orders', compact('orders', 'counts', 'status'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        return view('admin.orders.invoice', compact('order'));
    }

    public function downloadInvoice($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));
        return $pdf->download('invoice-INV-'.$order->id.'.pdf');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
        ]);

        $order = Order::with('user')->findOrFail($id);
        $order->status = $request->input('status');
        $order->save();

        if ($order->user && $order->user->email) {
            try {
                if ($order->status === 'delivered') {
                    // Generate PDF for attachment
                    // Note: We need to load relations if not already loaded, but findOrFail above does it? 
                    // No, line 72 only loads 'user'. load items.product for invoice view
                    $order->load(['items.product']);
                    $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));
                    $pdfContent = $pdf->output();

                    Mail::to($order->user->email)->send(new \App\Mail\OrderDeliveredInvoice($order, $pdfContent));
                } else {
                    Mail::to($order->user->email)->send(new OrderStatusUpdated($order));
                }
            } catch (\Exception $e) {
                \Log::error('Mail sending failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Order updated to ' . ucfirst($order->status) . ' and email sent.');
    }
}
