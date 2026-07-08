<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function read(Notification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);

        $notification->update(['is_read' => 1]);

        return redirect($this->redirectUrl($notification));
    }

    public function markRead(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:notifications,id'],
        ]);

        Notification::where('id', $request->id)
            ->where('user_id', Auth::id())
            ->update(['is_read' => 1]);

        return response()->json(['status' => true]);
    }

    private function redirectUrl(Notification $notification): string
    {
        if (in_array($notification->type, ['customer_coupon', 'product_promotion'], true)) {
            return route('products.index', [], false);
        }

        return $notification->link ?: route('home');
    }
}
