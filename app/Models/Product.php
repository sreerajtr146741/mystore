<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
// ADD:
use App\Models\Category; // for the category relation

class Product extends Model
{
    /**
     * Mass-assignable attributes
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'category',
        'image',
        'stock',
        'status',
        'slug',

        // discount fields
        'discount_type',
        'discount_value',
        'discount_starts_at',
        'discount_ends_at',
        'is_discount_active',
        'is_active',

        // ADD:
        'category_id', // keep your string 'category' too; this just enables relation
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'price'               => 'decimal:2',
        'discount_value'      => 'decimal:2',
        'stock'               => 'integer',
        'is_discount_active'  => 'boolean',
        'is_active'           => 'boolean',
        'discount_starts_at'  => 'datetime',
        'discount_ends_at'    => 'datetime',
    ];

    /**
     * Accessors to append on array/json
     */
    protected $appends = [
        'final_price',
        // ADD:
        'discounted_price', // keep legacy accessor visible in arrays/json
    ];

    /* =========================
       Relationships
       ========================= */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ADD:
    public function category()
    {
        // Uses 'category_id' FK (added above), adjusts automatically if null
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }

    /* =========================
       Scopes
       ========================= */
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /* =========================
       Accessors
       ========================= */

    public function getFinalPriceAttribute()
    {
        $price = (float) $this->price;

        if (!$this->is_discount_active) {
            return $price;
        }

        // Check dates if set
        if ($this->discount_starts_at && $this->discount_ends_at) {
            if (!now()->between($this->discount_starts_at, $this->discount_ends_at)) {
                return $price;
            }
        }

        // Apply product-specific discount
        $val = $this->discount_value ?? 0;
        $type = $this->discount_type ?? 'fixed';

        return max(0, $this->applyDiscount($price, $type, $val));
    }

    // ADD:
    public function getDiscountedPriceAttribute(): float
    {
        // Backward-compatible alias that also considers category-level percent discount.
        // Chooses the lowest price between existing final_price and category discount on base.
        $base = (float) ($this->price ?? 0);
        $currentFinal = (float) ($this->final_price ?? $base);

        $catPercent = 0.0;
        try {
            if ($this->relationLoaded('category') ? $this->category : $this->category()->first()) {
                $cat = $this->category;
                if ($cat && isset($cat->discount_percent)) {
                    $catPercent = (float) $cat->discount_percent;
                }
            }
        } catch (\Throwable $e) {
            // If relation lookup fails, ignore and treat as no category discount
            $catPercent = 0.0;
        }

        $withCategory = $catPercent > 0
            ? max(0.0, $this->applyDiscount($base, 'percent', $catPercent))
            : $base;

        // Return the better (lower) price
        return (float) min($currentFinal, $withCategory);
    }

    /**
     * Optional computed image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) return null;

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        try {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->image);
        } catch (\Throwable $e) {
            return $this->image;
        }
    }

    /* =========================
       Helpers
       ========================= */
    protected function applyDiscount(float $price, string $type, float $value): float
    {
        return $type === 'percent'
            ? $price - ($price * ($value / 100))
            : $price - $value;
    }
}
