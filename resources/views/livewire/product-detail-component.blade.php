<?php

use App\Models\Product;
use Livewire\Volt\Component;

new class extends Component {
    public $product;
    public $quantity = 1;
    public $isInWishlist = false;
    public $selectedAttributes = [];

    public function mount(int $productId): void
    {
        // 💡 កែសម្រួលចំណុច reviews.user ទៅជា productReviews.user ឱ្យត្រូវតាមឈ្មោះ Relationship
        $this->product = Product::with([
            'images',
            'vendor.stores',
            'category',
            'attributes.attribute',
            'attributes.attributeValue',
            'productReviews.user', // <-- 💡 កែប្រែត្រង់នេះ
        ])->findOrFail($productId);

        if ($this->product->attributes) {
            $grouped = $this->product->attributes->groupBy('attribute_id');
            // ផ្លាស់ប្តូរពីជួរទី ២៨ ដើម មកជាកូដនេះ៖
            foreach ($grouped as $attrId => $group) {
                $firstItem = $group->first();
                $attrName = $firstItem->attribute->name ?? 'Attribute';

                // ពិនិត្យថាតើ attributeValue មានទិន្នន័យឬអត់ ទើបចូលទៅយក value
                if ($firstItem->attributeValue) {
                    $this->selectedAttributes[$attrName] = $firstItem->attributeValue->value;
                } else {
                    $this->selectedAttributes[$attrName] = 'N/A'; // ឬតម្លៃណាមួយដែលអ្នកចង់ដាក់
                }
            }
        }

        $wishlist = session()->get('wishlist', []);
        $this->isInWishlist = isset($wishlist[$productId]);
    }

    public function selectAttributeValue($attributeName, $valueName): void
    {
        $this->selectedAttributes[$attributeName] = $valueName;
    }

    public function increaseQty(): void
    {
        $maxStock = $this->product->stock_quantity ?? 0;
        if ($this->quantity < $maxStock) {
            $this->quantity++;
        }
    }

    public function decreaseQty(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(): void
    {
        $productId = $this->product->id;
        $maxStock = $this->product->stock_quantity ?? 0;

        if ($maxStock <= 0 || $this->quantity > $maxStock) {
            return;
        }

        $this->dispatch('addToCartFromAnywhere', [
            'productId' => $productId,
            'quantity' => intval($this->quantity),
            'attributes' => $this->selectedAttributes,
        ]);

        $this->dispatch('notify', [
            'title' => 'Successfully added to cart!',
            'message' => $this->product->product_name . ' has been added to your cart.',
            'type' => 'success',
        ]);

        session()->flash('message', 'Product added to cart successfully!');
    }

    public function addToWishlist(): void
    {
        $productId = $this->product->id;
        $this->dispatch('addToWishlistFromAnywhere', [
            'productId' => $productId,
        ]);

        $this->isInWishlist = !$this->isInWishlist;

        $this->dispatch('notify', [
            'title' => $this->isInWishlist ? 'Added to Wishlist!' : 'Removed from Wishlist!',
            'message' => $this->isInWishlist ? $this->product->product_name . ' has been added.' : $this->product->product_name . ' has been removed.',
            'type' => 'success',
        ]);
    }
}; ?>

<div class="container my-2 product-details-wrapper">
    <!-- Google Fonts Links -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Kantumruuy+Pro:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        /* Global & Typography Consistency */
        .product-details-wrapper {
            font-family: 'Plus Jakarta Sans', 'Kantumruuy Pro', sans-serif;
            color: #334155;
        }

        .product-title,
        .price-text,
        .fw-bold,
        h1,
        h4,
        h5 {
            font-family: 'Outfit', 'Kantumruuy Pro', sans-serif;
        }

        /* Image Section */
        .main-image-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            position: relative;
            overflow: hidden;
        }

        .main-image-container {
            height: 420px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            border-radius: 12px;
            overflow: hidden;
        }

        .main-image-container img {
            max-height: 90%;
            object-fit: contain;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-image-container img:hover {
            transform: scale(1.05);
        }

        .thumb-img-box {
            width: 70px;
            height: 70px;
            padding: 4px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            background-color: #fff;
            transition: all 0.2s ease;
        }

        .thumb-img-box.active {
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        /* Buttons & Actions Consistency */
        .floating-wishlist-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .floating-wishlist-btn:hover {
            transform: scale(1.05);
            border-color: #cbd5e1;
        }

        .product-title {
            color: #0f172a;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .price-text {
            font-weight: 800;
            color: #4f46e5;
        }

        .stepper-group {
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            border-radius: 12px;
            width: 130px;
            height: 48px;
        }

        .btn-stepper-action {
            border: none;
            background: transparent;
            color: #64748b;
            width: 40px;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-stepper-action:hover:not(:disabled) {
            background-color: #e2e8f0;
            color: #0f172a;
        }

        .input-stepper-qty {
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            font-weight: 700;
            color: #0f172a;
        }

        .btn-premium-cart {
            background: linear-gradient(135deg, #4f46e5, #3730a3) !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 12px !important;
            height: 48px;
            font-weight: 600;
            transition: all 0.2s ease !important;
        }

        .btn-premium-cart:hover:not(:disabled) {
            background: linear-gradient(135deg, #4338ca, #2e2685) !important;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }

        /* Form Controls Consistency */
        .form-select-custom,
        .form-control-custom {
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 10px 14px;
            background-color: #ffffff;
            color: #334155;
            transition: all 0.2s ease;
        }

        .form-select-custom:focus,
        .form-control-custom:focus {
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
            outline: none;
        }

        /* Attributes */
        .attribute-badge {
            padding: 8px 18px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .attribute-badge:hover {
            border-color: #94a3b8;
            background: #f8fafc;
        }

        .attribute-badge.active {
            border-color: #4f46e5;
            background: #eeebff;
            color: #4f46e5;
            box-shadow: 0 0 0 1px #4f46e5;
        }

        /* Vendor Profile Box */
        .vendor-profile-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
        }

        .vendor-avatar-circle {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #ffffff;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        @media (max-width: 575.98px) {
            .main-image-container {
                height: 300px;
            }
        }

        .btn-back {
            background-color: #f1f5f9;
            color: #475569;
            border-radius: 10px;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-back:hover {
            background-color: #e2e8f0;
            color: #0f172a;
        }
    </style>

    {{-- Back Button --}}
    <div class="mb-4">
        <button type="button" onclick="history.back()" class="btn px-3 py-2 fw-semibold border-0 shadow-sm"
            style="background-color: #f1f5f9; color: #475569; border-radius: 10px; transition: all 0.2s ease;"
            onmouseover="this.style.backgroundColor='#e2e8f0'; this.style.color='#0f172a';"
            onmouseout="this.style.backgroundColor='#f1f5f9'; this.style.color='#475569';">
            <i class="fa-solid fa-arrow-left-long me-2"></i> Back
        </button>
    </div>

    <div class="row g-4 lg:g-5">
        {{-- 📸 Left: Product Image Gallery --}}
        <div class="col-md-6">
            <div class="card main-image-card p-3 shadow-sm">
                <!-- Floating Wishlist Button -->
                <button type="button" wire:click.stop="addToWishlist" wire:loading.attr="disabled"
                    class="btn p-0 floating-wishlist-btn">
                    <span wire:loading wire:target="addToWishlist"
                        class="spinner-border spinner-border-sm text-primary"></span>
                    <span wire:loading.remove wire:target="addToWishlist">
                        @if ($isInWishlist)
                            <i class="fa-solid fa-heart fs-5 text-danger animate__animated animate__bounceIn"></i>
                        @else
                            <i class="fa-regular fa-heart fs-5 text-secondary"></i>
                        @endif
                    </span>
                </button>

                <!-- Main Image Display -->
                <div class="main-image-container p-2 mb-3">
                    @if ($product->images && $product->images->count() > 0)
                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" id="mainProductImg"
                            class="img-fluid" alt="{{ $product->product_name }}">
                    @else
                        <img src="{{ asset('home_asset/img/product-sample.png') }}" id="mainProductImg"
                            class="img-fluid" alt="No Image">
                    @endif
                </div>

                <!-- Thumbnail Carousel/List -->
                <div class="d-flex gap-2 justify-content-center overflow-auto py-1">
                    @if ($product->images)
                        @foreach ($product->images as $index => $image)
                            <div wire:key="thumb-{{ $image->id }}"
                                class="thumb-img-box {{ $index === 0 ? 'active' : '' }}" onclick="changeImage(this)">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                    class="w-100 h-100 object-fit-contain rounded-2">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- 📝 Right: Product Info Sidebar --}}
        <div class="col-md-6 text-start d-flex flex-column justify-content-between">
            <div class="product-info-sidebar px-md-2">

                <!-- Category Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-2">
                    <span class="text-uppercase tracking-wider fw-bold text-primary" style="font-size: 0.75rem;">
                        {{ $product->category->category_name ?? 'Category' }}
                    </span>
                </nav>

                <!-- Product Title -->
                <h1 class="product-title text-slate-900 mb-2" style="font-size: 1.85rem; line-height: 1.3;">
                    {{ $product->product_name }}
                </h1>

                <!-- SKU and Stock Badges -->
                <div class="d-flex align-items-center gap-3 mb-4" style="font-size: 0.875rem;">
                    <span class="text-muted">SKU: <strong class="text-dark">{{ $product->sku ?? 'N/A' }}</strong></span>
                    <span style="width: 1px; height: 14px; background-color: #cbd5e1;"></span>
                    @if (($product->stock_quantity ?? 0) > 0)
                        <span
                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">In
                            Stock</span>
                    @else
                        <span
                            class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-bold">Out
                            of Stock</span>
                    @endif
                </div>

                <!-- Pricing Display -->
                <div class="mb-4 bg-light p-3 rounded-3 d-flex align-items-baseline gap-3">
                    <span
                        class="h2 mb-0 price-text">${{ number_format($product->discounted_price > 0 ? $product->discounted_price : $product->regular_price, 2) }}</span>
                    @if ($product->discounted_price > 0 && $product->discounted_price < $product->regular_price)
                        <span
                            class="text-muted text-decoration-line-through fs-5 fw-medium">${{ number_format($product->regular_price, 2) }}</span>
                        <span
                            class="badge bg-danger rounded-2 px-2.5 py-1 text-uppercase tracking-wider shadow-sm fw-bold"
                            style="font-size: 0.72rem;">Sale</span>
                    @endif
                </div>

                <!-- Product Description -->
                <p class="text-secondary mb-4 lh-lg" style="font-size: 0.925rem;">
                    {{ $product->description ?? 'No description available for this product.' }}
                </p>

                <hr class="text-muted opacity-25 my-4">

                {{-- 🎯 Attributes Selection Section --}}
                @if ($product->attributes && $product->attributes->count() > 0)
                    <div class="product-attributes-section mb-4">
                        @foreach ($product->attributes->groupBy('attribute_id') as $attrId => $group)
                            @php $attributeName = $group->first()->attribute->name ?? 'Attribute'; @endphp
                            <div class="mb-3" wire:key="attr-{{ $attrId }}">
                                <span class="d-block text-dark small fw-bold text-uppercase mb-2"
                                    style="letter-spacing: 0.5px; font-size: 0.75rem;">
                                    Select {{ $attributeName }}:
                                </span>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($group as $item)
                                        @php $valueName = $item->attributeValue->value ?? 'N/A'; @endphp
                                        <button type="button"
                                            wire:click="selectAttributeValue('{{ $attributeName }}', '{{ $valueName }}')"
                                            class="btn attribute-badge {{ ($selectedAttributes[$attributeName] ?? '') === $valueName ? 'active' : '' }}">
                                            {{ $valueName }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <hr class="text-muted opacity-25 my-4">
                @endif

                {{-- Interactive Actions Button Section --}}
                <div class="d-flex gap-3 align-items-center mb-4 pt-2">
                    <!-- Stepper Group -->
                    <div class="d-flex align-items-center justify-content-between stepper-group p-1 shadow-sm">
                        <button class="btn-stepper-action rounded-3" type="button" wire:click="decreaseQty"
                            {{ ($product->stock_quantity ?? 0) <= 0 ? 'disabled' : '' }}>
                            <i class="fa-solid fa-minus fs-6"></i>
                        </button>
                        <input type="text" class="form-control text-center input-stepper-qty fs-5 p-0"
                            wire:model="quantity" readonly>
                        <button class="btn-stepper-action rounded-3" type="button" wire:click="increaseQty"
                            {{ ($product->stock_quantity ?? 0) <= 0 || $quantity >= ($product->stock_quantity ?? 0) ? 'disabled' : '' }}>
                            <i class="fa-solid fa-plus fs-6"></i>
                        </button>
                    </div>

                    <!-- Add to Cart Button -->
                    <button type="button"
                        class="btn d-flex align-items-center justify-content-center gap-2 flex-grow-1 btn-premium-cart shadow-sm {{ ($product->stock_quantity ?? 0) <= 0 ? 'btn-secondary disabled' : '' }}"
                        wire:click.prevent="addToCart" wire:loading.attr="disabled"
                        {{ ($product->stock_quantity ?? 0) <= 0 ? 'disabled' : '' }}>
                        <span wire:loading wire:target="addToCart" class="spinner-border spinner-border-sm text-white"
                            role="status" aria-hidden="true"></span>
                        <span wire:loading.remove wire:target="addToCart">
                            @if (($product->stock_quantity ?? 0) <= 0)
                                <i class="fa-solid fa-ban fs-5"></i>
                            @else
                                <i class="fa-solid fa-basket-shopping fs-5"></i>
                            @endif
                        </span>
                        <span wire:loading wire:target="addToCart">Adding...</span>
                        <span wire:loading.remove
                            wire:target="addToCart">{{ ($product->stock_quantity ?? 0) <= 0 ? 'Out of Stock' : 'Add to Cart' }}</span>
                    </button>
                </div>

                {{-- Write Review Form Block --}}
                <div class="card p-4 shadow-sm mt-4 border-0" style="background-color: #f8fafc; border-radius: 16px;">
                    <h5 class="fw-bold mb-3" style="color: #0f172a;">Write Your Review</h5>

                    @auth
                        @if (session('success'))
                            <div class="alert alert-success border-0 rounded-3 small mb-3"
                                style="background-color: #ecfdf5; color: #065f46;">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('reviews.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <!-- Rating Stars Dropdown -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary text-uppercase mb-1"
                                    style="letter-spacing: 0.5px; font-size: 0.75rem;">Rating Stars:</label>
                                <select name="rating" class="form-select form-select-custom" style="max-width: 180px;"
                                    required>
                                    <option value="5">&#9733;&#9733;&#9733;&#9733;&#9733; (5 Stars)</option>
                                    <option value="4">&#9733;&#9733;&#9733;&#9733; (4 Stars)</option>
                                    <option value="3">&#9733;&#9733;&#9733; (3 Stars)</option>
                                    <option value="2">&#9733;&#9733; (2 Stars)</option>
                                    <option value="1">&#9733; (1 Star)</option>
                                </select>
                            </div>

                            <!-- Review Textarea -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary text-uppercase mb-1"
                                    style="letter-spacing: 0.5px; font-size: 0.75rem;">Your Comment:</label>
                                {{-- 💡 កែប្រែឈ្មោះ attribute ពី name="comment" ទៅជា name="review" --}}
                                <textarea name="review" class="form-control form-control-custom" rows="3"
                                    placeholder="Write your opinion about this product..." required></textarea>
                            </div>

                            <button type="submit"
                                class="btn text-white px-4 fw-semibold border-0 shadow-sm transition-all"
                                style="background: linear-gradient(135deg, #4f46e5, #3730a3); border-radius: 10px; height: 42px;">
                                Submit Review
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning m-0 border-0 rounded-3 small"
                            style="background-color: #fffbeb; color: #92400e;">
                            Please <a href="{{ route('login') }}" class="fw-bold text-decoration-none"
                                style="color: #4f46e5;">Sign In</a> first to write a review.
                        </div>
                    @endauth
                </div>

                {{-- 💬 ផ្នែកបង្ហាញបញ្ជី Review (Customer Reviews Display Section) --}}
                <div class="card p-4 shadow-sm mt-4 border-0" style="background-color: #ffffff; border-radius: 16px;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold m-0" style="color: #0f172a;">
                            {{-- 💡 កែប្រែការរាប់ចំនួនឱ្យទៅរក productReviews --}}
                            Customer Reviews ({{ $product->productReviews->count() }})
                        </h5>

                        {{-- បង្ហាញពិន្ទុផ្កាយមធ្យមភាគ (Average Rating) --}}
                        @if ($product->productReviews->count() > 0)
                            <div class="d-flex align-items-center gap-1">
                                <span
                                    class="fw-bold text-dark">{{ number_format($product->productReviews->avg('rating'), 1) }}</span>
                                <span class="text-warning">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= round($product->productReviews->avg('rating')))
                                            &#9733;
                                        @else
                                            &#9734;
                                        @endif
                                    @endfor
                                </span>
                            </div>
                        @endif
                    </div>

                    <hr class="text-muted opacity-25 my-2">

                    {{-- បញ្ជីនៃមតិយោបល់ --}}
                    @if ($product->productReviews && $product->productReviews->count() > 0)
                        <div class="review-list-container"
                            style="max-height: 400px; overflow-y: auto; padding-right: 5px;">
                            {{-- 💡 ប្តូរពី reviews ទៅជា productReviews --}}
                            @foreach ($product->productReviews->sortByDesc('created_at') as $reviewItem)
                                <div class="py-3 border-bottom border-light-subtle">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div>
                                            <!-- ឈ្មោះអ្នក Review -->
                                            <h6 class="fw-bold mb-0 text-slate-800" style="font-size: 0.95rem;">
                                                {{ $reviewItem->user->name ?? 'Anonymous User' }}
                                            </h6>
                                            <!-- កាលបរិច្ឆេទ -->
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                {{ $reviewItem->created_at->diffForHumans() }}
                                            </small>
                                        </div>

                                        <!-- ចំនួនផ្កាយដែលគេបានផ្តល់ឱ្យ -->
                                        <div class="text-warning" style="font-size: 0.85rem;">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $reviewItem->rating)
                                                    &#9733;
                                                @else
                                                    &#9734;
                                                @endif
                                            @endfor
                                        </div>
                                    </div>

                                    <!-- ខ្លឹមសារមតិយោបល់ -->
                                    <!-- ខ្លឹមសារមតិយោបល់ -->
                                    <p class="text-secondary m-0 mt-1 lh-base" style="font-size: 0.9rem;">
                                        {{ $reviewItem->review ?? 'No comment provided.' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- បង្ហាញសារនេះ បើមិនទាន់មាននរណា Review ឡើយ --}}
                        <div class="text-center py-4 text-muted">
                            <i class="fa-regular fa-comment-dots fs-3 d-block mb-2 opacity-50"></i>
                            <span class="small">No reviews yet. Be the first to review this product!</span>
                        </div>
                    @endif
                </div>

                {{-- Vendor Information Box --}}
                <div class="d-flex align-items-center justify-content-between vendor-profile-box p-3 mt-4 shadow-sm">
                    <div class="d-flex align-items-center gap-3">
                        @if (
                            $product->vendor &&
                                $product->vendor->stores &&
                                $product->vendor->stores->first() &&
                                $product->vendor->stores->first()->logo)
                            <img src="{{ asset('storage/' . $product->vendor->stores->first()->logo) }}"
                                class="rounded-3 border border-light-subtle shadow-sm"
                                style="width: 44px; height: 44px; object-fit: cover;"
                                alt="{{ $product->vendor->stores->first()->store_name }}">
                        @else
                            <div class="vendor-avatar-circle shadow-sm">
                                {{ strtoupper(substr($product->vendor?->stores?->first()?->store_name ?? 'ST', 0, 2)) }}
                            </div>
                        @endif

                        <div>
                            <small class="text-muted d-block fw-bold text-uppercase"
                                style="font-size: 0.65rem; letter-spacing: 0.5px;">Sold by</small>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#vendorModal"
                                class="text-dark fw-bold text-decoration-none hover:text-primary fs-6 transition-all">
                                {{ $product->vendor?->stores?->first()?->store_name ?? 'Unknown Store' }}
                            </a>
                        </div>
                    </div>

                    <button type="button"
                        class="btn btn-sm btn-white border border-light-subtle rounded-3 bg-white px-3 py-1.5 fw-bold text-secondary shadow-sm"
                        style="font-size: 0.8rem; border-radius: 10px !important; transition: all 0.2s;"
                        data-bs-toggle="modal" data-bs-target="#vendorModal">
                        View Profile
                    </button>
                </div>

                {{-- 🔔 Vendor / Store Profile Modal --}}
                <div class="modal fade" id="vendorModal" tabindex="-1" aria-labelledby="vendorModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
                            <div class="modal-header border-0 pb-0">
                                <button type="button" class="btn-close me-1" data-bs-shadow="none"
                                    data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center pt-0 px-4 pb-4">
                                <div class="mb-3 d-flex justify-content-center">
                                    @if (
                                        $product->vendor &&
                                            $product->vendor->stores &&
                                            $product->vendor->stores->first() &&
                                            $product->vendor->stores->first()->logo)
                                        <img src="{{ asset('storage/' . $product->vendor->stores->first()->logo) }}"
                                            class="rounded-circle border p-1 bg-white shadow-sm"
                                            style="width: 90px; height: 90px; object-fit: cover;"
                                            alt="{{ $product->vendor->stores->first()->store_name }}">
                                    @else
                                        <div class="vendor-avatar-circle mx-auto fs-3 shadow-sm"
                                            style="width: 90px; height: 90px; border-radius: 50% !important;">
                                            {{ strtoupper(substr($product->vendor?->stores?->first()?->store_name ?? 'ST', 0, 2)) }}
                                        </div>
                                    @endif
                                </div>

                                <h4 class="fw-bold text-slate-900 mb-1">
                                    {{ $product->vendor?->stores?->first()?->store_name ?? 'Unknown Store' }}
                                </h4>
                                <span
                                    class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 mb-3 small fw-bold">
                                    <i class="fa-solid fa-circle-check me-1"></i> Verified Store
                                </span>

                                @if ($product->vendor?->stores?->first()?->description)
                                    <p class="text-secondary small mb-3 px-2 lh-base">
                                        {{ $product->vendor->stores->first()->description }}
                                    </p>
                                @endif

                                <hr class="text-muted opacity-25 my-3">

                                <div class="text-start bg-light p-3 rounded-3 mb-4"
                                    style="font-size: 0.9rem; border-radius: 12px !important;">
                                    <div class="d-flex justify-content-between align-items-start mb-2.5">
                                        <span class="text-muted"><i
                                                class="fa-regular fa-envelope me-2 text-primary"></i> Email:</span>
                                        <span class="fw-semibold text-dark text-break text-end"
                                            style="max-width: 220px;">
                                            {{ $product->vendor?->stores?->first()?->store_email ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2.5">
                                        <span class="text-muted"><i class="fa-solid fa-phone me-2 text-primary"></i>
                                            Phone:</span>
                                        <span class="fw-semibold text-dark">
                                            {{ $product->vendor?->stores?->first()?->store_phone ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2.5">
                                        <span class="text-muted"><i class="fa-regular fa-clock me-2 text-primary"></i>
                                            Business Hours:</span>
                                        <span class="fw-semibold text-dark">
                                            {{ $product->vendor?->stores?->first()?->opening_hours ?? 'Mon - Sun (8:00 AM - 9:00 PM)' }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-start mb-0">
                                        <span class="text-muted"><i
                                                class="fa-solid fa-location-dot me-2 text-primary"></i>
                                            Location:</span>
                                        <span class="fw-semibold text-dark text-end small"
                                            style="max-width: 220px; line-height: 1.4;">
                                            {{ $product->vendor?->stores?->first()?->address ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="{{ route('home.store.details', $product->vendor->stores->first()->slug ?? '') }}"
                                        class="btn btn-primary py-2.5 fw-semibold border-0 shadow-sm text-white"
                                        style="background: linear-gradient(135deg, #4f46e5, #3730a3); border-radius: 12px;">
                                        <i class="fa-solid fa-basket-shopping me-2"></i> Visit Shop Website
                                    </a>
                                    <button type="button"
                                        class="btn btn-link text-secondary text-decoration-none fw-medium btn-sm mt-1"
                                        data-bs-dismiss="modal">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function changeImage(element) {
        document.querySelectorAll('.thumb-img-box').forEach(box => box.classList.remove('active'));
        element.classList.add('active');
        const mainImg = document.getElementById('mainProductImg');
        const newSrc = element.querySelector('img').src;
        mainImg.style.opacity = '0.3';
        setTimeout(() => {
            mainImg.src = newSrc;
            mainImg.style.opacity = '1';
        }, 120);
    }

    document.addEventListener('livewire:init', () => {
        Livewire.on('notify', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            Swal.fire({
                title: data.title || 'Success!',
                text: data.message || '',
                icon: data.type || 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                background: '#ffffff',
                color: '#1e293b',
                iconColor: data.type === 'success' ? '#4f46e5' : '#ef4444'
            });
        });
    });
</script>
