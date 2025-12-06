<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; color: #ffffff; font-size: 28px; font-weight: 600; }
        .content { padding: 40px 30px; }
        .greeting { font-size: 18px; color: #333; margin-bottom: 20px; }
        .message { font-size: 16px; color: #555; line-height: 1.6; margin-bottom: 30px; }
        .otp-box { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 30px; text-align: center; margin: 30px 0; }
        .otp-label { color: #ffffff; font-size: 14px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; }
        .otp-code { font-size: 42px; font-weight: bold; color: #ffffff; letter-spacing: 8px; font-family: 'Courier New', monospace; }
        .validity { text-align: center; color: #888; font-size: 14px; margin-top: 20px; }
        .footer { background: #f8f9fa; padding: 30px; text-align: center; color: #888; font-size: 14px; border-top: 1px solid #e0e0e0; }
        .footer a { color: #667eea; text-decoration: none; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛍️ MyStore</h1>
        </div>
        
        <div class="content">
            <div class="greeting">Hello {{ $userName }}!</div>
            
            <div class="message">
                {!! $messageContent !!}
            </div>
            
            <div class="otp-box">
                <div class="otp-label">Your Verification Code</div>
                <div class="otp-code">{{ $otp }}</div>
            </div>
            
            <div class="validity">
                ⏱️ This code is valid for <strong>10 minutes</strong>
            </div>
            
            <div class="warning">
                <strong>⚠️ Security Notice:</strong> Never share this code with anyone. MyStore will never ask for your OTP via phone or email.
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated message from MyStore.</p>
            <p>If you didn't request this code, please ignore this email.</p>
            <p>&copy; {{ date('Y') }} MyStore. All rights reserved.</p>
        </div>
    </div>
</body>
</html>