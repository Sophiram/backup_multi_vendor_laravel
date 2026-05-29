<?php

use Livewire\Volt\Component;

new class extends Component {
    public $paymentMethod = 'aba';
    public $promoCode = '';
    public $discount = 0.00;
    public $shipping = 5.00;
    public $appliedCoupon = null; // រក្សាទុកឈ្មោះ Coupon ដែលបានប្រើជោគជ័យ

    // ទាញយកទិន្នន័យពី Cart Session
    public function getCartItemsProperty()
    {
        return session()->get('cart', []);
    }

    // គណនាតម្លៃទំនិញសរុប (មិនទាន់បូកសេវាដឹក និងដក Discount)
    public function getSubtotalProperty()
    {
        return collect($this->cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    // គណនាតម្លៃចុងក្រោយដែលត្រូវទូទាត់
    public function getTotalProperty()
    {
        if (count($this->cartItems) === 0) {
            return 0;
        }
        $total = $this->subtotal + $this->shipping - $this->discount;
        return $total > 0 ? $total : 0;
    }

    // មុខងារបង្កើន ឬបន្ថយបរិមាណទំនិញ (+ / -)
    public function updateQuantity($id, $change)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $change;

            if ($cart[$id]['quantity'] <= 0) {
                unset($cart[$id]);
                session()->flash('info', 'Item removed from cart.');
            } else {
                session()->put('cart', $cart);
            }

            session()->put('cart', $cart);

            // គណនា Discount ឡើងវិញបើមានការផ្លាស់ប្តូរបរិមាណទំនិញ
            if ($this->appliedCoupon) {
                $this->applyPromo();
            }
        }
    }

    // មុខងារលុបទំនិញចេញពីកន្ត្រក (រូបធុងសំរាម)
    public function removeItem($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            session()->flash('info', 'Item removed from cart.');

            if (count($cart) === 0) {
                $this->removePromo();
            } elseif ($this->appliedCoupon) {
                $this->applyPromo();
            }
        }
    }

    // 🎯 មុខងារថ្មី៖ ពិនិត្យ និងអនុវត្តកូដបញ្ចុះតម្លៃ (Apply Promo Code)
    public function applyPromo()
    {
        if (empty($this->promoCode)) {
            session()->flash('error', 'Please enter a promo code.');
            return;
        }

        // ឧទាហរណ៍៖ បង្កើតលក្ខខណ្ឌកូដបញ្ចុះតម្លៃ (បងអាចដូរទៅទាញពី DB តាមក្រោយបាន)
        $coupons = [
            'DISCOUNT10' => ['type' => 'percentage', 'value' => 10], // ចុះ ១០ ភាគរយ
            'QUICK5'     => ['type' => 'fixed', 'value' => 5.00],    // ចុះ ៥ ដុល្លារថេរ
        ];

        $code = strtoupper(trim($this->promoCode));

        if (array_key_exists($code, $coupons)) {
            $coupon = $coupons[$code];
            $this->appliedCoupon = $code;

            if ($coupon['type'] === 'percentage') {
                $this->discount = ($this->subtotal * $coupon['value']) / 100;
            } else {
                $this->discount = $coupon['value'];
            }

            // ការពារកុំឱ្យតម្លៃ Discount ធំជាងតម្លៃទំនិញសរុប
            if ($this->discount > $this->subtotal) {
                $this->discount = $this->subtotal;
            }

            session()->flash('success', 'Promo code applied successfully!');
        } else {
            $this->discount = 0.00;
            $this->appliedCoupon = null;
            session()->flash('error', 'Invalid or expired promo code.');
        }
    }

    // 🎯 មុខងារថ្មី៖ លុបកូដបញ្ចុះតម្លៃវិញ
    public function removePromo()
    {
        $this->discount = 0.00;
        $this->promoCode = '';
        $this->appliedCoupon = null;
        session()->flash('info', 'Promo code removed.');
    }

    // ដំណើរការបន្តទៅកាន់ការទូទាត់ប្រាក់
    public function proceedToCheckout()
    {
        if (count($this->cartItems) === 0) return;

        session()->put('selected_payment_method', $this->paymentMethod);
        session()->put('cart_shipping', $this->shipping);
        session()->put('cart_discount', $this->discount);
        session()->put('applied_coupon', $this->appliedCoupon);

        return redirect()->route('checkout');
    }
}; ?>

<div class="cart-wrapper" style="background-color: #f8f9fa; min-height: 100vh; padding: 40px 0; font-family: 'Inter', sans-serif;">
    <div class="container">

        <!-- 🔔 ផ្ទាំងបង្ហាញសារជូនដំណឹង (Alert Messages) -->
        <div class="row">
            <div class="col-12">
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session()->has('info'))
                    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
        </div>

        <div class="row g-4">
            <!-- Cart Items Section -->
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0 fw-bold text-dark">Shopping Cart</h4>
                    <span class="text-muted fw-medium">{{ count($this->cartItems) }} {{ count($this->cartItems) > 1 ? 'items' : 'item' }}</span>
                </div>

                <!-- Product Cards List -->
                <div class="d-flex flex-column gap-3">
                    @forelse($this->cartItems as $id => $item)
                        <div class="product-card p-3 shadow-sm border-0 bg-white rounded-3">
                            <div class="row align-items-center g-3">
                                <!-- Image -->
                                <div class="col-3 col-md-2">
                                    <img src="{{ !empty($item['image']) ? asset('storage/' . $item['image']) : 'https://placehold.co/100x100?text=No+Img' }}"
                                         alt="{{ $item['name'] }}" class="product-image w-100 object-fit-cover rounded-3" style="height: 90px;">
                                </div>

                                <!-- Details -->
                                <div class="col-9 col-md-4">
                                    <h6 class="mb-1 fw-bold text-dark" style="font-size: 1rem;">{{ $item['name'] }}</h6>
                                    <p class="text-muted mb-0 small">Price: ${{ number_format($item['price'], 2) }}</p>
                                    @if(isset($item['discount']) && $item['discount'] > 0)
                                        <span class="discount-badge d-inline-block mt-1">{{ $item['discount'] }}% OFF</span>
                                    @endif
                                </div>

                                <!-- Quantity Controls -->
                                <div class="col-6 col-md-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" wire:click="updateQuantity('{{ $id }}', -1)" class="quantity-btn">-</button>
                                        <input type="number" class="quantity-input fw-medium text-dark" value="{{ $item['quantity'] }}" readonly>
                                        <button type="button" wire:click="updateQuantity('{{ $id }}', 1)" class="quantity-btn">+</button>
                                    </div>
                                </div>

                                <!-- Subtotal per item -->
                                <div class="col-4 col-md-2 text-md-end">
                                    <span class="fw-bold text-dark fs-5">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                </div>

                                <!-- Remove Action -->
                                <div class="col-2 col-md-1 text-end">
                                    <i wire:click="removeItem('{{ $id }}')" class="bi bi-trash remove-btn fs-5" title="Remove item"></i>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-white text-center py-5 rounded-3 border-0 shadow-sm bg-white">
                            <i class="bi bi-basket3 display-4 text-muted mb-3 opacity-50"></i>
                            <p class="text-muted mb-0 fw-medium">Your shopping cart is empty.</p>
                        </div>
                    @endforelse
                </div>

                <!-- 💳 Payment Methods Section -->
                @if(count($this->cartItems) > 0)
                    <div class="card p-4 border-0 shadow-sm rounded-3 mt-4 bg-white">
                        <h5 class="fw-bold mb-3 text-dark">
                            <i class="bi bi-credit-card me-2 text-primary"></i>Select Payment Method
                        </h5>

                        <div class="row g-2">
                            <!-- ABA Pay -->
                            <div class="col-6 col-sm-3">
                                <label class="w-100 m-0 cursor-pointer">
                                    <input type="radio" wire:model="paymentMethod" value="aba" class="btn-check" name="payment_option">
                                    <div class="btn btn-outline-light d-flex flex-column align-items-center justify-content-center p-3 rounded-3 border text-dark h-100 w-100 payment-card">
                                        <span class="fw-bold text-uppercase text-info mb-1" style="font-size: 0.9rem; letter-spacing: 0.5px;">ABA Pay</span>
                                        <small class="text-muted" style="font-size: 0.7rem;">Instant Pay</small>
                                    </div>
                                </label>
                            </div>

                            <!-- Bakong -->
                            <div class="col-6 col-sm-3">
                                <label class="w-100 m-0 cursor-pointer">
                                    <input type="radio" wire:model="paymentMethod" value="bakong" class="btn-check" name="payment_option">
                                    <div class="btn btn-outline-light d-flex flex-column align-items-center justify-content-center p-3 rounded-3 border text-dark h-100 w-100 payment-card">
                                        <span class="fw-bold text-uppercase text-danger mb-1" style="font-size: 0.9rem; letter-spacing: 0.5px;">Bakong</span>
                                        <small class="text-muted" style="font-size: 0.7rem;">KHQR Code</small>
                                    </div>
                                </label>
                            </div>

                            <!-- Credit Card -->
                            <div class="col-6 col-sm-3">
                                <label class="w-100 m-0 cursor-pointer">
                                    <input type="radio" wire:model="paymentMethod" value="card" class="btn-check" name="payment_option">
                                    <div class="btn btn-outline-light d-flex flex-column align-items-center justify-content-center p-3 rounded-3 border text-dark h-100 w-100 payment-card">
                                        <span class="fw-bold text-uppercase text-primary mb-1" style="font-size: 0.9rem; letter-spacing: 0.5px;">Visa/Master</span>
                                        <small class="text-muted" style="font-size: 0.7rem;">Credit/Debit</small>
                                    </div>
                                </label>
                            </div>

                            <!-- Acleda -->
                            <div class="col-6 col-sm-3">
                                <label class="w-100 m-0 cursor-pointer">
                                    <input type="radio" wire:model="paymentMethod" value="acleda" class="btn-check" name="payment_option">
                                    <div class="btn btn-outline-light d-flex flex-column align-items-center justify-content-center p-3 rounded-3 border text-dark h-100 w-100 payment-card">
                                        <span class="fw-bold text-uppercase mb-1" style="font-size: 0.9rem; color: #0d3b66; letter-spacing: 0.5px;">Acleda</span>
                                        <small class="text-muted" style="font-size: 0.7rem;">ToanChet</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Order Summary Section -->
            <div class="col-lg-4">
                <div class="summary-card p-4 shadow-sm border-0 bg-white rounded-3 position-sticky" style="top: 20px;">
                    <h5 class="mb-4 fw-bold text-dark">Order Summary</h5>

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-semibold text-dark">${{ number_format($this->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Discount</span>
                        <span class="text-success fw-semibold">-${{ number_format($this->discount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Shipping</span>
                        <span class="fw-semibold text-dark">${{ number_format($this->shipping, 2) }}</span>
                    </div>
                    <hr class="my-3" style="border-color: #e2e8f0;">
                    <div class="d-flex justify-content-between mb-4 fs-5">
                        <span class="fw-bold text-dark">Total</span>
                        <span class="fw-bold text-danger">${{ number_format($this->total, 2) }}</span>
                    </div>

                    <!-- 🎫 ផ្នែក Promo Code Input Box (កែសម្រួលឱ្យមានប៊ូតុង Apply និងប៊ូតុងលុប) -->
                    <div class="mb-4">
                        @if(!$appliedCoupon)
                            <div class="input-group">
                                <input type="text" wire:model="promoCode" class="form-control border-end-0 shadow-none" placeholder="Try 'DISCOUNT10' or 'QUICK5'" style="border-radius: 8px 0 0 8px; font-size: 0.95rem;">
                                <button wire:click="applyPromo" class="btn btn-primary" type="button" style="border-radius: 0 8px 8px 0; font-size: 0.95rem;">Apply</button>
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded-3 border border-dashed border-success">
                                <div class="small">
                                    <span class="fw-bold text-success"><i class="bi bi-ticket-perforated-fill me-1"></i>{{ $appliedCoupon }}</span>
                                    <span class="text-muted ms-1">(Applied)</span>
                                </div>
                                <button type="button" wire:click="removePromo" class="btn btn-sm btn-link text-danger text-decoration-none fw-semibold p-0 pe-1">Remove</button>
                            </div>
                        @endif
                    </div>

                    @if(count($this->cartItems) > 0)
                        <button wire:click="proceedToCheckout" class="btn btn-primary checkout-btn w-100 mb-3 fw-bold py-2.5 rounded-3 text-white">
                            Proceed to Checkout
                        </button>
                    @else
                        <button class="btn btn-secondary w-100 mb-3 fw-bold py-2.5 rounded-3 opacity-50" disabled>
                            Cart is Empty
                        </button>
                    @endif

                    <div class="d-flex justify-content-center gap-2 align-items-center">
                        <i class="bi bi-shield-check text-success fs-5"></i>
                        <small class="text-muted fw-medium">Secure checkout powered by Livewire</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Custom CSS សម្រាប់លម្អបន្ថែមពីលើ Bootstrap -->
<style>
        .border-dashed { border-style: dashed !important; }

    .product-card {
        transition: transform 0.2s ease-in-out;
    }
    .product-card:hover {
        transform: translateY(-2px);
    }
    .quantity-input {
        width: 50px;
        text-align: center;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 2px 0;
        font-size: 0.95rem;
    }
    .quantity-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background: #f1f5f9;
        border: none;
        font-weight: bold;
        color: #475569;
        transition: all 0.2s;
    }
    .quantity-btn:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    .remove-btn {
        color: #ef4444;
        cursor: pointer;
        transition: color 0.2s;
    }
    .remove-btn:hover {
        color: #b91c1c;
    }
    .discount-badge {
        background: #dcfce7;
        color: #15803d;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 6px;
    }
    .cursor-pointer { cursor: pointer; }
    .payment-card {
        background-color: #fafafa;
        border-color: #e2e8f0 !important;
        transition: all 0.2s ease-in-out;
    }
    .payment-card:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1 !important;
        transform: translateY(-2px);
    }
    .btn-check:checked + .payment-card {
        background-color: #f5f3ff !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }
    .btn-check:checked + .payment-card span {
        color: #6366f1 !important;
    }
    .checkout-btn {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        border: none;
        transition: all 0.2s;
    }
    .checkout-btn:hover {
        transform: translateY(-2px);
        background: linear-gradient(135deg, #4f46e5, #4338ca);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }
</style>
