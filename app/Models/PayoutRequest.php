<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'admin_note',
    ];
     public function user() {
        return $this->belongsTo(User::class);
    }
    public function vendor()
    {
        // ភ្ជាប់ទៅកាន់ Model Vendor តាមរយៈ vendor_id
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
