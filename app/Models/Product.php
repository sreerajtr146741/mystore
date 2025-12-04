<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // import for relation

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
        'image',          // or 'image_path' if that's your column
        'stock',
        'status',
        'slug',           // <-- missing comma was here

        // discount fields
        'discount_type',        // 'percent' | 'flat' | null
        'discount_value',       // numeric
        'discount_starts_at',   // datetime nullable
        'discount_ends_at',     // datetime nullable
        'is_discount_active',   // boolean
        'is_active',            // boolean (for public listing)
   
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
    protected $appends = ['final_price'];

    /* =========================
       Relationships
       ========================= */
    public function user()
    {
        return $this->belongsTo(User::class);
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
    public function getFinalPriceAttribute(): float
    {
        $base = (float) $this->price;
        $now  = Carbon::now();

        $windowOk = true;
        if ($this->discount_starts_at && $this->discount_ends_at) {
            $windowOk = $now->between($this->discount_starts_at, $this->discount_ends_at);
        }

        if (
            ($this->is_discount_active ?? false) &&
            $this->discount_type &&
            (float) $this->discount_value > 0 &&
            $windowOk
        ) {
            return max(0.0, $this->applyDiscount($base, (string)$this->discount_type, (float)$this->discount_value));
        }

        if (class_exists(\App\Models\Setting::class)) {
            $gActive = (bool) (int) (\App\Models\Setting::get('discount.global.active', 0));
            if ($gActive) {
                $gType  = (string) \App\Models\Setting::get('discount.global.type', 'percent');
                $gValue = (float)  \App\Models\Setting::get('discount.global.value', 0);
                $gStart = \App\Models\Setting::get('discount.global.starts_at');
                $gEnd   = \App\Models\Setting::get('discount.global.ends_at');

                $winOk = true;
                if ($gStart && $gEnd) {
                    $winOk = $now->between(Carbon::parse($gStart), Carbon::parse($gEnd));
                }

                if ($winOk && $gValue > 0) {
                    return max(0.0, $this->applyDiscount($base, $gType, $gValue));
                }
            }
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
