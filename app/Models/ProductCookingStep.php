<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCookingStep extends Model
{
    protected $fillable = [
        'product_id',
        'step_number',
        'instruction',
    ];

    protected $casts = [
        'step_number' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
