<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'total', 'status', 'payment_method', 'payment_status', 'shipping_address', 'delivery_date'
    ];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    public function items() {
        return $this->hasMany(OrderItem::class);
    }
}
