<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;
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