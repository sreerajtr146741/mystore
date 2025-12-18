<!DOCTYPE html>
<html>
<head>
    <title>Order Out for Delivery</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f3f4f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: #f59e0b; color: white; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 30px; }
        .order-info { background: #fffbeb; padding: 15px; border-radius: 6px; margin: 20px 0; border: 1px solid #fde68a; }
        .btn { display: inline-block; background: #f59e0b; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: 500; margin-top: 20px; }
        .footer { text-align: center; padding: 20px; background: #f9fafb; color: #6b7280; font-size: 12px; }
        .status-badge { display: inline-block; background: #fef3c7; color: #b45309; padding: 4px 12px; border-radius: 9999px; font-weight: 500; font-size: 14px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Out for Delivery!</h1>
        </div>
        <div class="content">
            <p>Hi {{ $order->user->name ?? 'Customer' }},</p>
            
            <p>Your order is almost there! Our courier partner is out for delivery with your package today.</p>
            
            <div class="order-info">
                <p style="margin: 0; color: #52525b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Order Number</p>
                <p style="margin: 5px 0 0; font-weight: 600; font-size: 18px;">#{{ $order->id }}</p>
                
                <div style="margin-top: 15px;">
                   <span class="status-badge">Out for Delivery</span>
                </div>
            </div>
            
            <p>Please ensure someone is available to receive the package.</p>
            
            <div style="text-align: center;">
                <a href="{{ route('orders.index') }}" class="btn">View Order Details</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} MyStore. All rights reserved.</p>
            <p>If you have any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>
