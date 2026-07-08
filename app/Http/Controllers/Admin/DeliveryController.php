<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $adminUser = Auth::guard('admin')->user();

        $query = Order::with([
                'user',
                'shippingAddress',
                'deliveryStaff',
                'orderItems.product',
                'orderStatusHistory' => fn ($query) => $query->orderBy('changed_at'),
            ])
            ->where(function ($query) {
                $query->whereIn('status', [
                    Order::STATUS_READY_FOR_DELIVERY,
                    Order::STATUS_OUT_FOR_DELIVERY,
                    Order::STATUS_DELIVERED,
                ])->orWhere(function ($query) {
                    $query->where('status', Order::STATUS_COMPLETED)
                        ->whereNotNull('delivery_staff_id');
                });
            })
            ->orderByDesc('id');

        if ($adminUser->role->name === 'delivery_staff') {
            $query->where('delivery_staff_id', $adminUser->id);
        } else {
            if ($request->filled('delivery_staff_id')) {
                $query->where('delivery_staff_id', $request->integer('delivery_staff_id'));
            }
        }

        if ($request->filled('status')) {
            if ($request->input('status') === Order::STATUS_DELIVERED) {
                $query->whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_COMPLETED]);
            } else {
                $query->where('status', $request->input('status'));
            }
        }

        $orders = $query->get();

        $deliveryStaffs = User::whereHas('role', function ($query) {
            $query->where('name', 'delivery_staff');
        })->where('status', 'active')->get();

        return view('admin.pages.deliveries', [
            'orders' => $orders,
            'deliveryStaffs' => $deliveryStaffs,
            'filters' => $request->only(['status', 'delivery_staff_id']),
            'adminUser' => $adminUser,
        ]);
    }

    public function startDelivery(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $order = Order::find($data['order_id']);
        $adminUser = Auth::guard('admin')->user();

        if ($order->status !== Order::STATUS_READY_FOR_DELIVERY) {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ những đơn hàng được đánh dấu sẵn sàng giao hàng mới có thể bắt đầu.',
            ], 422);
        }

        if (!$this->canHandleOrder($adminUser, $order)) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không được phép cập nhật đơn hàng này.',
            ], 403);
        }

        $order->status = Order::STATUS_OUT_FOR_DELIVERY;
        $order->dispatched_at = now();
        $order->save();
        $order->recordStatus(Order::STATUS_OUT_FOR_DELIVERY, $data['note'] ?? null);

        return response()->json([
            'status' => true,
            'message' => 'Đơn hàng được đánh dấu là đã giao.',
        ]);
    }

    public function completeDelivery(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $order = Order::find($data['order_id']);
        $adminUser = Auth::guard('admin')->user();

        if ($order->status !== Order::STATUS_OUT_FOR_DELIVERY) {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ những đơn hàng đã sẵn sàng giao hàng mới có thể được hoàn thành.',
            ], 422);
        }

        if (!$this->canHandleOrder($adminUser, $order)) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không được phép cập nhật đơn hàng này.',
            ], 403);
        }

        $order->status = Order::STATUS_DELIVERED;
        $order->delivered_at = now();
        $order->save();
        $order->recordStatus(Order::STATUS_DELIVERED, $data['note'] ?? null);

        return response()->json([
            'status' => true,
            'message' => 'Đơn hàng được đánh dấu là đã giao.',
        ]);
    }

    protected function canHandleOrder(User $adminUser, Order $order): bool
    {
        if ($adminUser->role->name === 'admin') {
            return true;
        }

        return $order->delivery_staff_id === $adminUser->id;
    }
}
