<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vendor_id',
        'store_name',
        'slug',
        'details',
        'logo',
        'address',
        'store_email',
        'store_phone',
        'status',
        'commission_rate',
        'is_active'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
    public function products(): HasMany // 👈 វានឹងស្គាល់លក្ខណៈសម្បត្តិ HasMany ត្រឹមត្រូវពី Eloquent
    {
        return $this->hasMany(Product::class, 'store_id');
    }
//     public function user()
// {
//     return $this->belongsTo(\App\Models\User::class);
// }
}
