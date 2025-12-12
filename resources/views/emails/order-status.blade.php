<!DOCTYPE html>
<html>
<head>
    <title>Order Status Update</title>
</head>
<body>
    <h1>Order Update</h1>
    <p>Dear {{ $order->user->name ?? 'Customer' }},</p>
    
    @if($order->status == 'out_for_delivery')
        <p><strong>BookNow: Congratulations, your order (INV-{{ $order->id }}) is out for delivery!</strong></p>
    @elseif($order->status == 'delivered')
        <p>We are happy to let you know that your order (INV-{{ $order->id }}) has been delivered.</p>
    @elseif($order->status == 'shipped')
        <p>Great news! Your order (INV-{{ $order->id }}) has been shipped and is on its way.</p>
    @elseif($order->status == 'processing')
        <p>We are currently processing your order (INV-{{ $order->id }}). We will notify you when it ships.</p>
    @elseif($order->status == 'placed')
        <p>Thank you for your order! Your order (INV-{{ $order->id }}) has been placed successfully.</p>
    @else
        <p>Your order (INV-{{ $order->id }}) status has been updated to: {{ ucfirst(str_replace('_', ' ', $order->status)) }}.</p>
    @endif
    
    <p>Thank you for shopping with us.</p>
</body>
</html>
