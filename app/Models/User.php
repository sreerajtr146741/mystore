<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * Keep ALL previously used fields:
     * - first_name/last_name (from version 1)
     * - name, profile_photo, role, products_count (from version 2)
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'email',
        'address',
        'phone',
        'password',
        'profile_photo',
        'role',
        'status',
        'status',
        'products_count',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => 'string',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // === ROLE CHECKS ===
    public function isAdmin(): bool
    {
        // MERGE: Allow both 'admin' and 'seller' (legacy) to access admin panel
        return (string)($this->role ?? '') === 'admin' || (string)($this->role ?? '') === 'seller';
    }

    /**
     * @deprecated Seller role is merged into Admin. Use isAdmin() instead.
     */
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
