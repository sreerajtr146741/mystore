<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->get();
        return response()->json(['status' => true, 'data' => $messages]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate(['reply' => 'required|string']);
        
        $message = ContactMessage::find($id);
        if (!$message) return response()->json(['status' => false, 'message' => 'Message not found'], 404);

        // Logic to send email would go here
        // ...

        $message->update(['status' => 'replied', 'reply_content' => $request->reply]);

        return response()->json(['status' => true, 'message' => 'Reply sent']);
    }

    public function destroy($id)
    {
        $message = ContactMessage::find($id);
        if (!$message) return response()->json(['status' => false, 'message' => 'Message not found'], 404);
        
        $message->delete();
        return response()->json(['status' => true, 'message' => 'Message deleted']);
    }
}
