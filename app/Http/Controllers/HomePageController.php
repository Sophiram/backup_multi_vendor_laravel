<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\Discount; // ✅ បានថែម Discount នៅទីនេះ
use App\Models\GiftCollection;
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
        $category = Category::where('category_name', $category_name)->firstOrFail();

        // $products = \App\Models\Product::where('category_id', $category->id)->get();
        $products = Product::with('images')->where('category_id', $category->id)->get();

        // 1. ថែមទិន្នន័យ HomePageSetting ដូចទំព័រ Index ដែរ
        $homepagesetting = \App\Models\HomePageSetting::with([
            'discountedProduct.images',
            'featuredProduct1.images',
            'featuredProduct2.images'
        ])->first();

        return view('home.category', compact('category', 'products', 'homepagesetting'));
    }
    public function showDiscounts()
    {
        $discounts = Discount::where('status', 1)
            ->latest()
            ->get();

        return view('home.discount', compact('discounts'));
    }

    public function showGiftCollections()
    {
        $giftCollections = GiftCollection::where('status', true)->latest()->get();
        return view('home.gift-collection', compact('giftCollections'));
    }

    public function showGiftDetail($id)
    {
        $gift = GiftCollection::where('status', true)->findOrFail($id);

        return view('home.gift-detail', compact('gift'));
    }

    public function showStores()
    {
        $stores = Store::where('status', 'approved')
            ->where('is_active', 1)
            ->latest()
            ->get();

        return view('home.store', compact('stores'));
    }

   public function storeDetails($slug)
    {
        $store = Store::with(['products' => function($query) {
                $query->where('status', 'Published'); // ✅ ប្រើប្រាស់ 'Published' តាម Table products ជាក់ស្តែង
            }])
            ->where('slug', $slug)
            ->where('status', 'approved')
            ->where('is_active', 1)
            ->firstOrFail();

        return view('home.store_details', compact('store'));
    }


    // ទំព័រអំពីយើង
    public function showAboutUs()
    {
        return view('home.about-us');
    }

    // ទំព័រព័ត៌មានដឹកជញ្ជូន
    public function showDeliveryInfo()
    {
        return view('home.delivery-info');
    }

    // ទំព័រគោលការណ៍ឯកជនភាព
    public function showPrivacyPolicy()
    {
        return view('home.privacy-policy');
    }

    // ទំព័រលក្ខខណ្ឌប្រើប្រាស់
    public function showTermsConditions()
    {
        return view('home.terms-conditions');
    }

}
