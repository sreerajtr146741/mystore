<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

   protected $fillable = [
    'name','email','password','phone','address','profile_photo',                
];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // ADD THIS METHOD — THIS IS THE FIX
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    // END OF FIX
}