<?php

namespace App\Http\Controllers;

use App\Models\HomePageSetting;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function index()
    {
        $homepagesetting = HomePageSetting::with([
            'discountedProduct.images',
            'featuredProduct1.images',
            'featuredProduct2.images'
        ])->first();
        $latestProducts = \App\Models\Product::with('images')
        ->where('status', 'Published')
        ->latest()
        ->take(8)
        ->get();
        return view('home.index', compact('homepagesetting', 'latestProducts'));
    }

 public function showCategoryProducts($category_name)
{
    $category = \App\Models\Category::where('category_name', $category_name)->firstOrFail();

    $products = \App\Models\Product::where('category_id', $category->id)->get();

    // 1. ថែមទិន្នន័យ HomePageSetting ដូចទំព័រ Index ដែរ
    $homepagesetting = \App\Models\HomePageSetting::with([
        'discountedProduct.images',
        'featuredProduct1.images',
        'featuredProduct2.images'
    ])->first();

    // 2. បោះ variable $homepagesetting ទៅកាន់ view តាមរយៈ compact
    return view('home.category', compact('category', 'products', 'homepagesetting'));
}
}
