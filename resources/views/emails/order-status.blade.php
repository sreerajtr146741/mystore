@extends('emails.layouts.base')

@section('title', 'Order Status Update')

@push('styles')
<style>
    .status-badge { 
        display: inline-block; 
        padding: 12px 24px; 
        border-radius: 24px; 
        font-weight: 700; 
        font-size: 16px; 
        text-transform: uppercase; 
        letter-spacing: 1px;
        margin: 20px 0;
    }
    .status-processing { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; }
    .status-shipped { background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; }
    .status-delivered { background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); color: white; }
    .status-out_for_delivery { background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: #333; }
    
    .order-timeline {
        margin: 30px 0;
        padding: 24px;
        background: #f8f9fa;
        border-radius: 8px;
    }
    .timeline-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-left: 3px solid #e9ecef;
        padding-left: 24px;
        position: relative;
    }
    .timeline-item.active {
        border-left-color: #2874f0;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -9px;
        width: 14px;
        height: 14px;
        background: #e9ecef;
        border-radius: 50%;
    }
    .timeline-item.active::before {
        background: #2874f0;
    }
    .timeline-item.completed::before {
        background: #28a745;
        content: '✓';
        color: white;
        font-size: 10px;
        line-height: 14px;
        text-align: center;
    }
    
    .order-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin: 24px 0;
    }
    .detail-item {
        background: #f8f9fa;
        padding: 16px;
        border-radius: 6px;
    }
    .detail-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .detail-value {
        font-size: 16px;
        color: #333;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
    @php
        $statusConfig = [
            'processing' => [
                'title' => 'Order is Being Processed',
                'icon' => '📦',
                'message' => 'Great news! Your order is being carefully prepared by our team.',
                'class' => 'status-processing'
            ],
            'shipped' => [
                'title' => 'Order Has Been Shipped',
                'icon' => '🚚',
                'message' => 'Your order is on its way! Track your package for real-time updates.',
                'class' => 'status-shipped'
            ],
            'out_for_delivery' => [
                'title' => 'Out for Delivery',
                'icon' => '🏍️',
                'message' => 'Your order will be delivered today. Please be available to receive it.',
                'class' => 'status-out_for_delivery'
            ],
            'delivered' => [
                'title' => 'Order Delivered Successfully',
                'icon' => '✅',
                'message' => 'Your order has been delivered. We hope you love your purchase!',
                'class' => 'status-delivered'
            ],
        ];
        
        $config = $statusConfig[$status] ?? [
            'title' => 'Order Status Updated',
            'icon' => '📋',
            'message' => 'Your order status has been updated.',
            'class' => 'status-processing'
        ];
    @endphp

    <div style="text-align: center;">
        <div style="font-size: 48px; margin: 20px 0;">{{ $config['icon'] }}</div>
        <h2 style="margin: 16px 0;">{{ $config['title'] }}</h2>
    </div>

    <div style="text-align: center; margin: 24px 0;">
        <span class="status-badge {{ $config['class'] }}">{{ strtoupper(str_replace('_', ' ', $status)) }}</span>
    </div>

    <p>Hi <strong>{{ $order->user->name ?? 'Customer' }}</strong>,</p>
    <p>{{ $config['message'] }}</p>

    <div class="order-details-grid">
        <div class="detail-item">
            <div class="detail-label">Order ID</div>
            <div class="detail-value">#{{ $order->id }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Order Date</div>
            <div class="detail-value">{{ $order->created_at->format('M d, Y') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Total Amount</div>
            <div class="detail-value">₹{{ number_format($order->total, 2) }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Items</div>
            <div class="detail-value">{{ $order->items->count() }} item(s)</div>
        </div>
    </div>

    <div class="order-timeline">
        <h3 style="margin: 0 0 20px 0; font-size: 16px; color: #333;">Order Progress</h3>
        <div class="timeline-item {{ in_array($status, ['processing', 'shipped', 'out_for_delivery', 'delivered']) ? 'completed' : '' }}">
            <div>Order Placed</div>
        </div>
        <div class="timeline-item {{ in_array($status, ['processing', 'shipped', 'out_for_delivery', 'delivered']) ? 'completed' : ($status == 'processing' ? 'active' : '') }}">
            <div>Processing</div>
        </div>
        <div class="timeline-item {{ in_array($status, ['shipped', 'out_for_delivery', 'delivered']) ? 'completed' : ($status == 'shipped' ? 'active' : '') }}">
            <div>Shipped</div>
        </div>
        <div class="timeline-item {{ in_array($status, ['out_for_delivery', 'delivered']) ? 'completed' : ($status == 'out_for_delivery' ? 'active' : '') }}">
            <div>Out for Delivery</div>
        </div>
        <div class="timeline-item {{ $status == 'delivered' ? 'completed active' : '' }}">
            <div>Delivered</div>
        </div>
    </div>

    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ config('app.url') }}/orders/{{ $order->id }}" class="btn-primary">Track Your Order</a>
    </div>

    @if($status == 'delivered')
        <div class="success-box">
            <strong>🎉 Enjoy your purchase!</strong><br>
            If you're satisfied with your order, we'd love to hear from you. Consider leaving a review!
        </div>
    @endif

    <p style="margin-top: 30px;">Thank you for shopping with MyStore!</p>
@endsection
