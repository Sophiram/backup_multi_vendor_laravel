<?php

use App\Models\Product;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component {

    // 🛒 1. Listen for the 'addToCartFromAnywhere' event globally
    #[On('addToCartFromAnywhere')]
    public function addToCart($productId, $quantity = 1)
    {
        if (is_array($productId)) {
            $quantity = $productId['quantity'] ?? 1;
            $productId = $productId['productId'] ?? null;
        }

        $quantity = intval($quantity);

        if (!$productId) {
            return;
        }

        $product = Product::with('images')->find($productId);
        if (!$product) {
            return;
        }

        $cart = session()->get('cart', []);
        $productImage = $product->images->first() ? $product->images->first()->image_path : null;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'name'     => $product->product_name,
                'price'    => $product->discounted_price ?? $product->regular_price,
                'quantity' => $quantity,
                'image'    => $productImage
            ];
        }

        session()->put('cart', $cart);

        $this->dispatch('cart-updated');
        $this->dispatch('notify', [
            'title' => 'Successfully added to cart!',
            'type'  => 'success',
        ]);
    }

    // ❤️ 2. Listen for the 'addToWishlistFromAnywhere' event globally using modern Livewire attribute
    #[On('addToWishlistFromAnywhere')]
    public function toggleWishlist($productId)
    {
        // Handle array payload structure if dispatched as an array from details component
        if (is_array($productId)) {
            $productId = $productId['productId'] ?? null;
        }

        if (!$productId) {
            return;
        }

        $wishlist = session()->get('wishlist', []);

        if (isset($wishlist[$productId])) {
            // If already exists, remove it
            unset($wishlist[$productId]);
            $this->dispatch('notify', [
                'title' => 'Removed from wishlist',
                'type' => 'error'
            ]);
        } else {
            // Fetch product details with its related images
            $product = Product::with('images')->find($productId);

            if ($product) {
                // Get the first image path from product images relationship
                $productImage = $product->images->first() ? $product->images->first()->image_path : null;

                $wishlist[$productId] = [
                    'name' => $product->product_name,
                    'price' => $product->discounted_price ?? $product->regular_price,
                    'image' => $productImage,
                ];

                $this->dispatch('notify', [
                    'title' => 'Added to wishlist',
                    'type' => 'success'
                ]);
            }
        }

        session()->put('wishlist', $wishlist);

        // Fire events to notify header components to update instantly
        $this->dispatch('wishlist-updated');
    }
};
?>

<div style="display: none;"></div>
