<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image',
        'start_at',
        'end_at',
        'sort_order',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
