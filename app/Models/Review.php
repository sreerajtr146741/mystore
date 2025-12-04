<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['buyer_id','product_id','rating','comment'];

    public function buyer()   { return $this->belongsTo(User::class, 'buyer_id'); }
    public function product() { return $this->belongsTo(Product::class); } // if you have Product
}
