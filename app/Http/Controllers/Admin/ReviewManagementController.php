<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review; // ប្រើ Model តែមួយ
use Illuminate\Http\Request;

class ReviewManagementController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['product', 'user'])->latest()->paginate(10);
        $avgRating = Review::avg('rating');
        $totalReviews = Review::count();
        $pendingCount = Review::where('status', 'pending')->count();
        $flaggedCount = Review::where('status', 'flagged')->count();

        return view('admin.reviews.index', compact('reviews', 'avgRating', 'totalReviews', 'pendingCount', 'flaggedCount'));
    }
    // នៅក្នុង App\Http\Controllers\Admin\ReviewManagementController.php

    public function manageReview()
    {
        $reviews = \App\Models\Review::with(['product', 'user'])->latest()->paginate(10);

        // កែសម្រួលត្រង់នេះ៖ លុបកូដដែលប្រើ 'status' ចេញ
        $stats = [
            'total'    => \App\Models\Review::count(),
            'avg'      => number_format(\App\Models\Review::avg('rating'), 1),
            'pending'  => 0, // កំណត់ជា 0 ព្រោះគ្មាន status
            'approved' => 0  // កំណត់ជា 0 ព្រោះគ្មាន status
        ];

        return view('admin.product.manage_product_review', compact('reviews', 'stats'));
    }

    public function update($id)
    {
        $review = Review::findOrFail($id);
        $review->status = 'approved';
        $review->save();

        return redirect()->back()->with('success', 'Review approved successfully!');
    }

    public function reject($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success', 'Review rejected and deleted');
    }
}
