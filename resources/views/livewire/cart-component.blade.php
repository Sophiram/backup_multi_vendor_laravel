<?php

use App\Models\Product;
use App\Models\Category;
use Livewire\Volt\Component;

new class extends Component {
    protected $listeners = [
        'cart-updated' => '$refresh',
    ];

    public function getCartProperty()
    {
        return session()->get('cart', []);
    }

    public function increaseQuantity($productId)
    {
        $cart = $this->cart;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
            session()->put('cart', $cart);

            $this->dispatch('cartUpdated');
            $this->dispatch('notify', [
                'title' => 'Item quantity increased',
                'type' => 'success',
            ]);
        }
    }

    public function decreaseQuantity($productId)
    {
        $cart = $this->cart;

        if (isset($cart[$productId]) && $cart[$productId]['quantity'] > 1) {
            $cart[$productId]['quantity']--;
            session()->put('cart', $cart);

            $this->dispatch('cartUpdated');
            $this->dispatch('notify', [
                'title' => 'Item quantity decreased',
                'type' => 'info',
            ]);
        } else {
            $this->removeItem($productId);
            $this->dispatch('cartUpdated');
        }
    }

    public function removeItem($productId)
    {
        $cart = $this->cart;
        unset($cart[$productId]);
        session()->put('cart', $cart);

        $this->dispatch('cartUpdated');
        $this->dispatch('notify', [
            'title' => 'Item removed from cart',
            'type' => 'error',
        ]);
    }

    public function getTotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    public function render(): mixed
    {
        return view('livewire.cart-component', [
            'cartItems' => $this->cart,
            'total' => $this->total,
            'cartCount' => collect($this->cart)->sum('quantity'),
        ]);
    }
}; ?>

<div class="d-inline-block position-relative" x-data="{ open: false }" @click.away="open = false">

 <style>
        @media (max-width: 575px) {
            .wishlist-dropdown {
                position: fixed !important;
                /* ផ្លាស់ប្តូរពី absolute មក fixed ដើម្បីឱ្យវារត់តាម viewport */
                top: 70px !important;
                /* កំណត់ចម្ងាយពីខាងលើនៃអេក្រង់ទូរស័ព្ទ */
                left: 5% !important;
                right: 5% !important;
                width: 90vw !important;
                margin-top: 0 !important;
            }
        }
    </style>

    {{-- 🛒 ផ្នែកប៊ូតុង Cart (ទុកដដែល) --}}
    <button @click="open = !open"
        class="d-flex align-items-center justify-content-center position-relative border-0 px-3 py-2"
        style="
            background-color: #f7c200;
            border-radius: 12px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            height: 42px;
        "
        onmouseover="this.style.backgroundColor='#f18800'; this.style.transform='scale(1.02)';"
        onmouseout="this.style.backgroundColor='#f7c200'; this.style.transform='scale(1)';">
        <i class="fa-solid fa-cart-shopping me-2 fs-5" style="color: #fff3e4;"></i>
        {{-- <span class="text-dark fw-bold small"
            style="color: #ffffff; font-family: 'Inter', sans-serif; letter-spacing: -0.01em; ">Cart</span> --}}

        @if ($cartCount > 0)
            <span
                class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger d-flex align-items-center justify-content-center fw-bold"
                style="
                    width: 22px;
                    height: 22px;
                    font-size: 0.75rem;
                    margin-top: 3px;
                    margin-left: -4px;
                    box-shadow: 0 0 0 3px #ffffff;
                    background-color: #ef4444 !important;
                ">
                {{ $cartCount }}
            </span>
        @endif
    </button>

    {{-- 🛍️ ផ្ទាំង Pop-up Mini Cart (កែសម្រួល class និង style ថ្មីដើម្បីកុំឱ្យឃ្លាតឆ្ងាយ) --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="position-absolute mt-2 bg-white p-3 shadow-lg wishlist-dropdown"
        style="
            width: 360px;
            z-index: 1050;
            max-width: 90vw;
            display: none;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            border: 1px solid rgba(0,0,0,0.05);
            top: 100%;
            right: -200px;
            margin-top: 10px;
         ">

        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3"
            style="border-bottom-color: #f3f4f6 !important;">
            <h6 class="fw-bold m-0 text-dark" style="font-size: 0.95rem;">
                <i class="fa-solid fa-shopping-bag me-2" style="color: #4f46e5;"></i> Mini Cart
            </h6>
            <span class="badge rounded-pill fw-semibold px-2 py-1"
                style="background-color: #e0e7ff; color: #4338ca; font-size: 0.75rem;">{{ $cartCount }} Items</span>
        </div>

        {{-- បញ្ជីផលិតផលក្នុង Pop-up (ទាញចេញពី $item) --}}
        <div class="overflow-y-auto pe-1" style="max-height: 260px; scrollbar-width: thin;">
            @forelse ($cartItems as $id => $item)
                <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom"
                    style="border-bottom-color: #f9fafb !important;">
                    <div class="d-flex align-items-center">

                        {{-- បង្ហាញរូបភាពពី session --}}
                        <img src="{{ !empty($item['image']) ? asset('storage/' . $item['image']) : asset('images/no-image.jpg') }}"
                            class="rounded-3 me-3 object-fit-cover bg-light" alt="Product"
                            style="width: 55px; height: 55px; border: 1px solid #f3f4f6;">

                        <div>
                            <h6 class="fw-bold text-dark m-0 small text-truncate" style="max-width: 150px;">
                                {{ $item['name'] ?? 'Product Title' }}
                            </h6>
                            <div class="mt-1">
                                <span class="fw-bold small"
                                    style="color: #4f46e5;">${{ number_format($item['price'], 2) }}</span>
                                <span class="text-muted small ms-1"
                                    style="font-size: 0.75rem;">x{{ $item['quantity'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column align-items-end justify-content-between">
                        {{-- Red Hover Background --}}
                        <button wire:click="removeItem('{{ $id }}')"
                            class="btn d-flex align-items-center justify-content-center p-0 border-0 mb-2 shadow-none rounded-circle"
                            style="width: 28px; height: 28px; color: #9ca3af; background-color: transparent; transition: all 0.2s ease;"
                            onmouseover="this.style.backgroundColor='#fee2e2'; this.style.color='#ef4444';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#9ca3af';">
                            <i class="fa-solid fa-trash-can" style="font-size: 0.85rem;"></i>
                        </button>

                        <div class="input-group input-group-sm rounded-2 shadow-sm"
                            style="width: 75px; border: 1px solid #e5e7eb; height: 24px;">
                            <button
                                class="btn btn-white bg-white border-0 p-0 text-center fw-bold text-muted small lh-1"
                                type="button" wire:click="decreaseQuantity('{{ $id }}')"
                                style="width: 22px;">-</button>
                            <input type="text"
                                class="form-control text-center bg-white border-0 p-0 fw-bold text-dark"
                                value="{{ $item['quantity'] }}" readonly style="font-size: 0.7rem; height: 22px;">
                            <button class="btn btn-white bg-white border-0 p-0 text-center fw-bold lh-1" type="button"
                                wire:click="increaseQuantity('{{ $id }}')"
                                style="width: 22px; color: #4f46e5;">+</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="fa-solid fa-basket-shopping fs-2 opacity-25 mb-2 d-block text-secondary"></i>
                    <p class="text-muted small m-0">Your cart is empty</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if ($cartCount > 0)
            <div class="mt-3 pt-2 border-top" style="border-top-color: #f3f4f6 !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small fw-medium">Subtotal:</span>
                    <span class="fw-extrabold text-dark fs-5"
                        style="font-weight: 800; letter-spacing: -0.02em;">${{ number_format($total, 2) }}</span>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('cart') }}" class="btn w-100 py-2 fw-bold text-center"
                            style="background-color: #f3f4f6; color: #4b5563; border-radius: 10px; font-size: 0.8rem; text-decoration: none; transition: background 0.15s;"
                            onmouseover="this.style.backgroundColor='#e5e7eb'"
                            onmouseout="this.style.backgroundColor='#f3f4f6'">
                            View Cart
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('checkout') }}" class="btn text-white w-100 py-2 fw-bold text-center"
                            style="background-color: #4f46e5; border-radius: 10px; font-size: 0.8rem; text-decoration: none; transition: opacity 0.15s;"
                            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                            Checkout
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
