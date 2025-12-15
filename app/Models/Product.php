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
    public function linkedCategory()
    {
        // Uses 'category_id' FK, adjusts automatically if null
        return $this->belongsTo(Category::class, 'category_id');
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
        // 1. PRODUCT-LEVEL DISCOUNT (Highest Priority)
        // If the product specifically has a discount active, use it.
        $base = (float) $this->price;
        if ($this->is_discount_active && ($this->discount_value > 0)) {
            return (float) $this->final_price;
        }

        // 2. CATEGORY-LEVEL HIERARCHY (Product > Brand/SubCat > ParentCat)
        $percent = 0;
        
        // Eager load if possible/needed, or just access relation
        $cat = $this->linkedCategory; 

        if ($cat) {
            // A. Check Immediate Category (e.g. BMW)
            if (!is_null($cat->discount_percent)) {
                // Check Expiry
                if (!$cat->discount_expires_at || Carbon::now()->lt($cat->discount_expires_at)) {
                    $percent = (float) $cat->discount_percent;
                }
            } 
            
            // B. If no valid discount found from immediate category (inherited or expired), Check Parent
            if ($percent == 0 && $cat->parent && !is_null($cat->parent->discount_percent)) {
                 if (!$cat->parent->discount_expires_at || Carbon::now()->lt($cat->parent->discount_expires_at)) {
                    $percent = (float) $cat->parent->discount_percent;
                 }
            }
        }

        // Apply percent if found > 0
        if ($percent > 0) {
            return max(0, $base - ($base * ($percent / 100)));
        }

        return $base;
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
