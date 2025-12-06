<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model {
    protected $fillable = ['email', 'otp', 'expires_at'];

    public function isExpired() {
        return $this->expires_at < now();
    }
}