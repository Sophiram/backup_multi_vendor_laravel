<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
   public function index(Request $request) {
    // បង្កើត query សម្រាប់យកទិន្នន័យទាំងអស់ដើម្បីគណនាស្ថិតិ
    $baseQuery = \App\Models\Product::query();

    $data['totalProducts'] = $baseQuery->count();
    $data['activeProducts'] = $baseQuery->clone()->where('status', 'Published')->count();
    $data['lowStock'] = $baseQuery->clone()->where('stock_quantity', '<', 5)->where('stock_quantity', '>', 0)->count();
    $data['outOfStock'] = $baseQuery->clone()->where('stock_quantity', 0)->count();

    // បង្កើត query សម្រាប់តារាង (មាន filter)
    $query = \App\Models\Product::with(['category', 'images']);

    // Filter Logic
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    if ($request->filled('stock_status')) {
        if ($request->stock_status == 'low_stock') {
            $query->where('stock_quantity', '<', 5)->where('stock_quantity', '>', 0);
        } elseif ($request->stock_status == 'out_of_stock') {
            $query->where('stock_quantity', 0);
        } elseif ($request->stock_status == 'in_stock') {
            $query->where('stock_quantity', '>=', 5);
        }
    }

    $products = $query->latest()->paginate(10);

    return view('admin.product.manage', compact('products', 'data'));
}

    public function review_manage()
    {
        return view('admin.product.manage_product_review');
    }


    public function edit($id) {
    $product = \App\Models\Product::findOrFail($id);
    return view('admin.product.edit', compact('product'));
}

public function update(Request $request, $id) {
    // កូដសម្រាប់ Update ផលិតផល
    $product = \App\Models\Product::findOrFail($id);
    $product->update($request->all());

    return redirect()->route('product.manage')->with('success', 'Product updated successfully!');
}


}
