<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Prefer single-product stash; else use cart
        $items = session('checkout_items');
        if (empty($items)) {
            $items = collect(session('cart', []))->values()->all();
        }

        // Normalize for blade
        $items = collect($items)->map(function ($it) {
            $qty   = (int)($it['qty'] ?? 1);
            $price = (float)($it['price'] ?? 0);
            return array_merge($it, [
                'qty'        => $qty,
                'price'      => $price,
                'line_total' => $qty * $price,
            ]);
        })->values()->all();

        return view('checkout.index', compact('items'));
    }

    public function process(Request $request)
    {
        // Load items from stash or cart
        $items  = session('checkout_items');
        $source = 'checkout_items';

        if (empty($items)) {
            $items  = collect(session('cart', []))->values()->all();
            $source = 'cart';
        }

        if (empty($items)) {
            return redirect()->route('checkout')->with('error', 'Your cart is empty.');
        }

        // Validate buyer details only (totals are recomputed)
        $buyer = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone'     => 'required|string|max:30',
            'email'     => 'required|email|max:255',
            'address'   => 'required|string|max:500',
        ]);

        // Recompute totals server-side
        $items = collect($items)->map(function ($it) {
            $qty   = (int)($it['qty'] ?? 1);
            $price = (float)($it['price'] ?? 0);
            return array_merge($it, [
                'qty'        => $qty,
                'price'      => $price,
                'line_total' => $qty * $price,
            ]);
        })->values()->all();

        $subtotal = collect($items)->sum('line_total');
        $shipping = $subtotal < 300 && $subtotal > 0 ? 59.0 : 0.0;
        $total    = $subtotal + $shipping;

        // Choose recipient: logged-in user preferred, else form email
        $user    = $request->user();
        $toEmail = optional($user)->email ?: $buyer['email'];
        $toName  = optional($user)->name  ?: $buyer['full_name'];

        // Try to send receipt (only on success do we clear items)
        $emailSent = false;
        try {
            Mail::send('emails.order_receipt', compact('buyer','items','subtotal','shipping','total'), function ($m) use ($toEmail, $toName) {
                $m->to($toEmail, $toName)->subject('Your MyStore Order Receipt');
            });
            $emailSent = true;
        } catch (\Throwable $e) {
            Log::error('Email send failed: ' . $e->getMessage());
        }

        if ($emailSent) {
            if ($source === 'checkout_items') {
                session()->forget('checkout_items'); // keep cart intact
            } else {
                session()->forget('cart'); // clear full cart
            }
            return redirect()->route('checkout.success')->with('success', 'Payment successful! Receipt emailed.');
        }

        // Email failed → keep items and stay on page
        return back()->withInput()->with('error', 'Could not send the receipt email. Please check your email and try again.');
    }

    public function success()
    {
        return view('checkout.success');
    }

    public function cancel(Request $request)
    {
        return redirect()->route('cart.index')->with('success', 'Checkout cancelled.');
    }
}
