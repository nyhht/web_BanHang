<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property string|null $status
 * @property string $changed_at
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory whereChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderStatusHistory extends Model
{
    protected $table = 'order_status_history';

    protected $fillable = ['order_id', 'status', 'changed_at', 'note'];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
