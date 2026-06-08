<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $category_name;
    public $price_range = 2000;
    public $sort_by = 'default';
    public $selected_stars = [];
    public $selected_subcategory_id = null;

    public function mount($category_name)
    {
        $this->category_name = $category_name;
    }

    public function updating($property)
    {
        if (in_array($property, ['price_range', 'sort_by', 'selected_stars', 'category_name', 'selected_subcategory_id'])) {
            $this->resetPage();
        }
    }

    public function changeCategory($name)
    {
        $this->category_name = $name;
        $this->selected_subcategory_id = null;
        $this->resetPage();
    }

    public function changeSubcategory($subId)
    {
        $this->selected_subcategory_id = $this->selected_subcategory_id == $subId ? null : $subId;
        $this->resetPage();
    }

    public function toggleWishlist($productId)
    {
        $wishlist = session()->get('wishlist', []);

        if (isset($wishlist[$productId])) {
            unset($wishlist[$productId]);
            $this->dispatch('wishlistUpdated');
            $this->dispatch('notify', [
                'title' => 'Successfully removed from wishlist',
                'type' => 'error',
            ]);
        } else {
            $product = Product::with('images')->findOrFail($productId);
            $productImage = $product->images->first() ? $product->images->first()->image_path : null;

            $wishlist[$productId] = [
                'name' => $product->product_name,
                'price' => $product->discounted_price > 0 && $product->discounted_price < $product->regular_price ? $product->discounted_price : $product->regular_price,
                'image' => $productImage,
            ];

            $this->dispatch('wishlistUpdated');
            $this->dispatch('notify', [
                'title' => 'Successfully added to wishlist',
                'type' => 'success',
            ]);
        }

        session()->put('wishlist', $wishlist);
    }

    public function render(): mixed
    {
        $category = Category::with('subcategories')->where('category_name', $this->category_name)->firstOrFail();
        $categories = Category::with('subcategories')->get();

        // បង្កើត SQL Statement សម្រាប់កំណត់យកតម្លៃលក់ពិតប្រាកដ
        $effectivePrice = 'CASE WHEN discounted_price > 0 AND discounted_price < regular_price THEN discounted_price ELSE regular_price END';

        $query = Product::with(['images', 'vendor.stores', 'productReviews'])
            ->where('status', 'published')
            ->where('category_id', $category->id)
            ->whereRaw("($effectivePrice) <= ?", [$this->price_range]); // Filter តាមតម្លៃលក់ជាក់ស្ដែង

        if ($this->selected_subcategory_id) {
            $query->where('subcategory_id', $this->selected_subcategory_id);
        }

        if (!empty($this->selected_stars)) {
            $query->whereHas('productReviews', function ($q) {
                $q->select('product_id')
                    ->groupBy('product_id')
                    ->havingRaw('FLOOR(AVG(rating)) IN (' . implode(',', array_map('intval', $this->selected_stars)) . ')');
            });
        }

        // Sort តាមតម្លៃលក់ជាក់ស្ដែង
        if ($this->sort_by === 'price_low_high') {
            $query->orderByRaw("($effectivePrice) ASC");
        } elseif ($this->sort_by === 'price_high_low') {
            $query->orderByRaw("($effectivePrice) DESC");
        } elseif ($this->sort_by === 'latest') {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(9);

        return view('livewire.product-by-category-component', [
            'products' => $products,
            'category' => $category,
            'categories' => $categories,
        ]);
    }
};
?>

<div>
    <div class="container py-2">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap"
            rel="stylesheet">

        <style>
            .category-page-wrapper {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }

            .category-banner {
                background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #93c5fd 100%) !important;
                border-radius: 24px;
                padding: 40px;
                color: #ffffff;
                margin-bottom: 25px;
                box-shadow: 0 15px 35px -10px rgba(59, 130, 246, 0.2);
            }

            .filter-card {
                background: #ffffff !important;
                border-radius: 22px;
                border: 1px solid rgba(226, 232, 240, 0.8);
                padding: 24px;
                position: sticky;
                top: 110px;
                box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.03);
            }

            .alibaba-sub-card {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 16px;
                transition: all 0.2s ease;
                cursor: pointer;
                text-decoration: none;
                display: block;
            }

            .alibaba-sub-card:hover,
            .alibaba-sub-card.active {
                border-color: #3b82f6;
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
                transform: translateY(-3px);
            }

            .alibaba-sub-card.active {
                background-color: #eff6ff;
                border-width: 2px;
            }

            /* --- Product Card Base --- */
            .product-card {
                background: #ffffff !important;
                border-radius: 16px;
                border: 1px solid rgba(226, 232, 240, 0.8);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                overflow: hidden;
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .product-card:hover {
                transform: translateY(-4px);
                border-color: #3b82f6;
                box-shadow: 0 15px 30px -10px rgba(59, 130, 246, 0.15);
            }

            .img-wrapper {
                background: #f8fafc;
                position: relative;
                overflow: hidden;
                padding-top: 100%;
            }

            .img-wrapper img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: contain;
                padding: 8px;
                transition: transform 0.5s ease;
            }

            .product-info-block {
                padding: 10px;
                flex-grow: 1;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                gap: 6px;
            }

            .product-title-text {
                font-family: 'Outfit', sans-serif;
                font-size: 0.82rem;
                font-weight: 600;
                color: #0f172a !important;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                height: 2.2rem;
                line-height: 1.1rem;
                margin-bottom: 2px;
            }

            /* --- Consistent & Responsive Cart CSS --- */
            .premium-stepper-container {
                display: flex;
                flex-direction: column;
                gap: 6px;
                width: 100%;
                margin-top: auto;
            }

            .stepper-input-group {
                border: 1px solid #cbd5e1;
                border-radius: 8px;
                overflow: hidden;
                background-color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                height: 30px;
            }

            .btn-stepper {
                border: none;
                background: #f1f5f9;
                color: #334155;
                width: 28px;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.75rem;
                cursor: pointer;
                transition: background 0.2s;
            }

            .btn-stepper:hover {
                background: #e2e8f0;
            }

            .qty-inline-input {
                border: none !important;
                background-color: transparent !important;
                color: #1e293b !important;
                font-weight: 700;
                font-size: 0.8rem;
                text-align: center;
                flex-grow: 1;
                width: 25px;
                padding: 0 !important;
            }

            .btn-premium-inline-cart {
                background: linear-gradient(135deg, #1e3a8a, #3b82f6) !important;
                color: #ffffff !important;
                font-weight: 600;
                font-size: 0.8rem;
                border: none !important;
                border-radius: 8px !important;
                width: 100%;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 4px;
                white-space: nowrap;
                transition: all 0.2s;
            }

            .btn-premium-inline-cart:hover {
                background: linear-gradient(135deg, #f97316, #ea580c) !important;
            }

            /* --- កែសម្រួលសម្រាប់ទូរស័ព្ទដៃ (Mobile Responsive) --- */
            @media (max-width: 767.98px) {
                .product-info-block {
                    padding: 8px;
                    gap: 4px;
                }

                .product-title-text {
                    font-size: 0.78rem;
                    height: 2rem;
                    line-height: 1rem;
                }

                .current-price {
                    font-size: 0.85rem !important;
                }

                .old-price {
                    font-size: 0.65rem !important;
                }

                .stepper-input-group {
                    height: 28px;
                }

                .btn-premium-inline-cart {
                    height: 28px;
                }

                .btn-premium-inline-cart span {
                    display: none;
                }

                .btn-premium-inline-cart i {
                    font-size: 0.9rem;
                    margin: 0;
                }
            }

            /* សម្រាប់អេក្រង់ធំ (Desktop) រៀបចំជាជួរដេកទន្ទឹមគ្នាពេញលេញ */
            @media (min-width: 768px) {
                .product-info-block {
                    padding: 14px;
                }

                .product-title-text {
                    font-size: 0.92rem;
                    height: 2.4rem;
                    line-height: 1.2rem;
                }

                .premium-stepper-container {
                    flex-direction: row;
                    align-items: center;
                    gap: 6px;
                }

                .stepper-input-group {
                    width: 75px;
                    flex-shrink: 0;
                    height: 34px;
                }

                .btn-premium-inline-cart {
                    flex-grow: 1;
                    width: auto;
                    height: 34px;
                }

                .btn-premium-inline-cart span {
                    display: inline !important;
                }
            }

            @media (min-width: 1200px) {
                .stepper-input-group {
                    width: 85px;
                }
            }

            .current-price {
                font-size: 0.95rem;
                font-weight: 800;
                color: #ef4444;
                font-family: 'Outfit';
            }

            .old-price {
                font-size: 0.72rem;
                color: #94a3b8;
                text-decoration: line-through;
                font-family: 'Outfit';
            }

            .stock-status-badge {
                font-size: 0.62rem;
                color: #10b981;
                font-weight: 600;
                background: #ecfdf5;
                padding: 1px 5px;
                border-radius: 4px;
            }

            .category-filter-btn {
                font-size: 0.9rem;
                text-align: left;
                transition: all 0.2s ease;
                border-radius: 10px !important;
                margin-bottom: 4px;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .category-filter-btn.active {
                background: linear-gradient(135deg, #1e3a8a, #3b82f6) !important;
                color: #ffffff !important;
                font-weight: 600;
            }
        </style>

        <div class="category-page-wrapper">
            {{-- Banner --}}
            <div class="category-banner animate__animated animate__fadeIn">
                <span class="badge bg-white text-dark mb-2 px-3 py-2 rounded-pill fw-bold text-uppercase shadow-sm"
                    style="font-size: 0.7rem; font-family: 'Outfit';">Collection</span>
                <h1 class="display-6 fw-extrabold m-0" style="font-family: 'Outfit', sans-serif; letter-spacing: -1px;">
                    {{ $category_name }}</h1>
                <p class="text-white-50 m-0 mt-2 small">Discover the best premium items under "{{ $category_name }}"
                    category.</p>
            </div>

            {{-- Subcategories Layout --}}
            @if ($category->subcategories && $category->subcategories->count() > 0)
                <div class="mb-5 animate__animated animate__fadeIn">
                    <h5 class="fw-bold text-dark mb-3" style="font-family: 'Outfit';">Shop by Subcategory</h5>
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                        @foreach ($category->subcategories as $sub)
                            <div class="col">
                                <div wire:click="changeSubcategory({{ $sub->id }})"
                                    class="alibaba-sub-card text-center p-3 h-100 {{ $selected_subcategory_id == $sub->id ? 'active' : '' }}">
                                    <div class="mx-auto mb-2 d-flex align-items-center justify-content-center bg-light rounded-circle"
                                        style="width: 70px; height: 70px; overflow:hidden;">
                                        @if ($sub->image && file_exists(public_path($sub->image)))
                                            <img src="{{ asset($sub->image) }}" alt="{{ $sub->subcategory_name }}"
                                                class="w-100 h-100 object-fit-cover">
                                        @else
                                            <img src="https://placehold.co/150x150?text={{ urlencode($sub->subcategory_name) }}"
                                                class="w-100 h-100 object-fit-cover">
                                        @endif
                                    </div>
                                    <div class="small fw-bold text-dark text-truncate">{{ $sub->subcategory_name }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="row g-2 g-md-4">
                {{-- Sidebar Filters --}}
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="filter-card border-0">
                        <h5 class="fw-bold mb-4 d-flex align-items-center justify-content-between text-dark"
                            style="font-family: 'Outfit';">
                            <span><i class="fa-solid fa-sliders text-primary me-2"></i>Filter Products</span>
                            @if (!empty($selected_stars) || $price_range < 2000 || $sort_by !== 'default' || $selected_subcategory_id)
                                <button class="btn btn-link p-0 text-muted small text-decoration-none"
                                    wire:click="$set('selected_stars', []); $set('price_range', 2000); $set('sort_by', 'default'); $set('selected_subcategory_id', null);"
                                    style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-rotate-left"></i> Reset
                                </button>
                            @endif
                        </h5>

                        {{-- Price Range --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark text-uppercase small"
                                style="letter-spacing: 0.5px;">Price Range</label>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">$10</span>
                                <span class="text-primary fw-bold px-2 py-1 rounded bg-light"
                                    style="font-family: 'Outfit'; font-size: 0.9rem;">Max: ${{ $price_range }}</span>
                            </div>
                            <input type="range" class="form-range" min="10" max="2000" step="10"
                                wire:model.live="price_range">
                        </div>

                        <hr class="my-4" style="border-color: rgba(0,0,0,0.06);">

                        {{-- Rating --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark text-uppercase small"
                                style="letter-spacing: 0.5px;">Product Rating</label>
                            <div class="d-flex flex-column gap-2 mt-2">
                                @for ($rating = 5; $rating >= 1; $rating--)
                                    <div class="form-check d-flex align-items-center gap-2 m-0 py-1">
                                        <input class="form-check-input mt-0" type="checkbox" value="{{ $rating }}"
                                            id="star_{{ $rating }}" wire:model.live="selected_stars"
                                            style="cursor: pointer; width: 17px; height: 17px;">
                                        <label class="form-check-label text-warning d-flex align-items-center gap-1"
                                            for="star_{{ $rating }}" style="cursor:pointer;">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="{{ $i <= $rating ? 'fa-solid fa-star' : 'fa-regular fa-star text-muted opacity-40' }}"></i>
                                            @endfor
                                            @if ($rating < 5)
                                                <span class="text-secondary small ms-1" style="font-size: 0.8rem;">&
                                                    up</span>
                                            @endif
                                        </label>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <hr class="my-4" style="border-color: rgba(0,0,0,0.06);">

                        {{-- Categories on Sidebar --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark text-uppercase small mb-2"
                                style="letter-spacing: 0.5px;">Product Categories</label>
                            <div class="d-flex flex-column" style="max-height: 350px; overflow-y: auto;">
                                @foreach ($categories as $cat)
                                    <button type="button" wire:click="changeCategory('{{ $cat->category_name }}')"
                                        class="btn btn-light category-filter-btn border-0 py-2 px-3 text-start {{ $category_name === $cat->category_name ? 'active' : 'text-secondary bg-transparent' }}">
                                        <div class="rounded-circle bg-white d-flex align-items-center justify-content-center border shadow-sm"
                                            style="width: 24px; height: 24px; overflow:hidden; flex-shrink:0;">
                                            @if ($cat->image && file_exists(public_path($cat->image)))
                                                <img src="{{ asset($cat->image) }}"
                                                    class="w-100 h-100 object-fit-cover">
                                            @else
                                                <i class="fa-solid fa-layer-group text-muted"
                                                    style="font-size: 10px;"></i>
                                            @endif
                                        </div>
                                        <span class="text-truncate">{{ $cat->category_name }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Product Grid Section --}}
                <div class="col-lg-9 col-12">
                    {{-- Toolbar --}}
                    <div class="d-flex flex-row justify-content-between align-items-center p-3 rounded-4 border mb-4 gap-2"
                        style="background: #ffffff; border-color: #e2e8f0 !important;">
                        <div class="text-muted small fw-semibold" style="font-family: 'Outfit';">
                            Showing <span class="text-dark fw-bold">{{ $products->count() }}</span> of <span
                                class="text-dark fw-bold">{{ $products->total() }}</span>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <select class="form-select toolbar-select rounded-3 small fw-semibold text-secondary py-1.5"
                                style="width: auto; min-width: 140px; font-size: 0.85rem;" wire:model.live="sort_by">
                                <option value="default">Sorting</option>
                                <option value="latest">Latest</option>
                                <option value="price_low_high">Price: Low-High</option>
                                <option value="price_high_low">Price: High-Low</option>
                            </select>
                            <button class="btn btn-light d-lg-none rounded-3 border-light-subtle py-1.5"
                                type="button" data-bs-toggle="collapse" data-bs-target="#mobileFilterCollapse">
                                <i class="fa-solid fa-filter"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Mobile Filter Dropdown --}}
                    <div class="collapse d-lg-none mb-4" id="mobileFilterCollapse">
                        <div class="p-3 rounded-4 border bg-white shadow-sm">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark small text-uppercase">Price Range
                                    (${{ $price_range }})</label>
                                <input type="range" class="form-range" min="10" max="2000"
                                    step="10" wire:model.live="price_range">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark small text-uppercase">Rating</label>
                                <div class="d-flex flex-wrap gap-2 mt-1">
                                    @for ($rating = 5; $rating >= 1; $rating--)
                                        <div
                                            class="form-check bg-light px-2 py-1.5 rounded-3 border d-inline-flex align-items-center gap-2 m-0">
                                            <input class="form-check-input m-0" type="checkbox"
                                                value="{{ $rating }}" id="m_star_{{ $rating }}"
                                                wire:model.live="selected_stars">
                                            <label
                                                class="form-check-label text-warning small fw-bold d-flex align-items-center gap-1"
                                                for="m_star_{{ $rating }}">
                                                {{ $rating }} <i class="fa-solid fa-star"></i>
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                            <div>
                                <label class="form-label fw-bold text-dark small text-uppercase">Categories</label>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    @foreach ($categories as $cat)
                                        <button type="button"
                                            wire:click="changeCategory('{{ $cat->category_name }}')"
                                            class="btn btn-sm btn-light border py-1 px-2.5 rounded-pill {{ $category_name === $cat->category_name ? 'bg-primary text-white border-primary' : 'text-secondary' }}">
                                            {{ $cat->category_name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Product Grid --}}
                    <div class="row g-2 g-md-3">
                        @forelse($products as $product)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="product-card">
                                    <div class="img-wrapper">
                                        <a href="{{ route('product.details', ['productId' => $product->id]) }}"
                                            class="d-block w-100 h-100">
                                            <img src="{{ $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : 'https://placehold.co/400x400?text=No+Image' }}"
                                                class="card-img-top" alt="{{ $product->product_name }}">
                                        </a>
                                        <button wire:click="toggleWishlist({{ $product->id }})"
                                            class="btn p-0 border-0 shadow-none position-absolute top-0 end-0 mt-2 me-2"
                                            style="z-index: 10;">
                                            @if (isset(session()->get('wishlist', [])[$product->id]))
                                                <i
                                                    class="fa-solid fa-heart fs-5 text-danger animate__animated animate__bounceIn"></i>
                                            @else
                                                <i class="fa-regular fa-heart fs-5 text-secondary"></i>
                                            @endif
                                        </button>
                                    </div>

                                    <div class="product-info-block">
                                        <div>
                                            <span class="text-muted text-uppercase fw-bold d-block"
                                                style="font-size: 0.6rem; letter-spacing: 0.5px;">
                                                {{ $product->vendor && $product->vendor->stores->first() ? $product->vendor->stores->first()->store_name : 'Official Store' }}
                                            </span>
                                            <h5 class="product-title-text" title="{{ $product->product_name }}">
                                                <a href="{{ route('product.details', ['productId' => $product->id]) }}"
                                                    class="text-dark text-decoration-none">
                                                    {{ $product->product_name }}
                                                </a>
                                            </h5>

                                            {{-- កែលម្អ Rating Stars ឱ្យបង្ហាញគ្រប់ ៥ ផ្កាយជានិច្ច --}}
                                            @php
                                                $avgRating =
                                                    $product->productReviews && $product->productReviews->count() > 0
                                                        ? $product->productReviews->avg('rating')
                                                        : 0;
                                                $fullStars = floor($avgRating);
                                                $halfStar = $avgRating - $fullStars >= 0.5 ? 1 : 0;
                                                $emptyStars = 5 - ($fullStars + $halfStar);
                                                $totalReviews = $product->productReviews
                                                    ? $product->productReviews->count()
                                                    : 0;
                                            @endphp
                                            <div class="d-flex align-items-center text-warning gap-1 mb-1"
                                                style="font-size: 0.68rem;">
                                                <div class="d-flex gap-0.5">
                                                    {{-- បង្ហាញផ្កាយពេញ --}}
                                                    @for ($i = 0; $i < $fullStars; $i++)
                                                        <i class="fa-solid fa-star"></i>
                                                    @endfor

                                                    {{-- បង្ហាញផ្កាយកន្លះ --}}
                                                    @if ($halfStar)
                                                        <i class="fa-solid fa-star-half-stroke"></i>
                                                    @endif

                                                    {{-- បង្ហាញផ្កាយទទេ (Empty Stars) ឱ្យគ្រប់ចំនួន ៥ --}}
                                                    @for ($i = 0; $i < $emptyStars; $i++)
                                                        <i class="fa-regular fa-star text-muted"
                                                            style="opacity: 0.4;"></i>
                                                    @endfor
                                                </div>
                                                @if ($totalReviews > 0)
                                                    <span class="text-muted"
                                                        style="font-size: 0.65rem;">({{ $totalReviews }})</span>
                                                @endif
                                            </div>

                                            {{-- Price --}}
                                            <div class="d-flex align-items-center justify-content-between gap-1 mb-2">
                                                <div class="d-flex align-items-baseline flex-wrap gap-1">
                                                    @if ($product->discounted_price > 0 && $product->discounted_price < $product->regular_price)
                                                        <span
                                                            class="current-price">${{ number_format($product->discounted_price, 2) }}</span>
                                                        <span
                                                            class="old-price">${{ number_format($product->regular_price, 2) }}</span>
                                                    @else
                                                        <span
                                                            class="current-price">${{ number_format($product->regular_price, 2) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Stepper & Button (Responsive & Consistent Section) --}}
                                        <div class="premium-stepper-container" x-data="{ quantity: 1, maxStock: {{ $product->stock_quantity ?? 10 }} }">
                                            <div class="stepper-input-group">
                                                <button type="button" class="btn-stepper"
                                                    @click="if(quantity > 1) quantity--"><i
                                                        class="fa-solid fa-minus"></i></button>
                                                <input type="number" x-model="quantity"
                                                    class="form-control qty-inline-input" readonly>
                                                <button type="button" class="btn-stepper"
                                                    @click="if(quantity < maxStock) quantity++"><i
                                                        class="fa-solid fa-plus"></i></button>
                                            </div>
                                            <button type="button"
                                                @click="$dispatch('addToCartFromAnywhere', { productId: {{ $product->id }}, quantity: quantity })"
                                                class="btn btn-premium-inline-cart">
                                                <i class="fa-solid fa-basket-shopping"></i><span>Add to Cart</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <div class="p-5 rounded-5 border max-width-md mx-auto"
                                    style="background: rgba(255,255,255,0.6);">
                                    <i class="fa-solid fa-box-open text-muted mb-3 display-4"></i>
                                    <h4 class="fw-bold text-dark">No Products Found</h4>
                                    <button class="btn btn-primary rounded-3 px-4 py-2 mt-2"
                                        wire:click="$set('selected_stars', []); $set('price_range', 2000); $set('selected_subcategory_id', null);">Reset
                                        Filter</button>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-center mt-5">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('notify', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            Swal.fire({
                title: data.title || 'Success!',
                icon: data.type || 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: true,
                background: '#ffffff',
                color: '#1e293b',
                iconColor: data.type === 'success' ? '#10b981' : '#ef4444',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        });
    });
</script>
