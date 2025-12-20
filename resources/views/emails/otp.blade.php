@extends('emails.layouts.base')

@section('title', $subject ?? 'Verification Code')

@push('styles')
<style>
    .otp-container { text-align: center; margin: 30px 0; }
    .otp-box { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
        border-radius: 12px; 
        padding: 32px; 
        display: inline-block;
        box-shadow: 0 8px 24px rgba(102,126,234,0.3);
    }
    .otp-label { 
        color: rgba(255,255,255,0.9); 
        font-size: 13px; 
        text-transform: uppercase; 
        letter-spacing: 2px; 
        margin-bottom: 12px; 
        font-weight: 600;
    }
    .otp-code { 
        font-size: 48px; 
        font-weight: 800; 
        color: #ffffff; 
        letter-spacing: 12px; 
        font-family: 'Courier New', Consolas, monospace; 
        text-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .validity-badge { 
        display: inline-block;
        background: #fff3cd; 
        color: #856404; 
        padding: 12px 24px; 
        border-radius: 20px; 
        font-size: 14px; 
        font-weight: 600;
        margin: 20px 0;
        border: 2px solid #ffc107;
    }
    .security-tips { 
        background: #f8f9fa; 
        border-radius: 8px; 
        padding: 20px; 
        margin: 24px 0; 
    }
    .security-tips ul { 
        margin: 12px 0; 
        padding-left: 20px; 
        text-align: left; 
    }
    .security-tips li { 
        margin: 8px 0; 
        color: #555; 
    }
</style>
@endpush

@section('content')
    <p>Hello <strong>{{ $userName }}</strong>,</p>
    
    <div style="margin: 20px 0;">
        {!! $messageContent !!}
    </div>

    <div class="otp-container">
        <div class="otp-box">
            <div class="otp-label">Your Verification Code</div>
            <div class="otp-code">{{ $otp }}</div>
        </div>
    </div>

    <div style="text-align: center;">
        <div class="validity-badge">
            ⏱️ Valid for <strong>10 minutes</strong>
        </div>
    </div>

    <div class="warning-box">
        <strong>🔒 Security Notice:</strong><br>
        Never share this code with anyone. MyStore will never ask for your OTP via phone call or WhatsApp.
    </div>

    <div class="security-tips">
        <strong style="color: #2874f0; font-size: 16px;">🛡️ Security Tips:</strong>
        <ul>
            <li>This code is for one-time use only</li>
            <li>Do not share this code with anyone, including MyStore staff</li>
            <li>If you didn't request this code, please ignore this email</li>
            <li>Delete this email after using the code</li>
        </ul>
    </div>

    <p style="margin-top: 24px; text-align: center; color: #6c757d; font-size: 14px;">
        If you didn't request this verification code, you can safely ignore this email.
    </p>
@endsection