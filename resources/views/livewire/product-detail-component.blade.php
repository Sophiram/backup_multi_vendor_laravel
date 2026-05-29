<?php

use App\Models\Product;
use Livewire\Volt\Component;

new class extends Component {
    public $product;
    public $quantity = 1;
    public $isInWishlist = false;

    // 🎯 បន្ថែម Property សម្រាប់ចាប់យក Attribute ដែលអតិថិជនជ្រើសរើស
    public $selectedAttributes = [];

    public function mount(int $productId): void
    {
        // ទាញយកទិន្នន័យ (ត្រូវប្រាកដថាបាន Eager Load គ្រប់ Relation)
        $this->product = Product::with(['images', 'vendor', 'category', 'attributes.attribute', 'attributes.attributeValue'])->findOrFail($productId);

        // កំណត់តម្លៃ Default
        if ($this->product->attributes) {
            $grouped = $this->product->attributes->groupBy('attribute_id');
            foreach ($grouped as $attrId => $group) {
                // យកឈ្មោះ Attribute តាមរយៈ Relation
                $attrName = $group->first()->attribute->name;
                // យកតម្លៃដំបូងដាក់ជា Default
                $this->selectedAttributes[$attrName] = $group->first()->attributeValue->value;
            }
        }

        $wishlist = session()->get('wishlist', []);
        $this->isInWishlist = isset($wishlist[$productId]);
    }

    // 🎯 មុខងារផ្លាស់ប្តូរតម្លៃ Attribute ពេលអតិថិជនចុចជ្រើសរើស (ឧទាហរណ៍៖ ដូរពណ៌ ឬទំហំ)
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

        // 🔗 បញ្ជូនទិន្នន័យproductId, បរិមាណ ព្រមទាំង Attributes ដែលបានជ្រើសរើសទៅកាន់កន្ត្រកទំនិញ
        $this->dispatch('addToCartFromAnywhere', [
            'productId' => $productId,
            'quantity' => intval($this->quantity),
            'attributes' => $this->selectedAttributes, // ➕ បន្ថែមការបញ្ជូន Attributes ទៅ Cart
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

<div class="container my-5 product-details-wrapper">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap"
        rel="stylesheet">

    <style>
        .product-details-wrapper {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .main-image-card {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 24px;
            position: relative;
        }

        .main-image-container {
            height: 440px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .main-image-container img {
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .main-image-container img:hover {
            transform: scale(1.03);
        }

        .thumb-img-box {
            width: 76px;
            height: 76px;
            padding: 6px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            cursor: pointer;
            background-color: #fff;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .thumb-img-box:hover {
            border-color: #cbd5e1;
        }

        .thumb-img-box.active {
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        .floating-wishlist-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            transition: all 0.2s ease;
        }

        .product-title {
            font-family: 'Outfit', sans-serif;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .price-text {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
        }

        .stepper-group {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 12px;
            overflow: hidden;
            width: 130px;
            height: 48px;
        }

        .btn-stepper-action {
            border: none;
            background: transparent;
            color: #475569;
            width: 40px;
            transition: all 0.15s;
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
            color: #1e293b;
        }

        .btn-premium-cart {
            background: linear-gradient(135deg, #4f46e5, #3730a3) !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 12px !important;
            height: 48px;
            font-weight: 700;
            letter-spacing: 0.3px;
            transition: all 0.2s ease !important;
        }

        .btn-premium-cart:hover:not(:disabled) {
            background: linear-gradient(135deg, #4338ca, #2e2685) !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }

        .vendor-profile-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
        }

        .vendor-avatar-circle {
            width: 46px;
            height: 46px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #ffffff;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15);
        }

        /* 🎨 បន្ថែមស្ទីលសម្រាប់ប៊ូតុងជ្រើសរើស Attribute */
        .attribute-badge {
            padding: 8px 16px;
            border: 2px solid #e2e8f0;
            background: #ffffff;
            color: #334155;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .attribute-badge:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .attribute-badge.active {
            border-color: #4f46e5;
            background: #f5f3ff;
            color: #4f46e5;
        }

        @media (max-width: 575.98px) {
            .main-image-container {
                height: 320px;
            }
        }
    </style>

    {{-- Back Button --}}
    <div class="mb-4">
        <a href="{{ url()->previous() }}" class="btn rounded-3 px-3 py-2 fw-medium border-0"
            style="background-color: #eef2ff; color: #4f46e5; transition: all 0.3s ease;"
            onmouseover="this.style.backgroundColor='#4f46e5'; this.style.color='#ffffff';"
            onmouseout="this.style.backgroundColor='#eef2ff'; this.style.color='#4f46e5';">
            <i class="fa-solid fa-chevron-left me-1"></i> Back
        </a>
    </div>

    <div class="row g-4 lg:g-5">
        {{-- 📸 Left: Product Image Gallery --}}
        <div class="col-md-6">
            <div class="card border-0 main-image-card p-3 shadow-sm">
                <button type="button" wire:click.stop="addToWishlist" wire:loading.attr="disabled"
                    class="btn p-0 floating-wishlist-btn shadow-sm">
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

                <div class="main-image-container p-2 mb-2 rounded-4">
                    @if ($product->images && $product->images->count() > 0)
                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" id="mainProductImg"
                            class="img-fluid" alt="{{ $product->product_name }}">
                    @else
                        <img src="{{ asset('home_asset/img/product-sample.png') }}" id="mainProductImg"
                            class="img-fluid" alt="No Image">
                    @endif
                </div>

                <div class="d-flex gap-2 justify-content-center overflow-auto py-2 px-1">
                    @if ($product->images)
                        @foreach ($product->images as $index => $image)
                            <div wire:key="thumb-{{ $image->id }}"
                                class="thumb-img-box {{ $index === 0 ? 'active' : '' }}" onclick="changeImage(this)">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                    class="w-100 h-100 object-fit-contain rounded">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- 📝 Right: Product Info Sidebar --}}
        <div class="col-md-6 text-start d-flex flex-column justify-content-between">
            <div class="product-info-sidebar p-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2 text-uppercase font-monospace tracking-wider"
                        style="font-size: 0.75rem;">
                        <li class="breadcrumb-item"><a href="#"
                                class="text-decoration-none text-muted fw-bold">{{ $product->category->category_name ?? 'Category' }}</a>
                        </li>
                        <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Details</li>
                    </ol>
                </nav>

                <h1 class="fw-extrabold mb-2 product-title text-slate-900"
                    style="font-size: 1.85rem; line-height: 1.25;">
                    {{ $product->product_name }}
                </h1>

                <div class="d-flex align-items-center gap-3 mb-3" style="font-size: 0.9rem;">
                    <span class="text-muted fw-medium">SKU: <strong
                            class="text-dark">{{ $product->sku ?? 'N/A' }}</strong></span>
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

                <hr class="text-muted opacity-25 my-3">

                <div class="my-3 py-1">
                    <div class="d-flex align-items-baseline gap-3">
                        <span
                            class="h1 mb-0 text-danger price-text">${{ number_format($product->discounted_price ?? $product->regular_price, 2) }}</span>
                        @if (isset($product->discounted_price) && $product->discounted_price < $product->regular_price)
                            <span
                                class="text-muted text-decoration-line-through fs-5 fw-medium">${{ number_format($product->regular_price, 2) }}</span>
                            <span
                                class="badge bg-danger rounded-2 px-2.5 py-1 text-uppercase tracking-wider shadow-sm fw-bold"
                                style="font-size: 0.75rem;">Sale</span>
                        @endif
                    </div>
                </div>

                {{-- <div style="background: #eee; padding: 20px;">
                    <strong>Debug info:</strong>
                    <p>Product ID: {{ $product->id }}</p>
                    <p>Attribute count: {{ $product->attributes->count() }}</p>

                    @foreach ($product->attributes as $attr)
                        <p>Attribute ID: {{ $attr->attribute_id }} |
                            Attribute Name: {{ $attr->attribute ? $attr->attribute->name : 'Missing Name' }} |
                            Value: {{ $attr->attributeValue ? $attr->attributeValue->value : 'Missing Value' }}
                        </p>
                    @endforeach
                </div> --}}

                <p class="text-secondary mb-4 lh-lg" style="font-size: 0.95rem;">
                    {{ $product->description ?? 'No description available for this product.' }}
                </p>

                <hr class="text-muted opacity-25 my-3">

                {{-- 🎯 ផ្នែកថ្មី៖ បង្ហាញការជ្រើសរើស Product Attributes (Dynamic) --}}
                @if ($product->attributes && $product->attributes->count() > 0)
                    <div class="product-attributes-section my-4">
                        @foreach ($product->attributes->groupBy('attribute_id') as $attrId => $group)
                            @php
                                $attributeName = $group->first()->attribute->name ?? 'Attribute';
                            @endphp
                            <div class="mb-3" wire:key="attr-{{ $attrId }}">
                                <span class="d-block text-secondary small fw-bold text-uppercase mb-2"
                                    style="letter-spacing: 0.5px;">
                                    Select {{ $attributeName }}:
                                </span>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($group as $item)
                                        @php
                                            $valueName = $item->attributeValue->value ?? 'N/A';
                                        @endphp
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
                    <hr class="text-muted opacity-25 my-3">
                @endif

                {{-- Interactive Actions Button Section --}}
                <div class="d-flex flex-column flex-sm-row gap-3 align-items-stretch align-items-sm-center mb-4 pt-2">
                    <div class="d-flex align-items-center justify-content-between stepper-group p-1">
                        <button class="btn-stepper-action rounded-3 d-flex align-items-center justify-content-center"
                            type="button" wire:click="decreaseQty"
                            {{ ($product->stock_quantity ?? 0) <= 0 ? 'disabled' : '' }}>
                            <i class="fa-solid fa-minus fs-6"></i>
                        </button>
                        <input type="text" class="form-control text-center input-stepper-qty fs-5 p-0"
                            wire:model="quantity" readonly>
                        <button class="btn-stepper-action rounded-3 d-flex align-items-center justify-content-center"
                            type="button" wire:click="increaseQty"
                            {{ ($product->stock_quantity ?? 0) <= 0 || $quantity >= ($product->stock_quantity ?? 0) ? 'disabled' : '' }}>
                            <i class="fa-solid fa-plus fs-6"></i>
                        </button>
                    </div>

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

                    <button type="button" wire:click.stop="addToWishlist" wire:loading.attr="disabled"
                        class="btn btn-outline-secondary d-none d-sm-flex align-items-center justify-content-center border-2 border-light-subtle bg-white"
                        style="height: 48px; width: 48px; border-radius: 12px; transition: all 0.2s;">
                        <span wire:loading wire:target="addToWishlist"
                            class="spinner-border spinner-border-sm text-secondary" role="status"></span>
                        <span wire:loading.remove wire:target="addToWishlist">
                            @if ($isInWishlist)
                                <i class="fa-solid fa-heart fs-5 text-danger"></i>
                            @else
                                <i class="fa-regular fa-heart fs-5 text-muted"></i>
                            @endif
                        </span>
                    </button>
                </div>

                {{-- Vendor Premium Card Information --}}
                <div class="d-flex align-items-center justify-content-between vendor-profile-box p-3 mt-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="vendor-avatar-circle">
                            {{ strtoupper(substr($product->vendor->vendor_name ?? 'VN', 0, 2)) }}
                        </div>
                        <div>
                            <small class="text-muted d-block fw-semibold text-uppercase"
                                style="font-size: 0.7rem; letter-spacing: 0.3px;">Sold by</small>
                            <a href="#"
                                class="text-dark fw-bold text-decoration-none hover:text-primary fs-6">{{ $product->vendor->vendor_name ?? 'Unknown Vendor' }}</a>
                        </div>
                    </div>
                    <a href="#"
                        class="btn btn-sm btn-white border border-light-subtle rounded-3 bg-white px-3 py-1.5 fw-bold text-secondary"
                        style="font-size: 0.8rem;">Visit Store</a>
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
                timer: 1500,
                timerProgressBar: true,
                background: '#ffffff',
                color: '#1e293b',
                iconColor: data.type === 'success' ? '#10b981' : '#ef4444'
            });
        });
    });
</script>
