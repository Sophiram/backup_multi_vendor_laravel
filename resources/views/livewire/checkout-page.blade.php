<?php

use Livewire\Volt\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

new class extends Component {
    public $fullName = '';
    public $phone = '';
    public $email = '';
    public $address = '';
    public $city = 'Phnom Penh';
    public $note = '';

    public $cartItems = [];
    public $paymentMethodId = null;
    public $selectedCompanyId = null;
    public $shipping = 0.0;
    public $discount = 0.0;
    public $subtotal = 0.0;
    public $total = 0.0;

    public function mount()
    {
        $this->cartItems = session()->get('cart', []);

        if (count($this->cartItems) === 0) {
            return redirect()->route('cart');
        }

        $this->shipping = session()->get('cart_shipping', 5.0);
        $this->discount = session()->get('cart_discount', 0.0);

        $this->subtotal = collect($this->cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        $this->total = $this->subtotal + $this->shipping - $this->discount;
        if ($this->total < 0) {
            $this->total = 0;
        }

        if (auth()->check()) {
            $this->fullName = auth()->user()->name;
            $this->email = auth()->user()->email;
        }

        $sessionMethodId = session()->get('selected_payment_method_id');

        if ($sessionMethodId && PaymentMethod::where('id', $sessionMethodId)->where('status', true)->exists()) {
            $this->paymentMethodId = $sessionMethodId;
        } else {
            $firstMethod = PaymentMethod::where('status', true)->first();
            $this->paymentMethodId = $firstMethod ? $firstMethod->id : null;
        }
    }

    public function getAvailablePaymentMethodsProperty()
    {
        return PaymentMethod::where('status', true)->get();
    }

    public function getSelectedMethodProperty()
    {
        return PaymentMethod::find($this->paymentMethodId);
    }

    public function updatedSelectedCompanyId($value)
    {
        $company = \App\Models\ShippingCompany::find($value);
        $this->shipping = $company ? $company->shipping_fee : 0.0;
        $this->total = $this->subtotal + $this->shipping - $this->discount;

        if ($this->total < 0) {
            $this->total = 0;
        }
    }

    public function placeOrder()
    {
        $this->validate(
            [
                'fullName' => 'required|string|min:3',
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
                'address' => 'required|string',
                'city' => 'required',
                'selectedCompanyId' => 'required',
                'paymentMethodId' => 'required|exists:payment_methods,id',
            ],
            [
                'selectedCompanyId.required' => 'Please select a shipping company.',
                'paymentMethodId.required' => 'Please select a payment method.',
            ],
        );

        $methodName = $this->selectedMethod ? $this->selectedMethod->name : 'Unknown';
        $methodType = $this->selectedMethod ? $this->selectedMethod->type : 'manual_bank';

        try {
            $result = DB::transaction(function () use ($methodName, $methodType) {
                // ១. បង្កើត Order
                $newOrder = Order::create([
                    'user_id' => auth()->id(),
                    'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                    'total_amount' => $this->total,
                    'status' => 'pending',
                    'shipping_address' => $this->address . ', ' . $this->city,
                    'payment_method' => $methodName,
                    'note' => $this->note,
                ]);
                \App\Models\Shipping::create([
                    'order_id' => $newOrder->id,
                    'shipping_company_id' => $this->selectedCompanyId,
                    'tracking_number' => 'TRK-' . strtoupper(Str::random(10)), // Generate កូដតាមដាននៅទីនេះ
                    'shipping_cost' => $this->shipping,
                    'shipping_status' => 'pending',
                ]);

                // ២. ពិនិត្យស្តុក និងកាត់ស្តុក
                foreach ($this->cartItems as $productId => $item) {
                    $product = \App\Models\Product::with('store.vendor')->where('id', $productId)->lockForUpdate()->first();

                    if (!$product) {
                        throw new \Exception('Product not found.');
                    }

                    if ($product->stock_quantity < $item['quantity']) {
                        throw new \Exception("Product {$product->name} is out of stock.");
                    }

                    $product->decrement('stock_quantity', $item['quantity']);
                    $basePrice = $product->discounted_price > 0 ? $product->discounted_price : $product->regular_price;
                    $additionalPrice = 0;

                    if (!empty($item['attribute_value_id'])) {
                        $productAttribute = DB::table('product_attributes')->where('product_id', $productId)->where('attribute_value_id', $item['attribute_value_id'])->first();

                        if ($productAttribute) {
                            $additionalPrice = $productAttribute->additional_price;
                        }
                    }
                    $finalPrice = $basePrice + $additionalPrice;
                    $totalItemPrice = $finalPrice * $item['quantity'];

                    $rule = \App\Models\CommissionRule::where('category_id', $product->category_id)->where('status', 'Active')->first();

                    $commissionRate = $rule ? $rule->commission_rate : 0.0;
                    $commissionAmount = ($totalItemPrice * $commissionRate) / 100;
                    $vendorNetAmount = $totalItemPrice - $commissionAmount;

                    OrderItem::create([
                        'order_id' => $newOrder->id,
                        'product_id' => $productId,
                        'vendor_id' => $product->store->vendor->user_id ?? null,
                        'quantity' => $item['quantity'],
                        'price' => $finalPrice,
                        'total' => $totalItemPrice,
                        'commission_rate' => $commissionRate, // បន្ថែមភាគរយ
                        'commission_amount' => $commissionAmount, // បន្ថែមលុយ Commission
                        'vendor_net_amount' => $vendorNetAmount, // បន្ថែមលុយអ្នកលក់
                    ]);
                }

                // ៣. បង្កើតទិន្នន័យ Payment លំនាំដើម
                Payment::create([
                    'order_id' => $newOrder->id,
                    'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
                    'amount' => $this->total,
                    'status' => 'pending',
                    'payment_method' => $methodName,
                ]);

                return [
                    'order' => $newOrder,
                    'type' => $methodType,
                ];
            });

            session()->forget(['cart', 'selected_payment_method_id', 'cart_shipping', 'cart_discount', 'applied_coupon']);
            session()->flash('success', 'Your order has been placed successfully!');

            if ($methodType === 'direct_integration' || $methodType === 'qr_payment') {
                return redirect()->route('payment.qr', ['order' => $result['order']->id]);
            } else {
                return redirect()->route('receipt', ['order' => $result['order']->id]);
            }
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return;
        }
    }
}; ?>


<div class="checkout-wrapper py-5">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .checkout-wrapper {
            background-color: #f8fafc;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .premium-heading {
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.5px;
        }

        .premium-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 24px;
            box-shadow: 0 10px 25px -15px rgba(0, 0, 0, 0.03);
        }

        .premium-form .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .premium-form .form-control,
        .premium-form .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 11px 16px;
            font-size: 0.92rem;
            color: #0f172a;
            background-color: #f8fafc;
            transition: all 0.2s;
        }

        .premium-form .form-control:focus,
        .premium-form .form-select:focus {
            background-color: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .premium-form .is-invalid {
            border-color: #ef4444 !important;
            background-image: none !important;
        }

        .payment-method-card {
            cursor: pointer;
        }

        .payment-method-card input[type="radio"] {
            display: none;
        }

        .payment-method-card .card-body {
            border: 2px solid #f1f5f9;
            background-color: #ffffff;
            transition: all 0.25s ease;
        }

        .payment-method-card:hover .card-body {
            border-color: #cbd5e1;
            background-color: #f8fafc;
        }

        .payment-method-card input[type="radio"]:checked+.card-body {
            border-color: #6366f1;
            background-color: #f8fafc;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.12);
        }

        .gateway-logo-badge {
            height: 38px;
            width: 50px;
            object-fit: contain;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .review-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px dashed #f1f5f9;
        }

        .review-item-row:last-child {
            border-bottom: none;
        }

        .review-product-img {
            width: 46px;
            height: 46px;
            background: #f8fafc;
            border-radius: 10px;
            object-fit: contain;
            padding: 2px;
            border: 1px solid #e2e8f0;
        }

        .style-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .style-scroll::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }

        .summary-price-text {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
        }

        .place-order-btn-premium {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            border-radius: 14px !important;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.25);
        }

        .place-order-btn-premium:hover:not([disabled]) {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
            transform: translateY(-2px);
        }
    </style>

    <div class="container animate__animated animate__fadeIn">

        {{-- បង្ហាញ Error Message ប្រសិនបើការបញ្ជាទិញមានបញ្ហា --}}
        @if (session()->has('error'))
            <div class="alert alert-danger rounded-4 mb-4 border-0 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span class="fw-semibold small">{{ session('error') }}</span>
            </div>
        @endif

        <div class="row g-4">
            <!-- 📝 Form បំពេញព័ត៌មានដឹកជញ្ជូន និងវិធីសាស្ត្រទូទាត់ប្រាក់ -->
            <div class="col-lg-7">
                <div class="premium-card p-4 premium-form">
                    <h5 class="fw-bold text-dark mb-1 premium-heading">
                        <i class="bi bi-truck me-2 text-primary"></i>Shipping Information
                    </h5>
                    <p class="text-muted small mb-4">Provide secure destination address details for accurate dispatch
                        courier</p>

                    {{-- Form ដើរតួជាក្បាលម៉ាស៊ីន Submit ទិន្នន័យ --}}
                    <form wire:submit.prevent="placeOrder" id="checkoutForm">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" wire:model="fullName"
                                    class="form-control @error('fullName') is-invalid @enderror"
                                    placeholder="e.g. John Doe">
                                @error('fullName')
                                    <div class="invalid-feedback fw-semibold small mt-1"><i
                                            class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" wire:model="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    placeholder="e.g. 012345678">
                                @error('phone')
                                    <div class="invalid-feedback fw-semibold small mt-1"><i
                                            class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email Address <span class="text-muted text-capitalize"
                                        style="font-size:0.7rem;">(Optional)</span></label>
                                <input type="email" wire:model="email" class="form-control"
                                    placeholder="e.g. john@example.com">
                            </div>

                            <div class="col-12 mt-3">
                                <label class="form-label">Select Shipping Courier <span
                                        class="text-danger">*</span></label>
                                <select wire:model.live="selectedCompanyId"
                                    class="form-select @error('selectedCompanyId') is-invalid @enderror">
                                    <option value="">-- Choose a courier --</option>
                                    @foreach (\App\Models\ShippingCompany::where('is_active', true)->get() as $company)
                                        <option value="{{ $company->id }}">
                                            {{ $company->name }} - ${{ number_format($company->shipping_fee, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedCompanyId')
                                    <div class="invalid-feedback fw-semibold small mt-1 d-block"><i
                                            class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">City / Province <span class="text-danger">*</span></label>
                                <select wire:model="city" class="form-select">
                                    <option value="Phnom Penh">Phnom Penh</option>
                                    <option value="Siem Reap">Siem Reap</option>
                                    <option value="Sihanoukville">Sihanoukville</option>
                                    <option value="Battambang">Battambang</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Delivery Address <span class="text-danger">*</span></label>
                                <textarea wire:model="address" rows="3" class="form-control @error('address') is-invalid @enderror"
                                    placeholder="House number, Street name, Sangkat, Khan..."></textarea>
                                @error('address')
                                    <div class="invalid-feedback fw-semibold small mt-1"><i
                                            class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Order Notes <span class="text-muted text-capitalize"
                                        style="font-size:0.7rem;">(Optional)</span></label>
                                <textarea wire:model="note" rows="2" class="form-control"
                                    placeholder="Special delivery descriptions or drop-off requests..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 💳 ផ្នែកជ្រើសរើសវិធីសាស្ត្រទូទាត់ប្រាក់ -->
                <div class="premium-card p-4 mt-4 animate__animated animate__fadeInUp">
                    <h5 class="fw-bold text-dark mb-1 premium-heading">
                        <i class="bi bi-credit-card-2-front me-2 text-primary"></i>Payment Method <span
                            class="text-danger">*</span>
                    </h5>
                    <p class="text-muted small mb-3">Choose how you want to pay for your order</p>

                    <div class="row g-3">
                        @forelse ($this->availablePaymentMethods as $method)
                            <div class="col-12 col-md-6">
                                <label class="payment-method-card w-100 m-0">
                                    {{-- ភ្ជាប់ទៅកាន់ Form ID ខាងលើដើម្បីរក្សាទិន្នន័យទូទាត់ --}}
                                    <input type="radio" wire:model.live="paymentMethodId" name="payment_method"
                                        value="{{ $method->id }}" form="checkoutForm">
                                    <div
                                        class="card-body p-3 rounded-4 d-flex align-items-center justify-content-between h-100">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="gateway-logo-badge bg-light rounded-3 p-1 border">
                                                @if ($method->logo)
                                                    <img src="{{ asset('storage/' . $method->logo) }}"
                                                        alt="{{ $method->name }}"
                                                        style="max-height: 100%; max-width: 100%;">
                                                @else
                                                    <i class="bi bi-bank2 text-secondary fs-5"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark small">{{ $method->name }}</h6>
                                                @if ($method->description)
                                                    <p class="text-muted mb-0 text-truncate"
                                                        style="font-size: 0.72rem; max-width: 120px;"
                                                        title="{{ $method->description }}">
                                                        {{ $method->description }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="radio-check-indicator ms-2">
                                            @if ($paymentMethodId == $method->id)
                                                <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                                            @else
                                                <i class="bi bi-circle text-muted opacity-50 fs-5"></i>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @empty
                            <div class="col-12">
                                <div
                                    class="alert alert-light border border-warning text-warning-emphasis small py-2 d-flex align-items-center mb-0">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i> No payment methods available
                                    right now.
                                </div>
                            </div>
                        @endforelse

                        @error('paymentMethodId')
                            <div class="col-12 mt-1">
                                <div class="invalid-feedback fw-semibold small d-block"><i
                                        class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- 📊 សេចក្ដីសង្ខេបការបញ្ជាទិញ (Order Summary Box) -->
            <div class="col-lg-5">
                <div class="premium-card p-4 position-sticky animate__animated animate__fadeIn" style="top: 24px;">
                    <h5 class="mb-3 fw-bold text-dark premium-heading">Review Your Order</h5>

                    <div class="order-items-list mb-4 style-scroll pe-1" style="max-height: 220px; overflow-y: auto;">
                        @foreach ($cartItems as $item)
                            <div class="review-item-row">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ !empty($item['image']) ? asset('storage/' . $item['image']) : 'https://placehold.co/50x50?text=No+Img' }}"
                                        class="review-product-img">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark small text-truncate"
                                            style="max-width: 170px;">{{ $item['name'] }}</h6>
                                        <small class="text-muted fw-semibold"
                                            style="font-family:'Outfit'; font-size:0.75rem;">Qty:
                                            {{ $item['quantity'] }}</small>
                                    </div>
                                </div>
                                <span
                                    class="fw-bold text-dark small summary-price-text">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between mb-2.5 small">
                        <span class="text-muted">Subtotal</span>
                        <span
                            class="fw-semibold text-dark summary-price-text">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2.5 small">
                        <span class="text-muted">Voucher Discount</span>
                        <span
                            class="text-success fw-semibold summary-price-text">-${{ number_format($discount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small">
                        <span class="text-muted">Courier Shipping</span>
                        <span
                            class="fw-semibold text-dark summary-price-text">${{ number_format($shipping, 2) }}</span>
                    </div>

                    <hr class="my-3.5" style="border-color: #e2e8f0;">

                    <div class="d-flex justify-content-between mb-4 align-items-center">
                        <span class="fw-bold text-dark fs-6">Grand Total</span>
                        <span
                            class="fw-extrabold text-danger summary-price-text fs-4">${{ number_format($total, 2) }}</span>
                    </div>

                    {{-- កែប្រែត្រង់នេះ៖ បន្ថែម form="checkoutForm" និង type="submit" ដើម្បីឱ្យ Enter Key ដំណើរការ --}}
                    <button form="checkoutForm" type="submit" wire:loading.attr="disabled"
                        class="btn btn-primary place-order-btn-premium w-100 mb-2.5 fw-bold py-2.5 text-white d-flex align-items-center justify-content-center gap-2">
                        <span wire:loading wire:target="placeOrder" class="spinner-border spinner-border-sm"></span>
                        <i wire:loading.remove wire:target="placeOrder" class="bi bi-shield-lock-fill fs-6"></i>
                        <span wire:loading.remove wire:target="placeOrder">Authorize & Place Order</span>
                        <span wire:loading wire:target="placeOrder">Validating Order Details...</span>
                    </button>

                    <a href="/cart"
                        class="btn btn-light w-100 py-2 rounded-3 text-secondary border fw-semibold small"
                        style="border-radius: 12px!important;">
                        Return to Shopping Cart
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
