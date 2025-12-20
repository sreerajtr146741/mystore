<!DOCTYPE html>
<html>
<head>
    <title>Reply from MyStore</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { background-color: #fff; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header { border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #333; }
        .content { font-size: 16px; line-height: 1.6; color: #555; }
        .footer { margin-top: 30px; font-size: 12px; color: #aaa; text-align: center; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>MyStore Support</h2>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>{!! nl2br(e($replyMessage)) !!}</p>
            <p>Best regards,<br>MyStore Support Team</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} MyStore. All rights reserved.
        </div>
    </div>
</body>
</html>
