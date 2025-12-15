<!DOCTYPE html>
<html>
<head>
    <title>Order Delivered</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4f46e5; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; border-radius: 0 0 8px 8px; }
        .btn { display: inline-block; background: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px; }
        .footer { text-align: center; margin-top: 20px; font-size: 0.8em; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Delivered!</h1>
        </div>
        <div class="content">
            <p>Hi {{ $order->user->name }},</p>
            
            <p>Great news! Your order <strong>#{{ $order->id }}</strong> has been delivered successfully.</p>
            
            <p>We hope you enjoy your purchase.</p>
            
            <p><strong>Invoice Attached:</strong> Please find your official invoice attached to this email.</p>
            
            <p>Thank you for shopping with us!</p>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ route('products.index') }}" class="btn">Shop Again</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} MyStore. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
