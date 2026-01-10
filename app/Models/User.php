<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'firstname',
        'lastname',
        'email',
        'phoneno',
        'address',
        'password',
        'role',
        'profile_photo',
        'last_login_at',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];

    // === ROLE CHECKS ===
    // Note: Since you removed 'role' from registration, 
    // ensure your database migration has a 'default' value for role.
    
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return (string)($this->role ?? '') === 'buyer';
    }

    public function isSeller(): bool
    {
        return (string)($this->role ?? '') === 'seller';
    }

    // === RELATIONSHIPS ===
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function sellerApplication()
    {
        return $this->hasOne(SellerApplication::class);
    }

    /**
     * Get the profile photo URL.
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo && Storage::disk('public')->exists($this->profile_photo)) {
            return asset('storage/' . $this->profile_photo);
        }

        // Fallback: firstname -> name -> email -> 'User'
        $name = $this->firstname ?: ($this->name ?: $this->email);
        return 'https://ui-avatars.com/api/?background=6d28d9&color=fff&name=' . urlencode($name ?? 'User');
    }
}