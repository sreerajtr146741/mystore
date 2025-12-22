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
     * Fixed: Added firstname, lastname, phoneno.
     * Removed: address and role (as requested).
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phoneno', // Changed from 'phone' to 'phoneno' to match your JSON
        'address', // Added back for profile updates
        'password',
        'profile_photo',
        'role', // Added back for AuthController logic
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
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // === ROLE CHECKS ===
    // Note: Since you removed 'role' from registration, 
    // ensure your database migration has a 'default' value for role.
    
    public function isAdmin(): bool
    {
        return (string)($this->role ?? '') === 'admin';
    }

    public function isUser(): bool
    {
        return (string)($this->role ?? '') === 'buyer';
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

    /**
     * Get the profile photo URL.
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo && Storage::disk('public')->exists($this->profile_photo)) {
            return asset('storage/' . $this->profile_photo);
        }

        // Using firstname for the avatar since 'name' was removed
        return 'https://ui-avatars.com/api/?background=6d28d9&color=fff&name=' . urlencode($this->firstname);
    }
}