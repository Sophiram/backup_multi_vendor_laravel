<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Services\KhqrService;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Models\IndividualInfor;;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'store', 'images'])
            ->withAvg('reviews', 'rating')
            ->where('status', 'Published')
            ->where('visibility', true)
            ->filter($request->only(['category', 'search', 'min_price', 'max_price']));

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'new':
                    $query->orderByDesc('created_at');
                    break;
                case 'price_low':
                    $query->orderBy('regular_price');
                    break;
                case 'price_high':
                    $query->orderByDesc('regular_price');
                    break;
                case 'popular':
                    $query->withCount('reviews')->orderByDesc('reviews_count');
                    break;
                default:
                    $query->orderByDesc('created_at');
            }
        } else {
            $query->orderByDesc('created_at');
        }

        $products = $query->paginate(12);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    public function show($id)
{
    // បន្ថែម 'attributes.attribute', 'attributes.attributeValue' ចូលទៅក្នុង with()
    $product = Product::with(['category', 'store', 'images', 'vendor', 'attributes.attribute', 'attributes.attributeValue'])
        ->withAvg('reviews', 'rating')
        ->findOrFail($id);

    $relatedProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->limit(5)
        ->get();

    $reviews = $product->reviews()->paginate(5);

    return view('products.show', compact('product', 'relatedProducts', 'reviews'));
}
}
