<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    
    protected $fillable = ['name', 'parent_id', 'discount_percent', 'discount_starts_at', 'discount_expires_at'];

    public function parent() {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products() {
        return $this->hasMany(Product::class);
    }

    protected $casts = [
        'discount_starts_at'  => 'datetime',
        'discount_expires_at' => 'datetime',
        'discount_percent'    => 'decimal:2',
    ];
}