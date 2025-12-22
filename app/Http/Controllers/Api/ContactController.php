<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Submit contact form
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000'
        ]);

        $contact = ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message
        ]);

        return ApiResponse::created(
            ['contact' => $contact],
            'Your message has been sent successfully. We will get back to you soon.'
        );
    }
}
