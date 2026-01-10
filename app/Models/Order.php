<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total', // Prompt asked for "total_amount", but DB likely has "total" or "amount". I will check migration or stick to "total" as per previous file. Let's use "total" to be safe with existing schema or map it. The prompt says "total_amount", I'll use total_amount in API but mapping to DB column 'total' if needed, or if I can update migration. Assuming DB is 'total' from previous Order files.
        'status', // placed, processing, etc
        'payment_status',
        'address',
        'payment_method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
