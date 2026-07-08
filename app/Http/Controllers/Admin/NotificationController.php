<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('is_read', 0)
            ->whereIn('type', ['order', 'contact', 'wishlist'])
            ->latest('created_at')
            ->get();

        foreach ($notifications as $notification) {
            if ($notification->title) {
                continue;
            }

            if ($notification->type === 'order') {
                $notification->title = 'Có đơn hàng mới';
            } elseif ($notification->type === 'contact') {
                $notification->title = 'Có liên hệ mới';
            } elseif ($notification->type === 'wishlist') {
                $notification->title = 'Sản phẩm yêu thích';
            }
        }

        return view('admin.pages.notifications', compact('notifications'));
    }

    public function update(Request $request)
    {
        Notification::where('id', $request->id)->update(['is_read' => 1]);

        return response()->json(['status' => true]);
    }
}
