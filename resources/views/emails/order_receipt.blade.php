@extends('emails.layouts.base')

@section('title', 'Order Confirmation - MyStore')

@push('styles')
<style>
    .order-header { background: linear-gradient(135deg, #28a745 0%, #20873a 100%); color: white; padding: 24px; text-align: center; border-radius: 8px; margin-bottom: 30px; }
    .order-header h2 { margin: 0; font-size: 24px; }
    .order-header p { margin: 8px 0 0 0; font-size: 14px; opacity: 0.95; }
    
    .order-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 24px 0; }
    .info-card { background: #f8f9fa; padding: 16px; border-radius: 6px; border-left: 3px solid #2874f0; }
    .info-card strong { display: block; color: #2874f0; font-size: 12px; text-transform: uppercase; margin-bottom: 8px; }
    .info-card p { margin: 4px 0; font-size: 14px; color: #333; }
    
    .product-item { border-bottom: 1px solid #e9ecef; padding: 16px 0; }
    .product-item:last-child { border-bottom: none; }
    .product-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; }
    .product-name { font-weight: 600; color: #333; margin: 0 0 4px 0; flex: 1; }
    .product-meta { font-size: 13px; color: #6c757d; }
    .product-qty { min-width: 80px; text-align: center; color: #6c757d; }
    .product-price { min-width: 100px; text-align: right; font-weight: 600; color: #333; }
    
    .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    .items-table thead { background: #f8f9fa; }
    .items-table th { padding: 12px 16px; text-align: left; font-size: 13px; color: #6c757d; text-transform: uppercase; font-weight: 600; border-bottom: 2px solid #dee2e6; }
    .items-table td { padding: 16px; border-bottom: 1px solid #e9ecef; }
    .items-table tbody tr:last-child td { border-bottom: none; }
    .items-table .text-right { text-align: right; }
    .items-table .text-center { text-align: center; }
    
    .price-summary { background: #f8f9fa; padding: 24px; border-radius: 8px; margin: 24px 0; border: 2px solid #e9ecef; }
    .price-row { display: flex; justify-content: space-between; margin: 10px 0; font-size: 15px; color: #333; }
    .price-row.discount { color: #28a745; font-weight: 600; }
    .price-row.total { border-top: 2px solid #dee2e6; padding-top: 16px; margin-top: 16px; font-size: 20px; font-weight: 700; color: #2874f0; }
    .price-row .label { color: #6c757d; }
    .price-row .value { font-weight: 600; }
    
    @media only screen and (max-width: 600px) {
        .order-info-grid { grid-template-columns: 1fr; }
        .items-table { font-size: 14px; }
        .items-table th, .items-table td { padding: 10px 8px; }
    }
</style>
@endpush

@section('content')
    <div class="order-header">
        <h2>✅ Order Confirmed!</h2>
        <p>Thank you for shopping with MyStore</p>
    </div>

    <p>Hi <strong>{{ $buyer['full_name'] ?? 'Customer' }}</strong>,</p>
    <p>Your order has been successfully placed and is being processed. We'll send you another email when your items are shipped.</p>

    <div class="order-info-grid">
        <div class="info-card">
            <strong>📦 Delivery Address</strong>
            <p>{{ $buyer['full_name'] ?? '' }}</p>
            <p>{{ $buyer['address'] ?? '' }}</p>
            <p>{{ $buyer['phone'] ?? '' }}</p>
        </div>
        <div class="info-card">
            <strong>📧 Contact Information</strong>
            <p>{{ $buyer['email'] ?? '' }}</p>
            <p>{{ $buyer['phone'] ?? '' }}</p>
        </div>
    </div>

    <h2>📋 Order Summary</h2>
    
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">Item</th>
                <th class="text-center" style="width: 15%;">Qty</th>
                <th class="text-right" style="width: 20%;">Price</th>
                <th class="text-right" style="width: 15%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #333;">{{ $item['name'] ?? 'Item' }}</div>
                    </td>
                    <td class="text-center" style="color: #6c757d;">{{ (int)($item['qty'] ?? 1) }}</td>
                    <td class="text-right">₹{{ number_format((float)($item['price'] ?? 0), 2) }}</td>
                    <td class="text-right" style="font-weight: 600;">₹{{ number_format((float)($item['line_total'] ?? ((float)($item['price'] ?? 0) * (int)($item['qty'] ?? 1))), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="price-summary">
        <h3 style="margin: 0 0 20px 0; font-size: 16px; color: #333; text-transform: uppercase; letter-spacing: 0.5px;">Price Details</h3>
        
        <div class="price-row">
            <span class="label">Price ({{ count($items) }} item{{ count($items) > 1 ? 's' : '' }})</span>
            <span class="value">₹{{ number_format((float)$subtotal, 2) }}</span>
        </div>
        
        @if(isset($discount) && $discount > 0)
        <div class="price-row discount">
            <span class="label">Discount</span>
            <span class="value">− ₹{{ number_format((float)$discount, 2) }}</span>
        </div>
        @endif
        
        @if(isset($platform_fee) && $platform_fee > 0)
        <div class="price-row">
            <span class="label">Platform Fee</span>
            <span class="value">₹{{ number_format((float)$platform_fee, 2) }}</span>
        </div>
        @endif
        
        <div class="price-row">
            <span class="label">Delivery Charges</span>
            <span class="value" style="color: #28a745; font-weight: 600;">{{ $shipping > 0 ? '₹'.number_format((float)$shipping, 2) : 'FREE' }}</span>
        </div>
        
        <div class="price-row total">
            <span>Total Amount</span>
            <span>₹{{ number_format((float)$total, 2) }}</span>
        </div>
        
        @if(isset($discount) && $discount > 0)
        <div style="background: #d4edda; padding: 12px; border-radius: 6px; margin-top: 16px; color: #155724; text-align: center; font-weight: 600;">
            🎉 You saved ₹{{ number_format((float)$discount, 2) }} on this order!
        </div>
        @endif
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ config('app.url') }}/orders" class="btn-primary">View Order Details</a>
    </div>

    <div class="info-box">
        <strong>📱 Track Your Order</strong>
        <p style="margin-top: 8px;">You can track your order status anytime by logging into your MyStore account and visiting the Orders page.</p>
    </div>

    <div class="warning-box">
        <strong>⚠️ Important:</strong> Please keep this email for your records. You may need it for returns or exchanges.
    </div>

    <p style="margin-top: 30px;">Thank you for choosing MyStore. If you have any questions, feel free to reply to this email.</p>
@endsection
