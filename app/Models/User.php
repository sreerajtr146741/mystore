<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'profile_photo',
        'role',
        'products_count',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => 'string',
    ];

    // === ROLE CHECKS ===
    public function isAdmin(): bool
{
    return (string)($this->role ?? '') === 'admin';
}

    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }

    public function isUser(): bool
    {
        return $this->role === 'buyer';
    }

    // === RELATIONSHIPS ===
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function sellerApplication()
    {
        return $this->hasOne(SellerApplication::class);
    }
    
}
