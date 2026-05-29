<?php

use Livewire\Volt\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

new class extends Component {
    // ព័ត៌មានអតិថិជន និងអាសយដ្ឋាន
    public $fullName = '';
    public $phone = '';
    public $email = '';
    public $address = '';
    public $city = 'Phnom Penh';
    public $note = '';

    // ទិន្នន័យហិរញ្ញវត្ថុទាញពី Session
    public $cartItems = [];
    public $paymentMethod = 'aba';
    public $shipping = 0.0;
    public $discount = 0.0;
    public $subtotal = 0.0;
    public $total = 0.0;

    // មុខងារហៅមកដំណើរការមុនគេពេលបើកទំព័រ (Mount)
    public function mount()
    {
        $this->cartItems = session()->get('cart', []);

        // ប្រសិនបើគ្មានទំនិញក្នុងកន្ត្រកទេ ឱ្យរុញទៅទំពើរកន្ត្រកវិញ
        if (count($this->cartItems) === 0) {
            return redirect()->route('cart');
        }

        // ចាប់យកទិន្នន័យគណនាដែលបញ្ជូនមកពី Cart Session
        $this->paymentMethod = session()->get('selected_payment_method', 'aba');
        $this->shipping = session()->get('cart_shipping', 5.0);
        $this->discount = session()->get('cart_discount', 0.0);

        $this->subtotal = collect($this->cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        $this->total = $this->subtotal + $this->shipping - $this->discount;
        if ($this->total < 0) {
            $this->total = 0;
        }

        // បំពេញព័ត៌មានលម្អិតស្វ័យប្រវត្តបើអតិថិជនបាន Login រួចរាល់
        if (auth()->check()) {
            $this->fullName = auth()->user()->name;
            $this->email = auth()->user()->email;
        }
    }

    // ដំណើរការរក្សាទុកការបញ្ជាទិញ (Place Order)
    public function placeOrder()
    {
        $this->validate([
            'fullName' => 'required|string|min:3',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
            'address' => 'required|string',
            'city' => 'required',
        ]);

        // ១. ចាប់យកអថេរ $order ដោយផ្ទាល់ពីការ Return របស់ DB::transaction
        $order = DB::transaction(function () {
            $newOrder = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'total_amount' => $this->total,
                'status' => 'pending',
                'shipping_address' => $this->address . ', ' . $this->city,
                'payment_method' => $this->paymentMethod,
            ]);

            foreach ($this->cartItems as $productId => $item) {
                OrderItem::create([
                    'order_id' => $newOrder->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                ]);
            }

            Payment::create([
                'order_id'       => $newOrder->id,
                'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
                'amount'         => $this->total,
                'status'         => 'pending',
                'payment_method' => $this->paymentMethod,
            ]);

            return $newOrder; // ២. Return តម្លៃ Object នេះចេញទៅក្រៅ
        });

        // ឥឡូវនេះ $order ប្រាកដជាមានតម្លៃហើយ
        session()->forget(['cart', 'selected_payment_method', 'cart_shipping', 'cart_discount', 'applied_coupon']);

        session()->flash('success', 'Your order has been placed successfully!');

        return redirect()->route('receipt', ['order' => $order->id]);
    }
}; ?>

<div class="checkout-wrapper"
    style="background-color: #f8f9fa; min-height: 100vh; padding: 40px 0; font-family: 'Inter', sans-serif;">
    <div class="container">
        <div class="row g-4">

            <!-- 📝 ផ្នែកខាងឆ្វេង៖ Form បំពេញព័ត៌មានដឹកជញ្ជូន -->
            <div class="col-lg-7">
                <div class="card p-4 border-0 shadow-sm rounded-3 bg-white">
                    <h5 class="fw-bold text-dark mb-4">
                        <i class="bi bi-truck me-2 text-primary"></i>Shipping Details
                    </h5>

                    <form wire:submit.prevent="placeOrder">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-secondary">Full Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" wire:model="fullName"
                                    class="form-control shadow-none @error('fullName') is-invalid @enderror"
                                    placeholder="John Doe">
                                @error('fullName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Phone Number <span
                                        class="text-danger">*</span></label>
                                <input type="text" wire:model="phone"
                                    class="form-control shadow-none @error('phone') is-invalid @enderror"
                                    placeholder="012345678">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Email Address
                                    (Optional)</label>
                                <input type="email" wire:model="email" class="form-control shadow-none"
                                    placeholder="john@example.com">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">City / Province <span
                                        class="text-danger">*</span></label>
                                <select wire:model="city" class="form-select shadow-none">
                                    <option value="Phnom Penh">Phnom Penh</option>
                                    <option value="Siem Reap">Siem Reap</option>
                                    <option value="Sihanoukville">Sihanoukville</option>
                                    <option value="Battambang">Battambang</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-secondary">Delivery Address <span
                                        class="text-danger">*</span></label>
                                <textarea wire:model="address" rows="3" class="form-control shadow-none @error('address') is-invalid @enderror"
                                    placeholder="House number, Street name, Group..."></textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-secondary">Order Notes
                                    (Optional)</label>
                                <textarea wire:model="note" rows="2" class="form-control shadow-none"
                                    placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 💳 ផ្នែកបង្ហាញវិធីសាស្ត្រទូទាត់ដែលបានជ្រើសរើស -->
                <div class="card p-4 border-0 shadow-sm rounded-3 bg-white mt-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-wallet2 me-2 text-primary"></i>Selected Payment Method
                    </h5>
                    <div class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            <div>
                                <span class="fw-bold text-uppercase text-dark">{{ $paymentMethod }} Pay</span>
                                <p class="text-muted small mb-0">You chose this option from shopping cart.</p>
                            </div>
                        </div>
                        <a href="/cart"
                            class="btn btn-sm btn-outline-primary rounded-2 px-3 text-decoration-none">Change</a>
                    </div>
                </div>
            </div>

            <!-- 📊 ផ្នែកខាងស្តាំ៖ សេចក្ដីសង្ខេបការបញ្ជាទិញ (Your Order) -->
            <div class="col-lg-5">
                <div class="card p-4 shadow-sm border-0 bg-white rounded-3 position-sticky" style="top: 20px;">
                    <h5 class="mb-4 fw-bold text-dark">Your Order</h5>

                    <!-- បញ្ជីទំនិញសង្ខេប -->
                    <div class="order-items-list mb-4 style-scroll" style="max-height: 240px; overflow-y: auto;">
                        @foreach ($cartItems as $item)
                            <div
                                class="d-flex align-items-center justify-content-between py-2 border-bottom border-light">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ !empty($item['image']) ? asset('storage/' . $item['image']) : 'https://placehold.co/50x50?text=No+Img' }}"
                                        class="rounded-3 object-fit-cover" style="width: 50px; height: 50px;">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark small text-truncate"
                                            style="max-width: 180px;">{{ $item['name'] }}</h6>
                                        <small class="text-muted">Qty: {{ $item['quantity'] }}</small>
                                    </div>
                                </div>
                                <span
                                    class="fw-semibold text-dark small">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- គណនាប្រាក់ -->
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-semibold text-dark">${{ number_format($this->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Discount</span>
                        <span class="text-success fw-semibold">-${{ number_format($this->discount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small">
                        <span class="text-muted">Shipping</span>
                        <span class="fw-semibold text-dark">${{ number_format($this->shipping, 2) }}</span>
                    </div>
                    <hr class="my-3" style="border-color: #e2e8f0;">
                    <div class="d-flex justify-content-between mb-4 fs-5">
                        <span class="fw-bold text-dark">Total</span>
                        <span class="fw-bold text-danger">${{ number_format($this->total, 2) }}</span>
                    </div>

                    <!-- ប៊ូតុងបញ្ជាក់ការកុម្ម៉ង់ -->
                    <button wire:click="placeOrder" wire:loading.attr="disabled" type="button"
                        class="btn btn-primary place-order-btn w-100 mb-3 fw-bold py-2.5 rounded-3 text-white">
                        <span wire:loading wire:target="placeOrder"
                            class="spinner-border spinner-border-sm me-2"></span>
                        <i class="bi bi-shield-lock me-2"></i>
                        <span wire:loading.remove wire:target="placeOrder">Place Order Now</span>
                        <span wire:loading wire:target="placeOrder">Processing...</span>
                    </button>

                    <a href="/cart"
                        class="btn btn-light w-100 py-2 rounded-3 text-secondary border fw-medium small">
                        Back to Shopping Cart
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .place-order-btn {
        background: linear-gradient(135deg, #4f46e5, #4338ca);
        border: none;
        transition: all 0.2s;
    }

    .place-order-btn:hover {
        transform: translateY(-2px);
        background: linear-gradient(135deg, #4338ca, #3730a3);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .style-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .style-scroll::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 4px;
    }
</style>
