<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Notification;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PromotionService
{
    public function assignRegistrationCoupons(User $user): int
    {
        $coupons = Coupon::usable()
            ->where('auto_assign_on_register', true)
            ->get();

        return $this->assignCouponsToUser($coupons, $user, 'register');
    }

    public function assignScheduledCoupons(?Carbon $date = null): int
    {
        $date ??= now();
        $coupons = Coupon::usable()
            ->where(function ($query) use ($date) {
                $query->where('auto_assign_weekend', true)
                    ->orWhereNotNull('auto_assign_dates');
            })
            ->get()
            ->filter(fn (Coupon $coupon) => $this->couponShouldRunOnDate($coupon, $date));

        if ($coupons->isEmpty()) {
            return 0;
        }

        $customers = User::whereHas('role', function ($query) {
            $query->where('name', 'customer');
        })->where('status', 'active')->get();

        $count = 0;
        foreach ($customers as $customer) {
            $count += $this->assignCouponsToUser($coupons, $customer, 'scheduled');
        }

        return $count;
    }

    public function applyDailyProductPromotions(?Carbon $date = null): int
    {
        $date ??= now();
        $coupons = Coupon::usable()
            ->where('auto_apply_to_products', true)
            ->where(function ($query) use ($date) {
                $query->whereNull('last_product_promotion_at')
                    ->orWhereDate('last_product_promotion_at', '<', $date->toDateString());
            })
            ->orderBy('id')
            ->get();

        $promotedCount = 0;

        foreach ($coupons as $coupon) {
            $promotedCount += $this->applyProductPromotionForCoupon($coupon, $date);
        }

        return $promotedCount;
    }

    public function applyProductPromotionForCoupon(Coupon $coupon, ?Carbon $date = null, bool $force = false): int
    {
        $date ??= now();
        $coupon->refresh();

        if (!$this->couponCanApplyProductPromotion($coupon, $date)) {
            return 0;
        }

        if (!$force && $coupon->last_product_promotion_at?->isSameDay($date)) {
            return 0;
        }

        $products = $this->productsForCoupon($coupon);

        if ($products->isEmpty()) {
            return 0;
        }

        $promotedCount = 0;

        DB::transaction(function () use ($coupon, $products, $date, &$promotedCount) {
            Product::where('sale_coupon_id', $coupon->id)
                ->update([
                    'sale_price' => null,
                    'sale_discount_percentage' => null,
                    'sale_starts_at' => null,
                    'sale_ends_at' => null,
                    'sale_coupon_id' => null,
                    'promoted_at' => null,
                ]);

            foreach ($products as $product) {
                $salePrice = $this->calculateSalePrice($product, $coupon);

                $product->update([
                    'sale_price' => $salePrice,
                    'sale_discount_percentage' => $coupon->discount_percentage,
                    'sale_starts_at' => $date,
                    'sale_ends_at' => $coupon->expires_at,
                    'sale_coupon_id' => $coupon->id,
                    'promoted_at' => $date,
                ]);
                $product->refresh();

                $this->notifyCustomersAboutProductDeal($coupon, $product);
                $promotedCount++;
            }

            $coupon->update(['last_product_promotion_at' => $date]);
        });

        return $promotedCount;
    }

    public function clearProductPromotionsForCoupon(Coupon $coupon): int
    {
        return Product::where('sale_coupon_id', $coupon->id)
            ->update([
                'sale_price' => null,
                'sale_discount_percentage' => null,
                'sale_starts_at' => null,
                'sale_ends_at' => null,
                'sale_coupon_id' => null,
                'promoted_at' => null,
            ]);
    }

    public function clearExpiredProductPromotions(?Carbon $date = null): int
    {
        $date ??= now();

        return Product::whereNotNull('sale_price')
            ->whereNotNull('sale_ends_at')
            ->where('sale_ends_at', '<=', $date)
            ->update([
                'sale_price' => null,
                'sale_discount_percentage' => null,
                'sale_starts_at' => null,
                'sale_ends_at' => null,
                'sale_coupon_id' => null,
                'promoted_at' => null,
            ]);
    }

    public function assignCouponsToUser(Collection $coupons, User $user, string $reason): int
    {
        $count = 0;

        foreach ($coupons as $coupon) {
            $attached = $user->coupons()
                ->where('coupons.id', $coupon->id)
                ->exists();

            if ($attached) {
                continue;
            }

            $user->coupons()->attach($coupon->id, [
                'assigned_reason' => $reason,
                'notified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($coupon->notify_customers) {
                $this->createCouponNotification($coupon, $user, $reason);
            }

            $count++;
        }

        return $count;
    }

    private function couponShouldRunOnDate(Coupon $coupon, Carbon $date): bool
    {
        if ($coupon->auto_assign_weekend && $date->isWeekend()) {
            return true;
        }

        $dates = collect($coupon->auto_assign_dates ?? [])
            ->filter()
            ->map(fn ($value) => Carbon::parse($value)->toDateString());

        return $dates->contains($date->toDateString());
    }

    private function productsForCoupon(Coupon $coupon): Collection
    {
        $query = Product::with('firstImage')
            ->where('status', 'in_stock')
            ->where('stock', '>', 0);

        $productIds = collect($coupon->product_ids ?? [])->filter()->values();

        if ($productIds->isNotEmpty()) {
            $query->whereIn('id', $productIds);
        }

        $limit = $coupon->daily_product_limit ?: 6;

        return $query->inRandomOrder()->limit($limit)->get();
    }

    private function calculateSalePrice(Product $product, Coupon $coupon): float
    {
        $price = (float) $product->price;
        $salePrice = round($price * (100 - $coupon->discount_percentage) / 100, 2);

        return max($salePrice, 0);
    }

    private function createCouponNotification(Coupon $coupon, User $user, string $reason): void
    {
        $title = $reason === 'register'
            ? 'Bạn vừa nhận mã giảm giá chào mừng'
            : 'Bạn có mã giảm giá mới';

        Notification::create([
            'user_id' => $user->id,
            'type' => 'customer_coupon',
            'title' => $title,
            'message' => "Mã {$coupon->code} giảm {$coupon->discount_percentage}% đã sẵn sàng trong tài khoản của bạn.",
            'link' => '/products',
            'data' => [
                'coupon_id' => $coupon->id,
                'coupon_code' => $coupon->code,
                'discount_percentage' => $coupon->discount_percentage,
                'reason' => $reason,
            ],
            'expires_at' => $coupon->expires_at,
            'is_read' => 0,
        ]);
    }

    private function notifyCustomersAboutProductDeal(Coupon $coupon, Product $product): void
    {
        if (!$coupon->notify_customers) {
            return;
        }

        $customers = User::whereHas('role', function ($query) {
            $query->where('name', 'customer');
        })->where('status', 'active')->get();

        $message = $coupon->product_promotion_message
            ?: "{$product->name} đang giảm {$coupon->discount_percentage}% hôm nay.";

        foreach ($customers as $customer) {
            Notification::create([
                'user_id' => $customer->id,
                'type' => 'product_promotion',
                'title' => 'Món ăn đang giảm giá',
                'message' => $message,
                'image' => $product->firstImage?->image,
                'link' => '/products',
                'data' => [
                    'coupon_id' => $coupon->id,
                    'coupon_code' => $coupon->code,
                    'product_id' => $product->id,
                    'discount_percentage' => $coupon->discount_percentage,
                    'sale_price' => $product->sale_price,
                ],
                'expires_at' => $coupon->expires_at,
                'is_read' => 0,
            ]);
        }
    }

    private function couponCanApplyProductPromotion(Coupon $coupon, Carbon $date): bool
    {
        if (!$coupon->is_active || !$coupon->auto_apply_to_products) {
            return false;
        }

        if ($coupon->starts_at !== null && $coupon->starts_at->gt($date)) {
            return false;
        }

        if ($coupon->expires_at !== null && $coupon->expires_at->lte($date)) {
            return false;
        }

        return !$coupon->hasReachedUsageLimit();
    }
}
