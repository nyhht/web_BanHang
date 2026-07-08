<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::orderBy('is_replied')
            ->orderByDesc('created_at')->get();
        $chatMessages = ChatMessage::with('user')
            ->latest()
            ->limit(100)
            ->get();

        return view('admin.pages.contact', compact('contacts', 'chatMessages'));
    }

    public function replyContact(Request $request)
    {
        $id = $request->contact_id;
        $messageContent = $request->message;
        $email = $request->email;
        if (is_object($messageContent)) {
            $messageContent = (string) $messageContent;
        }
        try {
            Mail::send('admin.emails.reply-contact', compact('messageContent'), function ($message) use ($email) {
                $message->to($email)
                    ->subject('Phản hồi liên hệ của khách hàng');
            });

            Contact::where('id', $id)->update(['is_replied' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'Phản hồi qua email thành công!',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể gửi email. Vui lòng thử lại sau. ' . $th->getMessage(),
            ]);
        }

    }

}
