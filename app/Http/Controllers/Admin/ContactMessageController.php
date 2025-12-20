<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\AdminContactReply;

class ContactMessageController extends Controller
{
    public function reply(Request $request, $id) {
        $msg = \App\Models\ContactMessage::findOrFail($id);
        
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Mail::to($msg->email)->send(new AdminContactReply($request->message, $request->subject));

        // Save reply to database
        $msg->replies()->create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Reply sent successfully!');
    }

    public function index() {
        $messages = \App\Models\ContactMessage::with('replies')->latest()->paginate(10);
        return view('admin.messages.index', compact('messages'));
    }

    public function destroy($id) {
        \App\Models\ContactMessage::findOrFail($id)->delete();
        return back()->with('success', 'Message deleted successfully!');
    }
}
