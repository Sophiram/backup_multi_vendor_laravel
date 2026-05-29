<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($validated['product_id']);

        // Check if user has purchased this product
        $hasPurchased = Order::where('user_id', $user->id)
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.product_id', $product->id)
            ->where('orders.status', 'delivered')
            ->exists();

        // Check if already reviewed
        $existingReview = ProductReview::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingReview) {
            $existingReview->update([
                'rating' => $validated['rating'],
                'review' => $validated['review']
            ]);
            return redirect()->back()->with('success', 'Review updated successfully');
        }

        ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => $validated['rating'],
            'review' => $validated['review'],
            'verified_purchase' => $hasPurchased
        ]);

        return redirect()->back()->with('success', 'Review posted successfully');
    }

    public function destroy($id)
    {
        $review = ProductReview::findOrFail($id);

        if ($review->user_id !== Auth::id() && Auth::user()->role !== 0) {
            abort(403);
        }

        $review->delete();

        return redirect()->back()->with('success', 'Review deleted successfully');
    }
}
