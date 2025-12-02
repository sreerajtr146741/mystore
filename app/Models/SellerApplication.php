<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_address',
        'gst_number',
        'status',
        'admin_note',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationship: Each application belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor: Check if pending/approved/rejected
    public function isPending()   { return $this->status === 'pending'; }
    public function isApproved()  { return $this->status === 'approved'; }
    public function isRejected()  { return $this->status === 'rejected'; }
}