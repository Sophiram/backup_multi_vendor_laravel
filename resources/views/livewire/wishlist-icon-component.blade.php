<?php

use Livewire\Volt\Component;

new class extends Component {
    // Add the direct event to the listeners array so it refreshes instantly
    protected $listeners = [
        'wishlist-updated' => '$refresh',
        'wishlistUpdated' => '$refresh',
        'addToWishlistFromAnywhere' => '$refresh', // 🔥 Add this line
    ];

    public function getWishlistItemsProperty()
    {
        return session()->get('wishlist', []);
    }

    public function getWishlistCountProperty()
    {
        return count($this->wishlistItems);
    }

    public function removeFromWishlist($productId)
    {
        $wishlist = session()->get('wishlist', []);

        if (isset($wishlist[$productId])) {
            unset($wishlist[$productId]);
            session()->put('wishlist', $wishlist);

            $this->dispatch('wishlist-updated');
            $this->dispatch('notify', [
                'title' => 'Removed from wishlist',
                'type' => 'error',
            ]);
        }
    }

    public function render(): mixed
    {
        return view('livewire.wishlist-icon-component', [
            'wishlistItems' => $this->wishlistItems,
            'wishlistCount' => $this->wishlistCount,
        ]);
    }
};
?>

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

    <button @click="open = !open"
        class="d-flex align-items-center justify-content-center position-relative border-0 px-3 py-2"
        style="
            background-color: #f3f4f6;
            border-radius: 12px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            height: 42px;
        "
        onmouseover="this.style.backgroundColor='#e5e7eb'; this.style.transform='scale(1.02)';"
        onmouseout="this.style.backgroundColor='#f3f4f6'; this.style.transform='scale(1)';">
        @if ($wishlistCount > 0)
            <i class="fa-solid fa-heart fs-5 text-danger"></i>
        @else
            <i class="fa-regular fa-heart fs-5 text-dark"></i>
        @endif

        @if ($wishlistCount > 0)
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
                {{ $wishlistCount }}
            </span>
        @endif
    </button>

    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="position-absolute mt-2 bg-white p-3 shadow-lg wishlist-dropdown"
        style="
        width: 340px;
        max-width: 90vw;
        z-index: 1050;
        display: none;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.08);

        /* សម្រាប់ Desktop រក្សាទុកកន្លែងនេះ */
        top: 100%;
        right: -280px;
        margin-top: 10px;
     ">

        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3"
            style="border-bottom-color: #f3f4f6 !important;">
            <h6 class="fw-bold m-0 text-dark" style="font-size: 0.95rem;">
                <i class="fa-solid fa-heart me-2" style="color: #ef4444;"></i> My Wishlist
            </h6>
            <span class="badge rounded-pill fw-semibold px-2 py-1"
                style="background-color: #fee2e2; color: #ef4444; font-size: 0.75rem;">{{ $wishlistCount }} Items</span>
        </div>

        <div class="overflow-y-auto pe-1" style="max-height: 260px; scrollbar-width: thin;">
            @forelse ($wishlistItems as $id => $item)
                <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom"
                    style="border-bottom-color: #f9fafb !important;">

                    <div class="d-flex align-items-center">
                        <img src="{{ !empty($item['image']) ? asset('storage/' . $item['image']) : asset('images/no-image.jpg') }}"
                            class="rounded-3 me-3 object-fit-cover bg-light" alt="Product"
                            style="width: 55px; height: 55px; border: 1px solid #f3f4f6;">

                        <div>
                            <h6 class="fw-bold text-dark m-0 small text-truncate" style="max-width: 170px;">
                                {{ $item['name'] ?? 'Product Title' }}
                            </h6>
                            <div class="mt-1">
                                <span class="fw-bold small" style="color: #ef4444;">
                                    ${{ number_format($item['price'] ?? 0, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <button wire:click="removeFromWishlist('{{ $id }}')"
                            class="btn d-flex align-items-center justify-content-center p-0 border-0 shadow-none rounded-circle"
                            style="width: 28px; height: 28px; color: #9ca3af; background-color: transparent; transition: all 0.2s ease;"
                            onmouseover="this.style.backgroundColor='#fee2e2'; this.style.color='#ef4444';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#9ca3af';">
                            <i class="fa-solid fa-trash-can" style="font-size: 0.85rem;"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="fa-regular fa-heart fs-2 opacity-25 mb-2 d-block text-secondary"></i>
                    <p class="text-muted small m-0">Your wishlist is empty</p>
                </div>
            @endforelse
        </div>

        @if ($wishlistCount > 0)
            <div class="mt-3 pt-2 border-top" style="border-top-color: #f3f4f6 !important;">
                <a href="/wishlist" class="btn w-100 py-2 fw-bold text-center text-white"
                    style="background-color: #ef4444; border-radius: 10px; font-size: 0.8rem; text-decoration: none; transition: opacity 0.15s;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    View All Wishlist &rarr;
                </a>
            </div>
        @endif
    </div>
</div>
