<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $delivery_staff_id
 * @property numeric $subtotal
 * @property numeric $discount_amount
 * @property numeric $shipping_fee
 * @property numeric|null $shipping_distance_km
 * @property int|null $shipping_duration_seconds
 * @property int|null $coupon_id
 * @property string|null $coupon_code
 * @property numeric $total_price
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $dispatched_at
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property int $shipping_address_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Coupon|null $coupon
 * @property-read \App\Models\User|null $deliveryStaff
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderStatusHistory> $orderStatusHistory
 * @property-read int|null $order_status_history_count
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\ShippingAddress $shippingAddress
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCouponCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDispatchedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUserId($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PACKED = 'packed';
    public const STATUS_READY_FOR_DELIVERY = 'ready_for_delivery';
    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELED = 'canceled';

    public const TYPE_NORMAL = 'normal';
    public const TYPE_SUBSCRIPTION = 'subscription';

    protected $fillable = [
        'user_id',
        'subscription_id',
        'order_type',
        'subtotal',
        'discount_amount',
        'shipping_fee',
        'shipping_distance_km',
        'shipping_duration_seconds',
        'total_price',
        'status',
        'scheduled_delivery_date',
        'shipping_address_id',
        'coupon_id',
        'coupon_code',
        'delivery_staff_id',
        'dispatched_at',
        'delivered_at',
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'delivered_at' => 'datetime',
        'scheduled_delivery_date' => 'date',
        'shipping_distance_km' => 'float',
        'shipping_duration_seconds' => 'integer',
    ];

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'Chờ xác nhận',
            self::STATUS_PROCESSING => 'Đang sơ chế',
            self::STATUS_PACKED => 'Đã đóng gói',
            self::STATUS_READY_FOR_DELIVERY => 'Sẵn sàng giao',
            self::STATUS_OUT_FOR_DELIVERY => 'Đang giao',
            self::STATUS_DELIVERED => 'Đã giao',
            self::STATUS_COMPLETED => 'Hoàn thành',
            self::STATUS_CANCELED => 'Đã hủy',
        ];
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function orderStatusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function deliveryStaff()
    {
        return $this->belongsTo(User::class, 'delivery_staff_id');
    }

    public function recordStatus(string $status, ?string $note = null): void
    {
        $this->orderStatusHistory()->create([
            'status' => $status,
            'changed_at' => now(),
            'note' => $note,
        ]);
    }
}
