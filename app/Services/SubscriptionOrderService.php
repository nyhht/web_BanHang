<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SubscriptionOrderService
{
    public function __construct(
        private ShippingFeeService $shippingFeeService,
        private VietQrService $vietQrService
    ) {
    }

    public function createDueOrder(Subscription $subscription): ?Order
    {
        return DB::transaction(function () use ($subscription) {
            $subscription = Subscription::with(['items.product', 'shippingAddress', 'user'])
                ->whereKey($subscription->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!$subscription->isActive() || !$subscription->next_run_at || $subscription->next_run_at->isFuture()) {
                return null;
            }

            if ($subscription->end_date && $subscription->next_run_at->toDateString() > $subscription->end_date->toDateString()) {
                $subscription->status = Subscription::STATUS_EXPIRED;
                $subscription->save();

                return null;
            }

            if (!$subscription->shippingAddress) {
                $this->pauseSubscription($subscription, 'Dia chi giao hang khong con ton tai.');
                throw new RuntimeException("Subscription #{$subscription->id} has no shipping address.");
            }

            if ($subscription->items->isEmpty()) {
                $this->pauseSubscription($subscription, 'Goi dinh ky khong co san pham.');
                throw new RuntimeException("Subscription #{$subscription->id} has no products.");
            }

            $scheduledDate = $subscription->next_run_at->toDateString();

            $existingOrder = Order::where('subscription_id', $subscription->id)
                ->whereDate('scheduled_delivery_date', $scheduledDate)
                ->first();

            if ($existingOrder) {
                $this->advanceSubscriptionAfterRun($subscription);

                return $existingOrder;
            }

            foreach ($subscription->items as $item) {
                if (!$item->product || $item->product->status !== 'in_stock' || $item->product->stock < $item->quantity) {
                    $name = $item->product?->name ?: 'San pham da bi xoa';
                    $this->pauseSubscription($subscription, "Khong du ton kho cho san pham {$name}.");
                    throw new RuntimeException("Subscription #{$subscription->id} paused because product stock is insufficient.");
                }
            }

            $shippingQuote = $this->shippingFeeService->quoteForAddress($subscription->shippingAddress);
            $subtotal = (float) $subscription->items->sum(fn($item) => $item->quantity * $item->price_snapshot);
            $shippingFee = (float) $shippingQuote['shipping_fee'];
            $total = max($subtotal + $shippingFee, 0);

            $order = Order::create([
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
                'order_type' => Order::TYPE_SUBSCRIPTION,
                'shipping_address_id' => $subscription->shipping_address_id,
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'shipping_fee' => $shippingFee,
                'shipping_distance_km' => $shippingQuote['distance_km'],
                'shipping_duration_seconds' => $shippingQuote['duration_seconds'],
                'total_price' => $total,
                'status' => Order::STATUS_PENDING,
                'scheduled_delivery_date' => $scheduledDate,
            ]);

            foreach ($subscription->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price_snapshot,
                ]);

                $item->product->stock -= $item->quantity;
                $item->product->save();
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $subscription->payment_method,
                'transaction_id' => $subscription->payment_method === 'vietqr'
                    ? $this->vietQrService->paymentContent($order)
                    : null,
                'amount' => $order->total_price,
                'status' => 'pending',
                'paid_at' => null,
            ]);

            $order->recordStatus(Order::STATUS_PENDING, 'Don hang duoc tao tu goi dinh ky.');

            $this->advanceSubscriptionAfterRun($subscription);

            Notification::create([
                'user_id' => $subscription->user_id,
                'type' => 'order',
                'message' => "Don hang dinh ky #{$order->id} da duoc tao.",
                'link' => route('order.show', $order->id),
                'is_read' => 0,
            ]);

            return $order;
        });
    }

    private function pauseSubscription(Subscription $subscription, string $reason): void
    {
        $subscription->status = Subscription::STATUS_PAUSED;
        $subscription->note = trim(($subscription->note ? $subscription->note . "\n" : '') . now()->format('d/m/Y H:i') . ' - ' . $reason);
        $subscription->save();

        Notification::create([
            'user_id' => $subscription->user_id,
            'type' => 'subscription',
            'message' => "Goi dinh ky #{$subscription->id} da tam dung: {$reason}",
            'link' => route('subscriptions.index'),
            'is_read' => 0,
        ]);

        Log::warning('Subscription paused', [
            'subscription_id' => $subscription->id,
            'reason' => $reason,
        ]);
    }

    private function advanceSubscriptionAfterRun(Subscription $subscription): void
    {
        $subscription->last_order_generated_at = now();
        $nextRunAt = $subscription->calculateFollowingRunAt();

        if ($subscription->end_date && $nextRunAt->toDateString() > $subscription->end_date->toDateString()) {
            $subscription->status = Subscription::STATUS_EXPIRED;
            $subscription->next_run_at = null;
        } else {
            $subscription->next_run_at = $nextRunAt;
        }

        $subscription->save();
    }
}
