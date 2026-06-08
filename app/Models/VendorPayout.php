<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorPayout extends Model
{
    protected $fillable = [
        'vendor_id',
        'amount',
        'bank_details_snapshot',
        'status',
        'transaction_receipt'
    ];

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Vendor::class,
            'id',
            'id',
            'vendor_id',
            'user_id'
        );
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
