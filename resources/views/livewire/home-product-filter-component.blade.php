<?php

use App\Models\Product;
use App\Models\Category;
use Livewire\Volt\Component;

new class extends Component {
    public $selectedCategory = null;
    public $categories = [];

    public function mount()
    {
        $this->categories = Category::all();
    }

    public function filterByCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
    }

    #[On('addToCartFromAnywhere')]
    public function addToCartFromAnywhere($productId = null, $quantity = 1): void
    {
        $quantity = intval($quantity);

        if (!$productId) {
            return;
        }

        $product = Product::with('images')->find($productId);
        if (!$product) {
            return;
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'name' => $product->product_name,
                'price' => $product->discounted_price ?? $product->regular_price,
                'quantity' => $quantity,
                'image' => $product->images && $product->images->first() ? $product->images->first()->image_path : null,
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
            $this->dispatch('notify', [
                'title' => 'Successfully removed from wishlist',
                'type' => 'error',
            ]);
        } else {
            $product = \App\Models\Product::with('images')->findOrFail($productId);
            $productImage = $product->images->first() ? $product->images->first()->image_path : null;

            $wishlist[$productId] = [
                'name' => $product->product_name,
                'price' => $product->discounted_price ?? $product->regular_price,
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

    public function with(): array
    {
        return [
            'products' => Product::with('images')
                // កំណត់ឱ្យបង្ហាញតែផលិតផលដែល Status ជា published
                ->where('status', 'published')
                ->when($this->selectedCategory, function ($query) {
                    $query->where('category_id', $this->selectedCategory);
                })
                ->take(12)
                ->get(),
        ];
    }
}; ?>

<div>
    <!-- 🛒 Header Section -->
    <section id="product-header" class="mt-4 mt-md-5 mb-4">
        <div class="row">
            <div class="col-12 text-center mb-3 mb-md-4">
                <h5 class="text-muted text-uppercase tracking-wider small mb-2"
                    style="font-size: 0.75rem; letter-spacing: 1px;">Discover Your Required Product</h5>
                <h2 class="fw-bold text-dark header-main-title">From 267+ Different Vendors, 30+ Categories</h2>
            </div>

            <div class="col-12 mb-2 mb-md-4">
                <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 pb-2">
                    <button wire:click="filterByCategory(null)"
                        class="btn px-3 py-2 rounded-3 d-flex align-items-center gap-2 fw-semibold shadow-sm transition-all
                        {{ $selectedCategory === null ? 'btn-danger text-white' : 'btn-light text-danger border-0' }}"
                        style="{{ $selectedCategory === null ? 'background: linear-gradient(135deg, #dc3545, #bd2130);' : 'background-color: #ffeef0;' }}">
                        <i class="fas fa-fire-alt"></i>
                        <span>Hot in Sale</span>
                    </button>

                    @foreach ($categories as $category)
                        <button wire:click="filterByCategory({{ $category->id }})"
                            class="btn px-3 py-2 rounded-3 fw-medium transition-all shadow-sm
                            {{ $selectedCategory === $category->id ? 'btn-primary text-white' : 'btn-light text-primary border-0' }}"
                            style="{{ $selectedCategory === $category->id ? 'background: linear-gradient(135deg, #0d6efd, #0a58ca);' : 'background-color: #e0e7ff; color: #4338ca;' }}">
                            {{ $category->category_name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- 🛍️ Products Grid Section -->
    <div class="mt-4 mt-md-5">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 small py-2" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 g-sm-4">
            @forelse($products as $product)
                <div class="col d-flex align-items-stretch">
                    <div
                        class="card w-100 border-0 custom-product-card position-relative overflow-hidden d-flex flex-column">

                        <!-- Product Image Container -->
                        <div class="position-relative product-img-container">
                            <a href="{{ route('product.details', ['productId' => $product->id]) }}"
                                class="d-block w-100 h-100">
                                <img src="{{ $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : 'no image data' }}"
                                    class="card-img-top product-main-img" alt="{{ $product->product_name }}">
                            </a>

                            <!-- Wishlist Button -->
                            <button wire:click="toggleWishlist({{ $product->id }})"
                                class="btn p-0 border-0 shadow-none position-absolute top-0 end-0 mt-3 me-3"
                                style="z-index: 10;">
                                @if (isset(session()->get('wishlist', [])[$product->id]))
                                    <i
                                        class="fa-solid fa-heart fs-4 text-danger animate__animated animate__bounceIn"></i>
                                @else
                                    <i class="fa-regular fa-heart fs-4 text-secondary"></i>
                                @endif
                            </button>

                            <!-- កែសម្រួលត្រង់ផ្នែក Badge -->
                            @if ($product->status === 'published')
                                <span
                                    class="badge position-absolute top-0 start-0 m-2 m-md-3 px-2 py-1 vendor-premium-badge">
                                    <i class="fa-solid fa-circle-check me-1 text-primary"></i> Verified
                                </span>
                            @endif
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-2.5 p-md-3.5 d-flex flex-column justify-content-between flex-grow-1">
                            <div>
                                <h5 class="product-title mb-1" title="{{ $product->product_name }}">
                                    <a href="{{ route('product.details', ['productId' => $product->id]) }}"
                                        class="text-dark text-decoration-none">
                                        {{ $product->product_name }}
                                    </a>
                                </h5>
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
                                            <span class="current-price">
                                                ${{ number_format($product->discounted_price ?? $product->regular_price, 2) }}
                                            </span>
                                            @if (isset($product->discounted_price) && $product->discounted_price < $product->regular_price)
                                                <span class="old-price">
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

                                <!-- ✨ ផ្នែកប៊ូតុង និងប្រអប់លេខថ្មី ដែលមានលក្ខណៈ Responsive ឥតខ្ចោះ -->
                                <div x-data="{ quantity: 1 }" class="mt-3 px-1 pb-2">
                                    <div class="responsive-cart-container">

                                        <!-- Stepper សម្រាប់គ្រប់គ្រងចំនួនលំអិត -->
                                        {{-- <div class="custom-qty-stepper">
                                            <button type="button" @click="if(quantity > 1) quantity--"
                                                class="qty-control-btn">-</button>
                                            <input type="number" x-model="quantity" min="1"
                                                max="{{ $product->stock_quantity }}" class="qty-modern-input"
                                                aria-label="Quantity">
                                            <button type="button" @click="quantity++"
                                                class="qty-control-btn">+</button>
                                        </div> --}}

                                        <!-- ប៊ូតុង Add to Cart Premium -->
                                        <button type="button"
                                            wire:click="addToCartFromAnywhere({{ $product->id }}, quantity)"
                                            class="btn-premium-inline-cart">
                                            <i class="fa-solid fa-basket-shopping"></i>
                                            <span>Add to Cart</span>
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
</div>

<style>
    /* 📱 Responsive Flex Layout សម្រាប់ប្រអប់ទិញទំនិញ */
    .responsive-cart-container {
        display: flex;
        gap: 8px;
        align-items: center;
        width: 100%;
    }

    /* 🔢 Modern Quantity Stepper */
    .custom-qty-stepper {
        display: flex;
        align-items: center;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        background: #f8fafc;
        height: 40px;
        box-sizing: border-box;
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
        width: 34px !important;
        border: none !important;
        background: transparent !important;
        text-align: center;
        font-weight: 700;
        font-size: 0.95rem;
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

    /* 🛍️ Premium Add to Cart Button */
    .btn-premium-inline-cart {
        flex-grow: 1;
        height: 40px;
        background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.85rem;
        border: none !important;
        border-radius: 12px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.25s ease-in-out !important;
        white-space: nowrap;
    }

    .btn-premium-inline-cart:hover {
        background: linear-gradient(135deg, #4f46e5, #3730a3) !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        transform: translateY(-1px);
    }

    .btn-premium-inline-cart:active {
        transform: translateY(1px);
    }

    /* 🎨 Global Card Styling */
    .transition-all {
        transition: all 0.2s ease-in-out;
    }

    .btn-light.text-primary:hover {
        background-color: #c7d2fe !important;
        transform: translateY(-1px);
    }

    .custom-product-card {
        background: #ffffff;
        border-radius: 20px !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .custom-product-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 30px rgba(99, 102, 241, 0.12);
    }

    .product-img-container {
        height: 240px;
        background-color: #f8fafc;
        border-radius: 20px 20px 0 0;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
    }

    .product-main-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: transform 0.5s ease;
    }

    .custom-product-card:hover .product-main-img {
        transform: scale(1.06);
    }

    .vendor-premium-badge {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(4px);
        color: #1e293b;
        font-weight: 600;
        font-size: 0.75rem;
        border-radius: 8px !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .product-title {
        font-size: 0.975rem;
        font-weight: 700;
        line-height: 1.4;
        height: 2.8rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .custom-product-card:hover .product-title a {
        color: #4f46e5 !important;
    }

    .product-short-desc {
        font-size: 0.825rem;
        color: #64748b;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.3rem;
        line-height: 1.4;
    }

    .price-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #94a3b8;
        letter-spacing: 0.5px;
        display: block;
    }

    .current-price {
        font-size: 1.2rem;
        font-weight: 800;
        color: #ef4444;
    }

    .old-price {
        font-size: 0.825rem;
        color: #94a3b8;
        text-decoration: line-through;
    }

    .stock-status-badge {
        font-size: 0.75rem;
        color: #10b981;
        font-weight: 600;
        background: #ecfdf5;
        padding: 4px 10px;
        border-radius: 6px;
    }

    /* ==========================================================================
       📱 RESPONSIVE DESIGN FOR TABLET & MOBILE SCREENS
       ========================================================================== */

    /* 📑 1. សម្រាប់ឧបករណ៍ Tablet និងអេក្រង់ទំហំមធ្យម (Medium Screens: ល្អិតជាង 991.98px) */
    @media (max-width: 991.98px) {
        .product-img-container {
            height: 190px !important;
            padding: 10px;
        }

        .product-title {
            font-size: 0.9rem;
            height: 2.6rem;
        }

        .product-short-desc {
            font-size: 0.8rem;
            height: 2.2rem;
            margin-bottom: 0.5rem !important;
        }

        .current-price {
            font-size: 1.05rem;
        }

        .stock-status-badge {
            font-size: 0.7rem;
            padding: 2px 8px;
        }

        .custom-qty-stepper,
        .btn-premium-inline-cart {
            height: 42px;
        }

        .qty-control-btn {
            width: 32px;
            font-size: 1rem;
        }

        .qty-modern-input {
            width: 36px !important;
        }
    }

    /* 📱 2. សម្រាប់ឧបករណ៍ទូរស័ព្ទដៃផ្ទាល់ (Small Mobile Screens: ល្អិតជាង 575.98px) */
    @media (max-width: 575.98px) {
        .responsive-cart-container {
            flex-direction: column;
            gap: 8px;
        }

        .custom-qty-stepper {
            width: 100% !important;
            justify-content: space-between;
            height: 42px;
        }

        .qty-control-btn {
            width: 46px;
            font-size: 1.1rem;
        }

        .qty-modern-input {
            font-size: 1rem;
            width: 40px !important;
        }

        .btn-premium-inline-cart {
            width: 100% !important;
            height: 42px;
            font-size: 0.85rem;
        }

        .product-img-container {
            height: 160px !important;
        }
    }
</style>

<!-- 🔔 SweetAlert2 JavaScript -->
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
