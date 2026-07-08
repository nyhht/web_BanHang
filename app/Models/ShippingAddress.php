<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $full_name
 * @property string $phone
 * @property string $address
 * @property string $city
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $google_place_id
 * @property string|null $formatted_address
 * @property int $default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShippingAddress whereUserId($value)
 * @mixin \Eloquent
 */
class ShippingAddress extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'address',
        'city',
        'latitude',
        'longitude',
        'google_place_id',
        'formatted_address',
        'default',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
