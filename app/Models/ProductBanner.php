<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBanner extends Model
{
    protected $fillable = [
        'product_id',
        'image',
        'start_at',
        'end_at',
        'sort_order',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
      Get the product that owns the banner.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
