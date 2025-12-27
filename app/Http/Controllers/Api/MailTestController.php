<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailTestController extends Controller
{
    /**
     * Send a test email
     */
    public function send()
    {
        try {
            // Send a raw text email
            Mail::raw('This is a test email sent using MyStore API.', function ($message) {
                $message->to('receiver@example.com')
                        ->subject('Test Email from MyStore API');
            });

            return ApiResponse::success(null, 'Mail sent successfully via Laravel Mail!');

        } catch (\Throwable $e) {
            Log::error('API Mail send failed', [
                'exception' => $e->getMessage()
            ]);
            return ApiResponse::error('Sorry, email could not be sent. Check laravel.log for details.', 500);
        }
    }
}
