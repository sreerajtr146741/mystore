<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function edit()
    {
        try {
            return view('admin.discount-global', [
                'active' => (bool) (int) Setting::get('discount.global.active', 0),
                'type'   => Setting::get('discount.global.type', 'percent'),
                'value'  => Setting::get('discount.global.value', 0),
                'starts' => Setting::get('discount.global.starts_at'),
                'ends'   => Setting::get('discount.global.ends_at'),
            ]);
        } catch (\Exception $e) {

            \Log::error('Discount edit load failed: '.$e->getMessage());

            return back()->with('error', 'Unable to load discount settings.');
        }
    }

    public function update(Request $request)
    {
        try {

            $data = $request->validate([
                'active' => ['nullable','boolean'],
                'type'   => ['required','in:percent,flat'],
                'value'  => ['required','numeric','min:0'],
                'starts' => ['nullable','date'],
                'ends'   => ['nullable','date','after_or_equal:starts'],
            ]);

            Setting::set('discount.global.active', (int)($data['active'] ?? 0));
            Setting::set('discount.global.type', $data['type']);
            Setting::set('discount.global.value', (string)$data['value']);
            Setting::set('discount.global.starts_at', $data['starts'] ?? null);
            Setting::set('discount.global.ends_at', $data['ends'] ?? null);

            return back()->with('success', 'Store wide discount updated.');

        } catch (\Exception $e) {

            \Log::error('Discount update failed: '.$e->getMessage());

            return back()->with('error', 'Something went wrong while saving.');
        }
    }
}
