<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property int $discount_percentage
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property int|null $usage_limit
 * @property int $times_used
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereDiscountPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereTimesUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coupon whereUsageLimit($value)
 * @mixin \Eloquent
 */
class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'discount_percentage',
        'starts_at',
        'expires_at',
        'usage_limit',
        'times_used',
        'is_active',
        'restricted_to_assigned_users',
        'auto_assign_on_register',
        'auto_assign_weekend',
        'auto_assign_dates',
        'notify_customers',
        'auto_apply_to_products',
        'product_ids',
        'daily_product_limit',
        'last_product_promotion_at',
        'product_promotion_message',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'restricted_to_assigned_users' => 'boolean',
        'auto_assign_on_register' => 'boolean',
        'auto_assign_weekend' => 'boolean',
        'auto_assign_dates' => 'array',
        'notify_customers' => 'boolean',
        'auto_apply_to_products' => 'boolean',
        'product_ids' => 'array',
        'last_product_promotion_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['assigned_reason', 'notified_at', 'used_at'])
            ->withTimestamps();
    }

    public function promotedProducts()
    {
        return $this->hasMany(Product::class, 'sale_coupon_id');
    }

    public function hasStarted(): bool
    {
        return $this->starts_at === null || $this->starts_at->isPast();
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function hasReachedUsageLimit(): bool
    {
        if (is_null($this->usage_limit)) {
            return false;
        }

        return $this->times_used >= $this->usage_limit;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUsable($query)
    {
        return $query->active()
            ->where(function ($query) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->where(function ($query) {
                $query->whereNull('usage_limit')->orWhereColumn('times_used', '<', 'usage_limit');
            });
    }
}
