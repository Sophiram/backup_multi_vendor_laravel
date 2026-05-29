<?php

use Livewire\Volt\Component;
use App\Models\Product;

new class extends Component {
    protected $listeners = [
        'wishlist-updated' => '$refresh',
        'wishlistUpdated' => '$refresh',
    ];

    public function getWishlistItemsProperty()
    {
        return session()->get('wishlist', []);
    }

    public function removeFromWishlist($productId)
    {
        $wishlist = session()->get('wishlist', []);

        if (isset($wishlist[$productId])) {
            unset($wishlist[$productId]);
            session()->put('wishlist', $wishlist);

            $this->dispatch('wishlist-updated');
            $this->dispatch('notify', [
                'title' => 'Successfully removed from wishlist',
                'type' => 'error',
            ]);
        }
    }

    public function addToCart($data)
    {
        $productId = $data['productId'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        $wishlist = session()->get('wishlist', []);

        if (isset($wishlist[$productId])) {
            $item = $wishlist[$productId];
            $cart = session()->get('cart', []);

            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] += $quantity;
            } else {
                $cart[$productId] = [
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'image' => $item['image'],
                    'quantity' => $quantity,
                ];
            }

            session()->put('cart', $cart);

            $this->dispatch('cart-updated');
            $this->dispatch('notify', [
                'title' => 'Added ' . $quantity . ' item(s) to cart successfully!',
                'type' => 'success',
            ]);
        }
    }

    public function render(): mixed
    {
        return view('livewire.wishlist-page-component', [
            'wishlistItems' => $this->wishlistItems,
        ])->layout('layouts.user');
    }
};
?>

<div class="container py-5">

    <style>
        .wishlist-page-wrapper {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .product-title-text {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a !important;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.6rem;
            line-height: 1.3;
        }

        /* 🛠️ កែសម្រួលរចនាសម្ព័ន្ធប៊ូតុងខាងក្រោមឱ្យ Responsive បានល្អ */
        .premium-stepper-container {
            width: 100%;
        }

        .stepper-input-group {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            overflow: hidden;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            height: 38px;
        }

        .btn-stepper {
            border: none;
            background: #f1f5f9;
            color: #334155;
            width: 32px;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .btn-stepper:hover {
            background-color: #e2e8f0;
            color: #0f172a;
        }

        .qty-inline-input {
            border: none !important;
            background-color: transparent !important;
            color: #1e293b !important;
            font-weight: 700;
            font-size: 0.85rem;
            text-align: center;
            flex-grow: 1;
            width: 30px;
            padding: 0 !important;
            box-shadow: none !important;
            pointer-events: none;
        }

        .btn-premium-inline-cart {
            background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 0.8rem;
            border: none !important;
            border-radius: 10px !important;
            width: 100%;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            transition: all 0.25s ease-in-out !important;
            white-space: nowrap !important;
            box-shadow: 0 4px 10px rgba(124, 58, 237, 0.15);
        }

        .btn-premium-inline-cart:hover {
            background: linear-gradient(135deg, #f97316, #ea580c) !important;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.25);
            transform: translateY(-1px);
        }

        /* បង្រួមអក្សរលើប៊ូតុងបន្តិចពេលនៅលើទូរស័ព្ទតូចខ្លាំង ដើម្បីកុំឱ្យធ្លាក់បន្ទាត់ */
        @media (max-width: 380px) {
            .btn-premium-inline-cart span {
                display: none; /* បង្ហាញតែ Icon ឡានទិញទំនិញ ពេលអេក្រង់តូចពេក */
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

    <div class="wishlist-page-wrapper">
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="fw-bold text-dark">
                    <i class="fa-solid fa-heart me-2 text-danger"></i> My Wishlist
                    <span class="fs-5 text-muted fw-normal">({{ count($wishlistItems) }} Items)</span>
                </h3>
                <p class="text-muted">Manage all the products you have saved for later</p>
            </div>
        </div>

        @if (count($wishlistItems) > 0)
            <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-2 g-md-3 g-lg-4">
                @foreach ($wishlistItems as $id => $item)
                    <div class="col">
                        <div class="card h-100 border-0 position-relative border-light d-flex flex-column justify-content-between"
                            style="
                                border-radius: 22px;
                                border: 1px solid rgba(226, 232, 240, 0.7);
                                box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.03);
                                transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
                                overflow: hidden;
                            "
                            onmouseover="this.style.transform='translateY(-6px)'; this.style.borderColor='#7c3aed'; this.style.boxShadow='0 20px 35px -10px rgba(124, 58, 237, 0.15)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(226, 232, 240, 0.7)'; this.style.boxShadow='0 10px 30px -10px rgba(0, 0, 0, 0.03)';">

                            <div>
                                <!-- Trash Button -->
                                <button wire:click="removeFromWishlist('{{ $id }}')"
                                    class="btn position-absolute top-0 end-0 m-2 d-flex align-items-center justify-content-center p-0 border-0 shadow-none rounded-circle"
                                    style="width: 32px; height: 32px; color: #9ca3af; background-color: rgba(255,255,255,0.9); box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 10; transition: all 0.2s;"
                                    onmouseover="this.style.backgroundColor='#fee2e2'; this.style.color='#ef4444';"
                                    onmouseout="this.style.backgroundColor='rgba(255,255,255,0.9)'; this.style.color='#9ca3af';">
                                    <i class="fa-solid fa-trash-can" style="font-size: 0.9rem;"></i>
                                </button>

                                <!-- Product Image -->
                                <a href="{{ route('product.details', ['productId' => $id]) }}" class="d-block text-decoration-none">
                                    <div class="p-3 text-center bg-light position-relative" style="height: 180px; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);">
                                        <img src="{{ !empty($item['image']) ? asset('storage/' . $item['image']) : 'https://placehold.co/400x400?text=No+Image' }}"
                                            class="w-100 h-100 object-fit-contain" style="transition: transform 0.5s;"
                                            alt="{{ $item['name'] ?? 'Product' }}"
                                            onmouseover="this.style.transform='scale(1.06) rotate(2deg)';"
                                            onmouseout="this.style.transform='scale(1) rotate(0deg)';">
                                    </div>
                                </a>

                                <!-- Product Body -->
                                <div class="p-2 p-sm-3 pb-0">
                                    <span class="text-muted small text-uppercase fw-bold d-none d-sm-inline-block" style="font-size: 0.65rem; letter-spacing: 0.5px;">Saved Item</span>
                                    <h5 class="product-title-text mb-1" title="{{ $item['name'] ?? '' }}">
                                        <a href="{{ route('product.details', ['productId' => $id]) }}" class="text-dark text-decoration-none">
                                            {{ $item['name'] ?? 'Product Title' }}
                                        </a>
                                    </h5>
                                    <div class="my-1">
                                        <span class="fs-5 fw-bold text-danger" style="font-family: 'Outfit';">
                                            ${{ number_format($item['price'] ?? 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- 🛒 ផ្នែកកែសម្រួលថ្មី៖ ប្រើប្រាស់ Bootstrap Row/Col ងាយស្រួលរត់តាមអេក្រង់ -->
                            <div class="px-2 pb-2 px-sm-3 pb-sm-3 mt-auto">
                                <div x-data="{ quantity: 1, maxStock: 10 }" class="premium-stepper-container">
                                    <div class="row g-1 align-items-center">
                                        <!-- កំណត់ទំហំ 5/12 សម្រាប់ Stepper -->
                                        <div class="col-5">
                                            <div class="stepper-input-group">
                                                <button type="button" class="btn-stepper" @click="if(quantity > 1) quantity--">
                                                    <i class="fa-solid fa-minus"></i>
                                                </button>
                                                <input type="number" x-model="quantity" class="form-control qty-inline-input" readonly>
                                                <button type="button" class="btn-stepper" @click="if(quantity < maxStock) quantity++">
                                                    <i class="fa-solid fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- កំណត់ទំហំ 7/12 សម្រាប់ប៊ូតុង Add to Cart -->
                                        <div class="col-7">
                                            <button type="button"
                                                @click="$wire.addToCart({ productId: '{{ $id }}', quantity: quantity })"
                                                class="btn btn-premium-inline-cart">
                                                <i class="fa-solid fa-basket-shopping"></i>
                                                <span>Add to Cart</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="row justify-content-center py-5">
                <div class="col-md-6 text-center">
                    <div class="mb-4">
                        <i class="fa-regular fa-heart display-1 text-muted opacity-25"></i>
                    </div>
                    <h4 class="fw-bold text-secondary">Your wishlist is empty</h4>
                    <p class="text-muted mb-4">You haven't saved any products yet. Go back to the shop to find your favorite items.</p>
                    <a href="/" class="btn px-4 py-2 text-white fw-bold" style="background-color: #3b82f6; border-radius: 10px; text-decoration: none;">
                        <i class="fa-solid fa-arrow-left me-2"></i> Continue Shopping
                    </a>
                </div>
            </div>
        @endif
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
                timer: 2400,
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
