<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionItem extends Model
{
    protected $fillable = [
        'subscription_id',
        'product_id',
        'quantity',
        'price_snapshot',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_snapshot' => 'float',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
