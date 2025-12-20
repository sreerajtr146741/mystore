<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'MyStore')</title>
    <style>
        /* Reset */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        
        /* Base Styles */
        body { margin: 0; padding: 0; width: 100% !important; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f4f7fa; }
        
        /* Container */
        .email-wrapper { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .email-container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        
        /* Header */
        .email-header { background: linear-gradient(135deg, #2874f0 0%, #1557b0 100%); padding: 30px 40px; text-align: center; }
        .email-header h1 { margin: 0; color: #ffffff; font-size: 28px; font-weight: 700; letter-spacing: -0.5px; }
        .email-header .tagline { color: rgba(255,255,255,0.9); font-size: 14px; margin-top: 8px; }
        
        /* Content */
        .email-content { padding: 40px; color: #333333; font-size: 16px; line-height: 1.6; }
        .email-content h2 { color: #2874f0; font-size: 22px; margin: 0 0 20px 0; font-weight: 600; }
        .email-content p { margin: 0 0 16px 0; }
        
        /* Button */
        .btn-primary { display: inline-block; padding: 14px 32px; background: #fb641b; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(251,100,27,0.3); transition: all 0.3s; }
        .btn-primary:hover { background: #e85910; box-shadow: 0 6px 16px rgba(251,100,27,0.4); }
        
        .btn-secondary { display: inline-block; padding: 12px 28px; background: transparent; color: #2874f0 !important; text-decoration: none; border: 2px solid #2874f0; border-radius: 6px; font-weight: 600; font-size: 14px; }
        
        /* Info Box */
        .info-box { background: #f8f9fa; border-left: 4px solid #2874f0; padding: 20px; margin: 24px 0; border-radius: 4px; }
        .info-box strong { color: #2874f0; }
        
        .warning-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 16px; margin: 20px 0; color: #856404; border-radius: 4px; }
        .success-box { background: #d4edda; border-left: 4px solid #28a745; padding: 16px; margin: 20px 0; color: #155724; border-radius: 4px; }
        
        /* Footer */
        .email-footer { background: #f8f9fa; padding: 30px 40px; text-align: center; color: #6c757d; font-size: 14px; border-top: 1px solid #e9ecef; }
        .email-footer a { color: #2874f0; text-decoration: none; font-weight: 500; }
        .email-footer a:hover { text-decoration: underline; }
        .footer-links { margin: 20px 0; }
        .footer-links a { margin: 0 12px; }
        .social-links { margin: 16px 0; }
        .social-links a { margin: 0 8px; font-size: 20px; }
        
        /* Divider */
        .divider { height: 1px; background: #e9ecef; margin: 30px 0; }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container { margin: 10px auto !important; border-radius: 0 !important; }
            .email-header, .email-content, .email-footer { padding: 24px !important; }
            .btn-primary, .btn-secondary { display: block !important; margin: 10px 0 !important; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f7fa;">
        <tr>
            <td style="padding: 20px 0;">
                <div class="email-container">
                    <!-- Header -->
                    <div class="email-header">
                        <h1>🛍️ MyStore</h1>
                        <div class="tagline">Your Trusted Shopping Destination</div>
                    </div>
                    
                    <!-- Main Content -->
                    <div class="email-content">
                        @yield('content')
                    </div>
                    
                    <!-- Footer -->
                    <div class="email-footer">
                        <div class="footer-links">
                            <a href="{{ config('app.url') }}">Shop Now</a>
                            <a href="{{ config('app.url') }}/about">About Us</a>
                            <a href="{{ config('app.url') }}/contact">Contact</a>
                        </div>
                        
                        <div class="divider"></div>
                        
                        <p style="margin: 8px 0;">
                            <strong>MyStore</strong> - Your one-stop shop for everything you need
                        </p>
                        <p style="margin: 8px 0; font-size: 13px; color: #999;">
                            This email was sent to you because you have an account with MyStore.
                        </p>
                        <p style="margin: 16px 0 8px 0; font-size: 12px; color: #aaa;">
                            © {{ date('Y') }} MyStore. All rights reserved.
                        </p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
