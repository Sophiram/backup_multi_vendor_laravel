@extends('layouts.app')

@section('content')
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Product Details -->
            <div class="bg-white rounded-lg shadow-sm p-8 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Product Images -->
                    <div>
                        <div class="bg-gray-200 rounded-lg h-96 flex items-center justify-center mb-4">
                            @if ($product->images()->first())
                                <img id="mainImage" src="{{ Storage::url($product->images()->first()->image_path) }}"
                                    alt="{{ $product->product_name }}" class="w-full h-full object-cover rounded-lg">
                            @else
                                <img id="mainImage" src="https://via.placeholder.com/500x400" alt="Product Image"
                                    class="w-full h-full object-cover">
                            @endif
                        </div>
                        @if ($product->images()->count() > 1)
                            <div class="grid grid-cols-4 gap-2">
                                @foreach ($product->images() as $image)
                                    <img src="{{ Storage::url($image->image_path) }}" alt="Product"
                                        class="cursor-pointer rounded hover:opacity-75 transition h-20 object-cover"
                                        onclick="document.getElementById('mainImage').src='{{ Storage::url($image->image_path) }}'">
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div>
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->product_name }}</h1>
                                <p class="text-gray-600">SKU: {{ $product->sku }}</p>
                            </div>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                {{ $product->stock_status }}
                            </span>
                        </div>

                        <!-- Rating -->
                        <div class="flex items-center mb-6">
                            <div class="flex text-yellow-400">
                                @for ($i = 0; $i < 5; $i++)
                                    @if ($i < floor($product->average_rating))
                                        <span>★</span>
                                    @elseif($i < $product->average_rating)
                                        <span>⭐</span>
                                    @else
                                        <span>☆</span>
                                    @endif
                                @endfor
                            </div>
                            <span class="ml-2 text-gray-600">({{ $product->review_count }} reviews)</span>
                        </div>

                        <!-- Price -->
                        <div class="mb-6">
                            @if ($product->discounted_price)
                                <p class="text-4xl font-bold text-red-600 mb-2">
                                    ${{ number_format($product->discounted_price, 2) }}</p>
                                <p class="text-xl text-gray-400 line-through">
                                    ${{ number_format($product->regular_price, 2) }}</p>
                                <p class="text-green-600 mt-2">Save
                                    {{ round((1 - $product->discounted_price / $product->regular_price) * 100) }}%</p>
                            @else
                                <p class="text-4xl font-bold text-gray-900">
                                    ${{ number_format($product->regular_price, 2) }}</p>
                            @endif
                        </div>

                        <!-- Stock Info -->
                        <div class="mb-6">
                            <p class="text-sm text-gray-600">{{ $product->stock_quantity }} in stock</p>
                        </div>

                        <!-- Add to Cart -->
                        @auth
                            <form action="{{ route('cart.add') }}" method="POST" class="mb-6">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <div class="flex items-center gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity:</label>
                                        <input type="number" name="quantity" value="1" min="1"
                                            max="{{ $product->stock_quantity }}"
                                            class="px-3 py-2 border border-gray-300 rounded-md w-20">
                                    </div>
                                    <button type="submit"
                                        class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 transition mt-6">
                                        Add to Cart
                                    </button>
                                </div>
                            </form>
                        @else
                            <a href="{{ route('login') }}"
                                class="block bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 transition text-center mb-6">
                                Login to Buy
                            </a>
                        @endauth

                        <!-- Store Info -->
                        <div class="border-t pt-6">
                            <h3 class="font-semibold text-gray-900 mb-2">Sold by</h3>
                            <p class="text-gray-600">{{ $product->store->store_name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description & Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="md:col-span-2 bg-white rounded-lg shadow-sm p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Description</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>

                    @if ($product->meta_description)
                        <div class="mt-6 pt-6 border-t">
                            <h3 class="font-semibold text-gray-900 mb-2">Additional Info</h3>
                            <p class="text-gray-700">{{ $product->meta_description }}</p>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-lg shadow-sm p-8">
                    <h3 class="font-semibold text-gray-900 mb-4">Quick Info</h3>
                    <ul class="space-y-3 text-sm text-gray-700">
                        <li><strong>Category:</strong> {{ $product->category->category_name }}</li>
                        @if ($product->subcategory)
                            <li><strong>Sub-Category:</strong> {{ $product->subcategory->subcategory_name }}</li>
                        @endif
                        <li><strong>SKU:</strong> {{ $product->sku }}</li>
                        <li><strong>Tax Rate:</strong> {{ $product->tax_rate }}%</li>
                        <li><strong>Visibility:</strong> {{ $product->visibility ? 'Public' : 'Private' }}</li>
                    </ul>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="bg-white rounded-lg shadow-sm p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Customer Reviews ({{ $product->review_count }})</h2>

                <!-- Add Review Form -->
                @auth
                    <div class="mb-8 pb-8 border-b">
                        <h3 class="font-semibold text-gray-900 mb-4">Leave a Review</h3>
                        <form action="{{ route('review.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                <div class="flex gap-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="rating" value="{{ $i }}" required
                                                class="mr-2">
                                            <span class="text-2xl">★</span>
                                        </label>
                                    @endfor
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Review</label>
                                <textarea name="review" rows="4" placeholder="Share your experience with this product..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>

                            <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                                Submit Review
                            </button>
                        </form>
                    </div>
                @else
                    <p class="text-gray-600 mb-6"><a href="{{ route('login') }}"
                            class="text-indigo-600 hover:text-indigo-700">Login</a> to leave a review</p>
                @endauth

                <!-- Reviews List -->
                <div class="space-y-6">
                    @forelse($reviews as $review)
                        <div class="border-b pb-6">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $review->user->name }}</p>
                                    <div class="flex items-center gap-2">
                                        <div class="flex text-yellow-400">
                                            @for ($i = 0; $i < $review->rating; $i++)
                                                <span>★</span>
                                            @endfor
                                        </div>
                                        @if ($review->verified_purchase)
                                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Verified
                                                Purchase</span>
                                        @endif
                                    </div>
                                </div>
                                @if (auth()->check() && auth()->user()->id === $review->user_id)
                                    <form action="{{ route('review.destroy', $review->id) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 text-sm hover:text-red-700">Delete</button>
                                    </form>
                                @endif
                            </div>
                            <p class="text-gray-700 text-sm">{{ $review->review }}</p>
                            <p class="text-gray-500 text-xs mt-2">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-gray-600">No reviews yet. Be the first to review!</p>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if ($reviews->hasPages())
                    <div class="mt-6">
                        {{ $reviews->links() }}
                    </div>
                @endif
            </div>

            <!-- Related Products -->
            @if ($relatedProducts->count() > 0)
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Products</h2>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                        @foreach ($relatedProducts as $related)
                            <a href="{{ route('products.show', $related->id) }}"
                                class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition">
                                <div class="bg-gray-200 h-40 flex items-center justify-center">
                                    @if ($related->images()->first())
                                        <img src="{{ Storage::url($related->images()->first()->image_path) }}"
                                            alt="{{ $related->product_name }}" class="w-full h-full object-cover">
                                    @else
                                        <img src="https://via.placeholder.com/300x200" alt="Product Image">
                                    @endif
                                </div>
                                <div class="p-3">
                                    <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $related->product_name }}
                                    </h3>
                                    <p class="text-lg font-bold text-gray-900">
                                        ${{ number_format($related->regular_price, 2) }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
