<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    public const FREQUENCY_DAILY = 'daily';
    public const FREQUENCY_WEEKLY = 'weekly';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id',
        'shipping_address_id',
        'frequency',
        'week_day',
        'start_date',
        'end_date',
        'preferred_delivery_time',
        'next_run_at',
        'last_order_generated_at',
        'status',
        'payment_method',
        'estimated_subtotal',
        'estimated_shipping_fee',
        'estimated_total',
        'note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_run_at' => 'datetime',
        'last_order_generated_at' => 'datetime',
        'estimated_subtotal' => 'float',
        'estimated_shipping_fee' => 'float',
        'estimated_total' => 'float',
        'week_day' => 'integer',
    ];

    public static function frequencyLabels(): array
    {
        return [
            self::FREQUENCY_DAILY => 'Hang ngay',
            self::FREQUENCY_WEEKLY => 'Hang tuan',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_ACTIVE => 'Dang hoat dong',
            self::STATUS_PAUSED => 'Tam dung',
            self::STATUS_CANCELED => 'Da huy',
            self::STATUS_EXPIRED => 'Da het han',
        ];
    }

    public static function weekDayLabels(): array
    {
        return [
            1 => 'Thu 2',
            2 => 'Thu 3',
            3 => 'Thu 4',
            4 => 'Thu 5',
            5 => 'Thu 6',
            6 => 'Thu 7',
            7 => 'Chu nhat',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class);
    }

    public function items()
    {
        return $this->hasMany(SubscriptionItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function calculateNextRunAt(?Carbon $from = null): Carbon
    {
        $time = $this->preferred_delivery_time ?: '08:00:00';
        $base = $from ? $from->copy() : Carbon::parse($this->start_date);

        if ($this->frequency === self::FREQUENCY_DAILY) {
            return $base->setTimeFromTimeString($time);
        }

        $weekDay = $this->week_day ?: 1;
        $nextRun = $base->setTimeFromTimeString($time);

        while ($nextRun->isoWeekday() !== $weekDay) {
            $nextRun->addDay();
        }

        return $nextRun;
    }

    public function calculateFollowingRunAt(): Carbon
    {
        $current = $this->next_run_at ? $this->next_run_at->copy() : $this->calculateNextRunAt();

        if ($this->frequency === self::FREQUENCY_DAILY) {
            return $current->addDay();
        }

        return $current->addWeek();
    }
}
