<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\VietQrService;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function showOrder(int $id)
    {
        $order = Order::with(['orderItems.product.mealKitIngredients', 'orderItems.product.cookingSteps', 'user', 'shippingAddress', 'payment', 'deliveryStaff'])->findOrFail($id);

        $userId = Auth::id();
        abort_unless((int) $order->user_id === (int) $userId, 403);

        $vietQr = app(VietQrService::class);
        $vietQrUrl = optional($order->payment)->payment_method === 'vietqr' ? $vietQr->qrUrl($order) : null;
        $vietQrContent = optional($order->payment)->payment_method === 'vietqr' ? $vietQr->paymentContent($order) : null;
        $vietQrAccount = $vietQr->accountInfo();

        return view('clients.pages.order-detail', compact('order', 'vietQrUrl', 'vietQrContent', 'vietQrAccount'));
    }

    public function cancel(int $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', Order::STATUS_PENDING)
            ->firstOrFail();

        foreach ($order->orderItems as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        $order->status = Order::STATUS_CANCELED;
        $order->save();
        $order->recordStatus(Order::STATUS_CANCELED, 'Order canceled by customer');

        return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công và sản phẩm đã được hoàn kho.');
    }

    public function received(int $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', Order::STATUS_DELIVERED)
            ->firstOrFail();

        if ($payment = $order->payment) {
            if ($payment->status !== 'completed') {
                $payment->status = 'completed';
                $payment->paid_at = $payment->paid_at ?? now(); 
                $payment->save();
            }
        }

        $order->status = Order::STATUS_COMPLETED;
        $order->save();
        $order->recordStatus(Order::STATUS_COMPLETED, 'Order completed by customer confirmation');

        return redirect()
            ->back()
            ->with('success', 'Xác nhận thành công. Bạn có thể đánh giá đơn hàng này!');
    }

}
