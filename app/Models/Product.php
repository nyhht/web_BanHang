<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $category_id
 * @property string|null $description
 * @property string|null $ingredients
 * @property string|null $cooking_instructions
 * @property numeric $price
 * @property int $stock
 * @property string $status
 * @property string|null $unit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $cartItems
 * @property-read int|null $cart_items_count
 * @property-read \App\Models\Category $category
 * @property-read \App\Models\ProductImage|null $firstImage
 * @property-read mixed $average_rating
 * @property-read mixed $image_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductImage> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @method static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCookingInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIngredients($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'description',
        'ingredients',
        'cooking_instructions',
        'price',
        'sale_price',
        'sale_discount_percentage',
        'sale_starts_at',
        'sale_ends_at',
        'sale_coupon_id',
        'promoted_at',
        'stock',
        'status',
        'unit',
        'serving_size',
        'prep_time',
        'cook_time',
        'calories',
        'storage_instruction',
        'expiry_days',
    ];

    protected $appends = ['image_url', 'average_rating', 'current_price', 'is_on_sale', 'sale_percent'];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'sale_starts_at' => 'datetime',
        'sale_ends_at' => 'datetime',
        'promoted_at' => 'datetime',
        'serving_size' => 'integer',
        'prep_time' => 'integer',
        'cook_time' => 'integer',
        'calories' => 'integer',
        'expiry_days' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function firstImage()
    {
        return $this->hasOne(ProductImage::class)->orderBy('id', 'ASC');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function mealKitIngredients()
    {
        return $this->hasMany(ProductIngredient::class);
    }

    public function cookingSteps()
    {
        return $this->hasMany(ProductCookingStep::class)->orderBy('step_number');
    }

    public function getImageUrlAttribute()
    {
        return $this->firstImage?->image ? asset('storage/' . $this->firstImage->image) : asset('storage/uploads/products/default-product.png');
    }
    public function getAverageRatingAttribute()
    {
        return $this->reviews->avg('rating') ?? 0;
    }

    public function getIsOnSaleAttribute(): bool
    {
        if ($this->sale_price === null || $this->sale_price >= $this->price) {
            return false;
        }

        if ($this->sale_starts_at !== null && $this->sale_starts_at->isFuture()) {
            return false;
        }

        if ($this->sale_ends_at !== null && $this->sale_ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function getCurrentPriceAttribute(): float
    {
        return $this->is_on_sale ? (float) $this->sale_price : (float) $this->price;
    }

    public function getSalePercentAttribute(): int
    {
        if (!$this->is_on_sale || (float) $this->price <= 0) {
            return 0;
        }

        return (int) round((1 - ((float) $this->sale_price / (float) $this->price)) * 100);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function subscriptionItems()
    {
        return $this->hasMany(SubscriptionItem::class);
    }

    public function saleCoupon()
    {
        return $this->belongsTo(Coupon::class, 'sale_coupon_id');
    }

    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price')
            ->whereColumn('sale_price', '<', 'price')
            ->where(function ($query) {
                $query->whereNull('sale_starts_at')->orWhere('sale_starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('sale_ends_at')->orWhere('sale_ends_at', '>', now());
            });
    }

    public function scopePromotedFirst($query)
    {
        return $query->orderByRaw('CASE WHEN promoted_at IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('promoted_at');
    }

}
