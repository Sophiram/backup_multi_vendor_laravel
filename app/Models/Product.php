<?php

namespace App\Models;

use App\Http\Controllers\Admin\ProductAttributeController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'description',
        'sku',
        'vendor_id',
        'category_id',
        'subcategory_id',
        'store_id',
        'regular_price',
        'discounted_price',
        'tax_rate',
        'stock_quantity',
        'stock_status',
        'slug',
        'visibility',
        'meta_title',
        'meta_description',
        'status',

    ];

    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function subcategory(){
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function store(){
        return $this->belongsTo(Store::class, 'store_id');
    }
    public function vendor(){
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function images(){
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function attributes()
    {
    return $this->hasMany(ProductAttribute::class, 'product_id');
    }

    // នៅក្នុង App\Models\Product.php
public function primaryImage() {
    return $this->hasOne(\App\Models\ProductImage::class, 'product_id', 'id')
                ->where('is_primary', 1);
}
}
