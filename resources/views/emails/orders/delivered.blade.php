<!DOCTYPE html>
<html>
<head>
    <title>Order Delivered</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f3f4f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: #10b981; color: white; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 30px; }
        .order-info { background: #ecfdf5; padding: 15px; border-radius: 6px; margin: 20px 0; border: 1px solid #a7f3d0; }
        .btn { display: inline-block; background: #10b981; color: white; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: 500; margin-top: 20px; }
        .footer { text-align: center; padding: 20px; background: #f9fafb; color: #6b7280; font-size: 12px; }
        .status-badge { display: inline-block; background: #d1fae5; color: #047857; padding: 4px 12px; border-radius: 9999px; font-weight: 500; font-size: 14px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Delivered!</h1>
        </div>
        <div class="content">
            <p>Hi {{ $order->user->name ?? 'Customer' }},</p>
            
            <p>We are happy to let you know that your order has been delivered successfully.</p>
            
            <div class="order-info">
                <p style="margin: 0; color: #52525b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Order Number</p>
                <p style="margin: 5px 0 0; font-weight: 600; font-size: 18px;">#{{ $order->id }}</p>
                
                <div style="margin-top: 15px;">
                   <span class="status-badge">Delivered</span>
                </div>
            </div>
            
            <p>We hope you love your purchase! Thank you for choosing us.</p>
            
            <p><strong>Invoice Attached:</strong> Please find your official invoice attached to this email.</p>
            
            <div style="text-align: center;">
                <a href="{{ route('products.index') }}" class="btn">Shop Again</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} MyStore. All rights reserved.</p>
            <p>If you have any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>
