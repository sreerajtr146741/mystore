<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Normalize and fetch current checkout items (prefer checkout_items over cart).
     */
    private function currentItems(): array
    {
        $items = session('checkout_items');
        if (empty($items)) {
            $items = collect(session('cart', []))->values()->all();
        }

        return collect($items)->map(function ($it) {
            $qty   = (int)($it['qty'] ?? 1);
            $price = (float)($it['price'] ?? 0);
            return array_merge($it, [
                'qty'        => $qty,
                'price'      => $price,
                'line_total' => $qty * $price,
            ]);
        })->values()->all();
    }

    /**
     * Compute totals with coupons + shipping rules.
     */
    private function totals(array $items): array
    {
        $subtotal = collect($items)->sum('line_total');

        $shipping = ($subtotal > 0 && $subtotal < 300) ? 59.0 : 0.0;
        $platformFee = ($subtotal > 0) ? 10.0 : 0.0;

        $percent      = (float) session('discount_percent', 0);
        $flat         = (float) session('discount_amount', 0);
        $freeShipping = (bool)  session('free_shipping', false);

        if ($freeShipping) {
            $shipping = 0.0;
        }

        $percentOff = $percent > 0 ? ($subtotal * ($percent / 100.0)) : 0.0;
        $flatOff    = $flat > 0 ? $flat : 0.0;

        $discount = $percentOff + $flatOff;

        if ($discount > $subtotal) {
            $discount = $subtotal;
        }

        $total = max(0.0, ($subtotal - $discount) + $shipping + $platformFee);

        return [
            'subtotal'     => round($subtotal, 2),
            'shipping'     => round($shipping, 2),
            'platform_fee' => round($platformFee, 2),
            'discount'     => round($discount, 2),
            'total'        => round($total, 2),
        ];
    }

    public function index(Request $request)
    {
        try {

            $items  = $this->currentItems();
            $totals = $this->totals($items);

            return view('checkout.index', [
                'items'        => $items,
                'subtotal'     => $totals['subtotal'],
                'shipping'     => $totals['shipping'],
                'platform_fee' => $totals['platform_fee'],
                'discount'     => $totals['discount'],
                'total'        => $totals['total'],
                'coupon'       => session('coupon_code'),
            ]);

        } catch (\Throwable $e) {

            Log::error('Checkout index error: '.$e->getMessage());
            return back()->with('error', 'Unable to load checkout.');
        }
    }

    public function proceed(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone'     => 'required|string|max:30',
            'email'     => 'required|email|max:255',
            'address'   => 'required|string|max:500',
        ]);

        // Save address to session for the next step
        session(['checkout_address' => $data]);

        return redirect()->route('checkout.payment');
    }

    public function payment()
    {
        $items = $this->currentItems();
        if (empty($items)) {
            return redirect()->route('products.index');
        }

        $totals = $this->totals($items);

        return view('checkout.payment', [
            'items'        => $items,
            'subtotal'     => $totals['subtotal'],
            'shipping'     => $totals['shipping'],
            'platform_fee' => $totals['platform_fee'],
            'discount'     => $totals['discount'],
            'total'        => $totals['total'],
            'address'      => session('checkout_address'),
            'user'         => auth()->user()
        ]);
    }

    public function process(Request $request)
    {
        try {

            $source = session()->has('checkout_items') ? 'checkout_items' : 'cart';

            $items = $this->currentItems();
            if (empty($items)) {
                return redirect()->route('checkout')->with('error', 'Your cart is empty.');
            }

            $buyer = session('checkout_address'); // Get from session instead of request validation here
            if (!$buyer) {
                 // Fallback validation if session expired or direct hit
                 $buyer = $request->validate([
                    'full_name' => 'required|string|max:255',
                    'phone'     => 'required|string|max:30',
                    'email'     => 'required|email|max:255',
                    'address'   => 'required|string|max:500',
                ]);
            }

            $totals = $this->totals($items);
            $subtotal = $totals['subtotal'];
            $shipping = $totals['shipping'];
            $platform_fee = $totals['platform_fee'];
            $discount = $totals['discount'];
            $total    = $totals['total'];

            $user    = $request->user();
            $toEmail = optional($user)->email ?: $buyer['email'];
            $toName  = optional($user)->name  ?: $buyer['full_name'];

            $emailSent = false;

            try {
                Mail::send(
                    'emails.order_receipt',
                    compact('buyer','items','subtotal','shipping','platform_fee','discount','total'),
                    function ($m) use ($toEmail, $toName) {
                        $m->to($toEmail, $toName)->subject('Your MyStore Order Receipt');
                    }
                );
                $emailSent = true;
            } catch (\Throwable $e) {
                Log::error('Email send failed: ' . $e->getMessage());
            }

            if ($emailSent) {
                if ($source === 'checkout_items') {
                    session()->forget('checkout_items');
                } else {
                    // USER REQUEST: Keep items in cart after payment
                    // session()->forget('cart'); 
                }

                session()->forget(['discount_percent','discount_amount','coupon_code','free_shipping']);

                return redirect()->route('checkout.success')
                                 ->with('success', 'Payment successful! Receipt emailed.');
            }

            return back()->withInput()->with('error', 'Could not send the receipt email. Please try again.');

        } catch (\Throwable $e) {

            Log::error('Checkout process error: '.$e->getMessage());
            return back()->with('error', 'Checkout failed: '.$e->getMessage());
        }
    }

    public function success()
    {
        try {
            return view('checkout.success');
        } catch (\Throwable $e) {
            Log::error('Checkout success view error: '.$e->getMessage());
            return back()->with('error', 'Unable to load success page.');
        }
    }

    public function cancel(Request $request)
    {
        try {
            return redirect()->route('products.index')->with('success', 'Checkout cancelled. Your items are still saved.');
        } catch (\Throwable $e) {
            Log::error('Checkout cancel error: '.$e->getMessage());
            return back()->with('error', 'Unable to cancel checkout.');
        }
    }

    /**
     * Apply a coupon code to current checkout/cart.
     */
    public function applyCoupon(Request $request)
    {
        try {

            $data = $request->validate([
                'coupon_code' => ['required','string','max:32']
            ]);

            $code = strtoupper(trim($data['coupon_code']));

            $items    = $this->currentItems();
            $subtotal = collect($items)->sum('line_total');

            if ($subtotal <= 0) {
                return back()->with('error', 'Your cart is empty.');
            }

            $coupons = [
                'SAVE10'   => ['type' => 'percent', 'value' => 10,  'min' => 0],
                'FLAT150'  => ['type' => 'flat',    'value' => 150, 'min' => 500],
                'FREESHIP' => ['type' => 'ship',    'value' => 59,  'min' => 299],
            ];

            if (!array_key_exists($code, $coupons)) {
                return back()->with('error', 'Invalid coupon code.');
            }

            $c = $coupons[$code];
            if ($subtotal < $c['min']) {
                return back()->with('error', "Coupon requires minimum order ₹{$c['min']}.");
            }

            session()->forget(['discount_percent','discount_amount','coupon_code','free_shipping']);

            if ($c['type'] === 'percent') {
                session([
                    'discount_percent' => (float)$c['value'],
                    'coupon_code'      => $code,
                ]);
            } elseif ($c['type'] === 'flat') {
                session([
                    'discount_amount'  => (float)$c['value'],
                    'coupon_code'      => $code,
                ]);
            } elseif ($c['type'] === 'ship') {
                session([
                    'free_shipping'    => true,
                    'coupon_code'      => $code,
                ]);
            }

            return back()->with('success', 'Coupon applied!');

        } catch (\Throwable $e) {

            Log::error('Apply coupon error: '.$e->getMessage());
            return back()->with('error', 'Failed to apply coupon.');
        }
    }

    public function removeCoupon(Request $request)
    {
        try {
            session()->forget(['discount_percent','discount_amount','coupon_code','free_shipping']);
            return back()->with('success', 'Coupon removed.');
        } catch (\Throwable $e) {
            Log::error('Remove coupon error: '.$e->getMessage());
            return back()->with('error', 'Failed to remove coupon.');
        }
    }

    public function removeItem(Request $request, $id)
    {
        try {
            // Check where the items are coming from
            if (session()->has('checkout_items')) {
                $items = session('checkout_items', []);
                // checkout_items is an indexed array, need to find by id field
                $items = array_values(array_filter($items, function($item) use ($id) {
                    return $item['id'] != $id;
                }));
                
                if (empty($items)) {
                    session()->forget('checkout_items');
                    return redirect()->route('products.index')->with('success', 'Item removed. Returning to shop.');
                }
                
                session(['checkout_items' => $items]);
            } else {
                // Fallback to removing from cart (associative array keyed by ID)
                $cart = session('cart', []);
                if (isset($cart[$id])) {
                    unset($cart[$id]);
                    session(['cart' => $cart]);
                }
            }
            
            return back()->with('success', 'Item removed from checkout.');

        } catch (\Throwable $e) {
            Log::error('Checkout remove item error: '.$e->getMessage());
            return back()->with('error', 'Unable to remove item.');
        }
    }

    public function updateQuantity(Request $request, $id)
    {
        try {
            $qty = (int)$request->input('qty', 1);
            if ($qty < 1) $qty = 1;

            if (session()->has('checkout_items')) {
                $items = session('checkout_items', []);
                // checkout_items is an indexed array, need to find by id field
                foreach ($items as $index => $item) {
                    if (isset($item['id']) && $item['id'] == $id) {
                        $items[$index]['qty'] = $qty;
                        break;
                    }
                }
                session(['checkout_items' => $items]);
            } else {
                // cart is an associative array keyed by ID
                $cart = session('cart', []);
                if (isset($cart[$id])) {
                    $cart[$id]['qty'] = $qty;
                    session(['cart' => $cart]);
                }
            }

            $currentItems = $this->currentItems();
            $totals = $this->totals($currentItems);

            return response()->json([
                'success' => true,
                'totals'  => $totals
            ]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
