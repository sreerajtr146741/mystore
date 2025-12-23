<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

    class ContactController extends Controller
{
    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if (!empty($data['name']) && empty($data['first_name'])) {
            $parts = explode(' ', $data['name'], 2);
            $data['first_name'] = $parts[0];
            $data['last_name'] = $parts[1] ?? '';
        }

        if (empty($data['first_name'])) {
             return back()->with('error', 'Name is required.');
        }

        ContactMessage::create($data);

        return back()->with('success', 'Message sent successfully! We will get back to you soon.');
    }
}
