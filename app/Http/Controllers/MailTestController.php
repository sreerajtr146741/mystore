<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailTestController extends Controller
{
    public function send()
    {
        try {
            // Send a raw text email using the configured mailer
            Mail::raw('This is a test email sent using Laravel Mail Facade.', function ($message) {
                $message->to('receiver@example.com')
                        ->subject('Test Email from MyStore (Laravel Mail)');
            });

            return response('Mail sent successfully via Laravel Mail!', 200);

        } catch (\Throwable $e) {
            Log::error('Mail send failed', [
                'exception' => $e->getMessage()
            ]);
            return response('Sorry, email could not be sent. Check laravel.log for details.', 500);
        }
    }
}
