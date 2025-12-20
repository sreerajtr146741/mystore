<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminContactReply;
use App\Models\ContactMessage;
use Exception;

class ContactMessageController extends Controller
{
    public function reply(Request $request, $id)
    {
        try {
            // Find the message
            $msg = ContactMessage::findOrFail($id);

            // Validate input
            $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            // Send reply email
            Mail::to($msg->email)
                ->send(new AdminContactReply($request->message, $request->subject));

            // Save reply to database
            $msg->replies()->create([
                'user_id' => auth()->id(),
                'subject' => $request->subject,
                'message' => $request->message,
            ]);

            return back()->with('success', 'Reply sent successfully!');

        } catch (Exception $e) {

            // If anything fails (mail, DB, etc.)
            return back()->with(
                'error',
                'Something went wrong while sending the reply. Please try again.'
            );
        }
    }

    public function index()
    {
        try {
            $messages = ContactMessage::with('replies')
                ->latest()
                ->paginate(10);

            return view('admin.messages.index', compact('messages'));

        } catch (Exception $e) {
            return back()->with(
                'error',
                'Unable to load messages.'
            );
        }
    }

    public function destroy($id)
    {
        try {
            ContactMessage::findOrFail($id)->delete();

            return back()->with('success', 'Message deleted successfully!');

        } catch (Exception $e) {
            return back()->with(
                'error',
                'Failed to delete message.'
            );
        }
    }
}
