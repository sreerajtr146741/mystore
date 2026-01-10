<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Added back for user() relation
use App\Models\Category; // Added back for categoryModel() relation
use App\Models\ProductBanner; // Added back for banners() relation
use App\Models\OrderItem; // Added back for orderItems() relation

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'category_id',
        'category',
        'discount_type',
        'discount_value',
        'status',
        'specifications',
        'slug',
        'sku',
        'is_active',
    ];

    protected $casts = [
        'specifications' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'discount_value' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoryModel()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function banners()
    {
        return $this->hasMany(ProductBanner::class);
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
            // A. Check Immediate Category
            if (!is_null($cat->discount_percent)) {
                $isValid = true;
                
                // Get raw attributes to ensure timezone consistency
                $rawStart = $cat->getRawOriginal('discount_starts_at');
                $rawExpiry = $cat->getRawOriginal('discount_expires_at');
                $now = Carbon::now('Asia/Kolkata'); // Using fixed timezone as requested
                
                if ($rawStart) {
                    $start = Carbon::parse($rawStart, 'Asia/Kolkata');
                    if ($now->lt($start)) {
                        $isValid = false;
                    }
                }

                if ($isValid && $rawExpiry) {
                    $exp = Carbon::parse($rawExpiry, 'Asia/Kolkata'); 
                    if ($now->gt($exp)) {
                        $isValid = false;
                    }
                }

                if ($isValid) {
                    $percent = (float) $cat->discount_percent;
                }
            } 
            
            // B. If no valid discount found from immediate category (inherited or expired), Check Parent
            if ($percent == 0 && $cat->parent && !is_null($cat->parent->discount_percent)) {
                 $rawStart = $cat->parent->getRawOriginal('discount_starts_at');
                 $rawExpiry = $cat->parent->getRawOriginal('discount_expires_at');
                 $now = Carbon::now('Asia/Kolkata');
                 
                 $isValid = true;

                 if ($rawStart) {
                    $start = Carbon::parse($rawStart, 'Asia/Kolkata');
                    if ($now->lt($start)) {
                        $isValid = false;
                    }
                 }

                 if ($isValid && $rawExpiry) {
                     $exp = Carbon::parse($rawExpiry, 'Asia/Kolkata');
                     if ($now->gt($exp)) {
                         $isValid = false;
                     }
                 }
                 
                 if ($isValid) {
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
