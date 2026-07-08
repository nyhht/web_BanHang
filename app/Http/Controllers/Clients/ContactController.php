<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index()
    {
        return view('clients.pages.contact');
    }

    public function sendContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|numeric|digits_between:10,15',
        ], [
            'name.required' => 'Tên là bắt buộc.',
            'email.required' => 'Email là bắt buộc.',
            'phone.required' => 'Số điện thoại là bắt buộc.',
        ]);

        Contact::create([
            'full_name' => $request->name,
            'phone_number' => $request->phone,
            'email' => $request->email,
            'message' => $request->message,
            'is_replied' => 0
        ]);

        $user = Auth::user();

        if (Auth::check()) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'contact',
                'message' => "Có đơn liên hệ từ " . $request->email,
                'link' => '/contacts',
                'is_read' => 0
            ]);
        } else {
            Notification::create([
                'user_id' => null,
                'type' => 'contact',
                'message' => "Có đơn liên hệ từ " . $request->email,
                'link' => '/contacts',
                'is_read' => 0
            ]);
        }

        toastr()->success('Gửi thành công! Quản trị viên sẽ sớm liên hệ với bạn.');

        return redirect()->back();
    }
}
