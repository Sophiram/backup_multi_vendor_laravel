<?php

use App\Models\Product;
use App\Models\Category;
use Livewire\Volt\Component;
use Illuminate\Pagination\Paginator;
use Livewire\Attributes\On;

new class extends Component {
    public $selectedCategory = null;
    public $categories = [];

    public function mount()
    {
        $this->categories = Category::all();
    }
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
    public function filterByCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
    }

    #[On('addToCartFromAnywhere')]
    public function addToCartFromAnywhere($productId = null, $quantity = 1): void
    {
        $quantity = intval($quantity);

        if (!$productId || $quantity < 1) {
            return;
        }

        $product = Product::with('images')->find($productId);
        if (!$product) {
            return;
        }

        $cart = session()->get('cart', []);
        $price = $product->discounted_price > 0 && $product->discounted_price < $product->regular_price ? (float) $product->discounted_price : (float) $product->regular_price;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'name' => $product->product_name,
                'price' => $price,
                'quantity' => $quantity,
                'image' => $product->images->first()?->image_path,
            ];
        }

        session()->put('cart', $cart);

        $this->dispatch('cart-updated');
        $this->dispatch('notify', [
            'title' => 'Successfully added to cart!',
            'type' => 'success',
        ]);
    }

    public function toggleWishlist($productId)
    {
        $wishlist = session()->get('wishlist', []);

        if (isset($wishlist[$productId])) {
            unset($wishlist[$productId]);
            $this->dispatch('wishlistUpdated');
            $this->dispatch('$refresh');
            $this->dispatch('notify', [
                'title' => 'Successfully removed from wishlist',
                'type' => 'error',
            ]);
        } else {
            $product = Product::with('images')->find($productId);
            if (!$product) {
                return;
            }

            $productImage = $product->images->first() ? $product->images->first()->image_path : null;

            $wishlist[$productId] = [
                'name' => $product->product_name,
                'price' => $product->discounted_price > 0 && $product->discounted_price < $product->regular_price ? $product->discounted_price : $product->regular_price,
                'image' => $productImage,
            ];

            $this->dispatch('wishlistUpdated');
            $this->dispatch('$refresh');
            $this->dispatch('notify', [
                'title' => 'Successfully added to wishlist',
                'type' => 'success',
            ]);
        }

        session()->put('wishlist', $wishlist);
    }

    public function with(): array
    {
        return [
            'products' => Product::with(['images', 'productReviews'])
                ->where('status', 'published')
                ->when($this->selectedCategory, function ($query) {
                    $query->where('category_id', $this->selectedCategory);
                })
                ->paginate(10),
        ];
    }
}; ?>

<div>
    <section id="product-header" class="mt-4 mt-md-5 mb-4">
        <div class="row">
            <div class="col-12 text-center mb-3 mb-md-4">
                <h5 class="text-muted text-uppercase tracking-wider small mb-2"
                    style="font-size: 0.75rem; letter-spacing: 1px;">Discover Your Required Product</h5>
                <h2 class="fw-bold text-dark header-main-title fs-3 fs-md-2">From 267+ Different Vendors, 30+ Categories
                </h2>
            </div>

            <div class="col-12 mb-2 mb-md-4 position-relative">
                <div class="category-scroll-wrapper position-relative">

                    <div
                        class="category-scroll-container d-flex flex-nowrap flex-md-wrap align-items-center justify-content-start justify-content-md-center gap-2 pb-3">

                        <button wire:click="filterByCategory(null)"
                            class="btn px-3 py-2 rounded-3 d-flex align-items-center gap-2 fw-semibold shadow-sm transition-all text-nowrap
                        {{ $selectedCategory === null ? 'btn-danger text-white' : 'btn-light text-danger border-0' }}"
                            style="{{ $selectedCategory === null ? 'background: linear-gradient(135deg, #dc3545, #bd2130);' : 'background-color: #ffeef0;' }}">
                            <i class="fas fa-fire-alt"></i>
                            <span>Hot in Sale</span>
                        </button>

                        @foreach ($categories as $category)
                            <button wire:key="category-{{ $category->id }}"
                                wire:click="filterByCategory({{ $category->id }})"
                                class="btn px-3 py-2 rounded-3 fw-medium transition-all shadow-sm text-nowrap
                            {{ $selectedCategory === $category->id ? 'btn-primary text-white' : 'btn-light text-primary border-0' }}"
                                style="{{ $selectedCategory === $category->id ? 'background: linear-gradient(135deg, #0d6efd, #0a58ca);' : 'background-color: #e0e7ff; color: #4338ca;' }}">
                                {{ $category->category_name }}
                            </button>
                        @endforeach

                    </div>

                </div>
            </div>
        </div>

    </section>

    <div class="mt-4 mt-md-5">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 small py-2" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4">
            @forelse($products as $product)
                <div class="col d-flex align-items-stretch" wire:key="product-card-{{ $product->id }}">
                    <div
                        class="card w-100 border-0 custom-product-card position-relative overflow-hidden d-flex flex-column">

                        <div class="position-relative product-img-container">
                            <a href="{{ route('product.details', ['productId' => $product->id]) }}"
                                class="d-block w-100 h-100">
                                <img src="{{ $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : 'https://placehold.co/300x300?text=No+Image' }}"
                                    class="card-img-top product-main-img" alt="{{ $product->product_name }}">
                            </a>

                            <button wire:click="toggleWishlist({{ $product->id }})"
                                class="btn p-0 border-0 shadow-none position-absolute top-0 end-0 mt-2 me-2 mt-md-3 me-md-3"
                                style="z-index: 10;">
                                @if (isset(session()->get('wishlist', [])[$product->id]))
                                    <i
                                        class="fa-solid fa-heart fs-5 fs-md-4 text-danger animate__animated animate__bounceIn"></i>
                                @else
                                    <i class="fa-regular fa-heart fs-5 fs-md-4 text-secondary"></i>
                                @endif
                            </button>

                            @if ($product->status === 'published')
                                <span
                                    class="badge position-absolute top-0 start-0 m-2 m-md-3 px-2 py-1 vendor-premium-badge">
                                    <i class="fa-solid fa-circle-check me-1 text-primary"></i> Verified
                                </span>
                            @endif
                        </div>

                        <div class="card-body p-2 p-md-3 d-flex flex-column justify-content-between flex-grow-1">
                            <div>
                                <h5 class="product-title mb-1" title="{{ $product->product_name }}">
                                    <a href="{{ route('product.details', ['productId' => $product->id]) }}"
                                        class="text-dark text-decoration-none">
                                        {{ $product->product_name }}
                                    </a>
                                </h5>

                                <div class="d-flex align-items-center gap-1 mb-2" style="font-size: 0.78rem;">
                                    @php
                                        $reviewCount = $product->productReviews->count();
                                        $avgRating =
                                            $reviewCount > 0 ? round($product->productReviews->avg('rating')) : 0;
                                    @endphp

                                    <div class="text-warning">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $avgRating)
                                                &#9733;
                                            @else
                                                &#9734;
                                            @endif
                                        @endfor
                                    </div>

                                    @if ($reviewCount > 0)
                                        <span class="text-muted">({{ $reviewCount }})</span>
                                    @else
                                        <span class="text-light-emphasis text-muted" style="font-size: 0.7rem;">(0
                                            reviews)</span>
                                    @endif
                                </div>

                                <p class="product-short-desc mb-2 mb-md-3">
                                    {{ $product->description ?? 'No description available for this premium item.' }}
                                </p>
                            </div>

                            <div class="mt-auto">
                                <div
                                    class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-2 mb-md-3">
                                    <div class="price-section">
                                        <span class="price-label">Price</span>
                                        <div class="d-flex align-items-baseline gap-1">
                                            <span class="current-price fs-6 fs-md-5">
                                                ${{ number_format($product->discounted_price > 0 && $product->discounted_price < $product->regular_price ? $product->discounted_price : $product->regular_price, 2) }}
                                            </span>

                                            @if ($product->discounted_price > 0 && $product->discounted_price < $product->regular_price)
                                                <span class="old-price small">
                                                    ${{ number_format($product->regular_price, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="stock-status-badge">
                                        <i class="fa-solid fa-box small d-none d-sm-inline"></i>
                                        {{ $product->stock_status ?? 'In Stock' }}
                                    </div>
                                </div>

                                <div x-data="{ quantity: 1, maxStock: {{ $product->stock_quantity ?? 99 }} }" wire:ignore.self class="mt-2">
                                    <div class="responsive-cart-container">

                                        <div class="custom-qty-stepper">
                                            <button type="button" @click="if(quantity > 1) quantity--"
                                                class="qty-control-btn">-</button>
                                            <input type="number" x-model.number="quantity"
                                                @input="if(quantity < 1) quantity = 1; if(quantity > maxStock) quantity = maxStock;"
                                                min="1" class="qty-modern-input" aria-label="Quantity">
                                            <button type="button" @click="if(quantity < maxStock) quantity++"
                                                class="qty-control-btn">+</button>
                                        </div>

                                        <button type="button"
                                            x-on:click="$wire.addToCartFromAnywhere({{ $product->id }}, quantity)"
                                            class="btn-premium-inline-cart">
                                            <i class="fa-solid fa-basket-shopping"></i>
                                            <span class="d-none d-sm-inline">Add to Cart</span>
                                            <span class="d-inline d-sm-none">Add</span>
                                        </button>

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fa-solid fa-box-open text-muted mb-3" style="font-size: 3rem;"></i>
                    <h5 class="text-muted fw-normal">No products found in database.</h5>
                </div>
            @endforelse
        </div>
    </div>
    <div
        class="card-footer bg-white py-3 border-top border-light d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
        <small class="text-muted small">
            Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of
            {{ $products->total() }} entries
        </small>
        <nav aria-label="Pagination">
            {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
        </nav>
    </div>
</div>

<style>
    .responsive-cart-container {
        display: flex;
        gap: 6px;
        align-items: center;
        width: 100%;
    }

    .category-scroll-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        white-space: nowrap;
    }

    /* ==========================================================================
       SCROLLBAR GLOBAL CUSTOMIZATION (លុបសញ្ញាព្រួញទាំងស្រុងគ្រប់ Screen និង Zoom Level)
       ========================================================================== */
    .category-scroll-container::-webkit-scrollbar-button {
        display: none !important;
        width: 0px !important;
        height: 0px !important;
        background: transparent !important;
    }

    @media (max-width: 767.98px) {
        .category-scroll-container {
            scrollbar-width: thin;
            scrollbar-color: #ef0077 #f3e8ff;
            padding-bottom: 12px !important;
        }

        .category-scroll-container::-webkit-scrollbar {
            height: 4px;
        }

        .category-scroll-container::-webkit-scrollbar-track {
            background: #f3e8ff !important;
            border-radius: 10px;
        }

        .category-scroll-container::-webkit-scrollbar-thumb {
            background: #ef0077 !important;
            border-radius: 10px;
        }

        .category-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #d90067 !important;
        }

        .category-scroll-container::-webkit-scrollbar-button,
        .category-scroll-container::-webkit-scrollbar-button:start,
        .category-scroll-container::-webkit-scrollbar-button:end {
            display: none !important;
            width: 0px !important;
            height: 0px !important;
        }
    }

    @media (min-width: 768px) {
        .category-scroll-container::-webkit-scrollbar {
            display: none !important;
        }
    }

    /* Modern Quantity Stepper */
    .custom-qty-stepper {
        display: flex;
        align-items: center;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        background: #f8fafc;
        height: 36px;
        flex-shrink: 0;
    }

    .qty-control-btn {
        border: none;
        background: transparent;
        width: 26px;
        height: 100%;
        font-weight: 700;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }

    .qty-control-btn:hover {
        background: #cbd5e1;
        color: #0f172a;
    }

    .qty-modern-input {
        width: 30px !important;
        border: none !important;
        background: transparent !important;
        text-align: center;
        font-weight: 700;
        font-size: 0.9rem;
        color: #1e293b;
        padding: 0 !important;
        box-shadow: none !important;
        outline: none !important;
    }

    .qty-modern-input::-webkit-outer-spin-button,
    .qty-modern-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .qty-modern-input {
        -moz-appearance: textfield;
    }

    /* Premium Add to Cart Button */
    .btn-premium-inline-cart {
        flex-grow: 1;
        height: 36px;
        background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.8rem;
        border: none !important;
        border-radius: 10px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.25s ease-in-out !important;
        white-space: nowrap;
    }

    .btn-premium-inline-cart:hover {
        background: linear-gradient(135deg, #4f46e5, #3730a3) !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        transform: translateY(-1px);
    }

    /* Card & Animations */
    .custom-product-card {
        background: #ffffff;
        border-radius: 16px !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .custom-product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(99, 102, 241, 0.1);
    }

    .product-img-container {
        height: 200px;
        background-color: #f8fafc;
        border-radius: 16px 16px 0 0;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px;
    }

    .product-main-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: transform 0.5s ease;
    }

    .custom-product-card:hover .product-main-img {
        transform: scale(1.05);
    }

    .vendor-premium-badge {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(4px);
        color: #1e293b;
        font-weight: 600;
        font-size: 0.7rem;
        border-radius: 6px !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .product-title {
        font-size: 0.9rem;
        font-weight: 700;
        line-height: 1.3;
        height: 2.4rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-short-desc {
        font-size: 0.8rem;
        color: #64748b;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.2rem;
        line-height: 1.3;
    }

    .price-label {
        font-size: 0.7rem;
        color: #94a3b8;
        display: block;
        text-transform: uppercase;
        font-weight: 600;
    }

    .current-price {
        font-weight: 800;
        color: #ef4444;
    }

    .old-price {
        color: #94a3b8;
        text-decoration: line-through;
    }

    .stock-status-badge {
        font-size: 0.7rem;
        color: #10b981;
        font-weight: 600;
        background: #ecfdf5;
        padding: 2px 8px;
        border-radius: 6px;
    }

    @media (max-width: 767.98px) {
        .category-scroll-container {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }

        .product-img-container {
            height: 150px;
        }

        .row-cols-2>.col {
            padding-left: 6px;
            padding-right: 6px;
        }

        .g-3 {
            --bs-gutter-x: 0.75rem;
            --bs-gutter-y: 0.75rem;
        }
    }
</style>

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
                timer: 1500,
                timerProgressBar: true,
                background: '#ffffff',
                color: '#1e293b',
                iconColor: data.type === 'success' ? '#10b981' : '#ef4444'
            });
        });
    });
</script>
