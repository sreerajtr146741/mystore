<!DOCTYPE html>
<html>
<head>
    <title>Order Shipped</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f3f4f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: #0ea5e9; color: white; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 30px; }
        .order-info { background: #f0f9ff; padding: 15px; border-radius: 6px; margin: 20px 0; border: 1px solid #bae6fd; }
        .btn { display: inline-block; background: #0ea5e9; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: 500; margin-top: 20px; }
        .footer { text-align: center; padding: 20px; background: #f9fafb; color: #6b7280; font-size: 12px; }
        .status-badge { display: inline-block; background: #e0f2fe; color: #0369a1; padding: 4px 12px; border-radius: 9999px; font-weight: 500; font-size: 14px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your Order Has Been Shipped!</h1>
        </div>
        <div class="content">
            <p>Hi {{ $order->user->name ?? 'Customer' }},</p>
            
            <p>Great news! Your order is on its way. We've handed your package over to our delivery partner.</p>
            
            <div class="order-info">
                <p style="margin: 0; color: #52525b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Order Number</p>
                <p style="margin: 5px 0 0; font-weight: 600; font-size: 18px;">#{{ $order->id }}</p>
                
                <div style="margin-top: 15px;">
                   <span class="status-badge">Shipped</span>
                </div>
            </div>
            
            <p>You can expect your delivery soon.</p>
            
            <div style="text-align: center;">
                <a href="{{ route('orders.index') }}" class="btn">Track Order</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} MyStore. All rights reserved.</p>
            <p>If you have any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>
