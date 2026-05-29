@extends('layouts.app')

@section('content')
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Products</h1>
                <p class="text-gray-600">Browse our collection of products from verified vendors</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Sidebar Filters -->
                <div class="bg-white p-6 rounded-lg shadow-sm h-fit">
                    <form method="GET" action="{{ route('products.index') }}" class="space-y-6">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" placeholder="Search products..."
                                value="{{ request('search') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                            <div class="space-y-2">
                                <input type="number" name="min_price" placeholder="Min Price"
                                    value="{{ request('min_price') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <input type="number" name="max_price" placeholder="Max Price"
                                    value="{{ request('max_price') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <!-- Sort -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                            <select name="sort"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Newest</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low
                                    to High</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price:
                                    High to Low</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular
                                </option>
                            </select>
                        </div>

                        <button type="submit"
                            class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                            Apply Filters
                        </button>
                    </form>
                </div>

                <!-- Products Grid -->
                <div class="md:col-span-3">
                    @if ($products->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach ($products as $product)
                                <a href="{{ route('products.show', $product->id) }}"
                                    class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition">
                                    <div class="relative bg-gray-200 h-48 flex items-center justify-center">
                                        @if ($product->images()->first())
                                            <img src="{{ Storage::url($product->images()->first()->image_path) }}"
                                                alt="{{ $product->product_name }}" class="w-full h-full object-cover">
                                        @else
                                            <img src="https://via.placeholder.com/300x200" alt="Product Image"
                                                class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        <h3 class="font-semibold text-gray-900 truncate">{{ $product->product_name }}</h3>
                                        <p class="text-sm text-gray-600 line-clamp-2">{{ $product->description }}</p>
                                        <div class="mt-2 flex items-center justify-between">
                                            <div>
                                                @if ($product->discounted_price)
                                                    <p class="text-lg font-bold text-red-600">
                                                        ${{ number_format($product->discounted_price, 2) }}</p>
                                                    <p class="text-sm text-gray-400 line-through">
                                                        ${{ number_format($product->regular_price, 2) }}</p>
                                                @else
                                                    <p class="text-lg font-bold text-gray-900">
                                                        ${{ number_format($product->regular_price, 2) }}</p>
                                                @endif
                                            </div>
                                            <div class="text-sm">
                                                <span class="text-yellow-400">★ {{ $product->average_rating }}</span>
                                                <p class="text-gray-600">({{ $product->review_count }})</p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                            <p class="text-gray-500 text-lg">No products found matching your criteria</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
