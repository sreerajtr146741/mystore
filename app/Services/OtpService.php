<?php
namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class OtpService {
    /**
     * Generate and send OTP with context
     * 
     * @param string $email
     * @param string $context - 'registration', 'login', 'payment', 'password_reset'
     * @param array $data - Additional data like role, amount, etc.
     */
    public static function generateAndSend($email, $context = 'general', $data = []) {
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Clear old OTPs
        Otp::where('email', $email)->delete();
        
        // Create new OTP
        Otp::create([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10)
        ]);

        // Get user name
        $user = User::where('email', $email)->first();
        $userName = $user ? $user->first_name : 'User';

        // Prepare email content based on context
        [$subject, $message] = self::getEmailContent($context, $data);

        // Send email
        Mail::to($email)->send(new OtpMail($otp, $userName, $subject, $message));
    }

    /**
     Get email subject and message based on context
     */
    private static function getEmailContent($context, $data) {
        switch ($context) {
            case 'registration':
                $role = ucfirst($data['role'] ?? 'User');
                return [
                    "Welcome to MyStore - Registration Confirmation",
                    "<p>🎉 <strong>Congratulations!</strong> You have successfully registered as a <strong>{$role}</strong> on MyStore.</p>
                     <p>Please use the verification code below to complete your registration:</p>"
                ];

            case 'login':
                return [
                    "MyStore - Login Verification",
                    "<p>🔐 Someone is trying to log in to your MyStore account.</p>
                     <p>If this was you, please use the verification code below to complete your login:</p>
                     <p><small>If you didn't attempt to log in, please ignore this email and consider changing your password.</small></p>"
                ];

            case 'payment':
                $amount = $data['amount'] ?? '0.00';
                return [
                    "MyStore - Payment Verification Required",
                    "<p>💳 <strong>Payment Processing</strong></p>
                     <p>You are about to complete a payment of <strong>₹{$amount}</strong> on MyStore.</p>
                     <p>Please use the verification code below to authorize this transaction:</p>
                     <p><small>This is a security measure to protect your account from unauthorized transactions.</small></p>"
                ];

            case 'password_reset':
                return [
                    "MyStore - Password Reset Request",
                    "<p>🔑 <strong>Password Reset</strong></p>
                     <p>We received a request to reset your password for your MyStore account.</p>
                     <p>Please use the verification code below to proceed with resetting your password:</p>
                     <p><small>If you didn't request this, please ignore this email.</small></p>"
                ];

            default:
                return [
                    "MyStore - Verification Code",
                    "<p>Please use the verification code below to complete your action on MyStore:</p>"
                ];
        }
    }

    public static function verify($email, $otp) {
        $record = Otp::where('email', $email)->where('otp', $otp)->first();
        if ($record && !$record->isExpired()) {
            $record->delete();
            return true;
        }
        return false;
    }
}