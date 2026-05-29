<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
protected $table = 'product_reviews';

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'review',
        'verified_purchase', // បន្ថែមវាចូល
        'created_at',
        'updated_at'
    ];
    // តភ្ជាប់ទៅ Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // តភ្ជាប់ទៅ User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
