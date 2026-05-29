@extends('layouts.app')

@section('content')
    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

            @if ($cart && $cart->items()->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Checkout Form -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping Information</h2>

                        <form action="{{ route('checkout.process') }}" method="POST" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Shipping Address *</label>
                                <textarea name="shipping_address" required placeholder="Enter your full shipping address..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    rows="4">{{ auth()->user()->address ?? '' }}</textarea>
                                @error('shipping_address')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <input type="checkbox" name="billing_same" checked> Billing address is same as shipping
                                </label>
                            </div>

                            <div id="billingAddressDiv" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Billing Address</label>
                                <textarea name="billing_address" placeholder="Enter your billing address..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    rows="4"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                                <select name="payment_method" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select payment method</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="debit_card">Debit Card</option>
                                    <option value="paypal">PayPal</option>
                                </select>
                                @error('payment_method')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                                Place Order
                            </button>
                        </form>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Review</h2>

                        <div class="space-y-4 mb-6 pb-6 border-b">
                            @php
                                $subtotal = $cart->items->sum(function ($item) {
                                    return $item->price * $item->quantity;
                                });
                                $tax = $subtotal * 0.1;
                                $total = $subtotal + $tax;
                            @endphp

                            @foreach ($cart->items as $item)
                                <div class="flex items-center gap-4">
                                    @if ($item->product->images()->first())
                                        <img src="{{ Storage::url($item->product->images()->first()->image_path) }}"
                                            alt="{{ $item->product->product_name }}" class="w-12 h-12 object-cover rounded">
                                    @else
                                        <img src="https://via.placeholder.com/100" alt="Product"
                                            class="w-12 h-12 object-cover rounded">
                                    @endif
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">{{ $item->product->product_name }}</p>
                                        <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                    </div>
                                    <p class="font-semibold text-gray-900">
                                        ${{ number_format($item->price * $item->quantity, 2) }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between text-gray-700">
                                <span>Subtotal</span>
                                <span>${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-700">
                                <span>Tax (10%)</span>
                                <span>${{ number_format($tax, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-700">
                                <span>Shipping</span>
                                <span>Free</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-gray-900 border-t pt-3">
                                <span>Total</span>
                                <span>${{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Your cart is empty</h2>
                    <a href="{{ route('products.index') }}"
                        class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition">
                        Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.querySelector('input[name="billing_same"]').addEventListener('change', function() {
            const billingDiv = document.getElementById('billingAddressDiv');
            billingDiv.style.display = this.checked ? 'none' : 'block';
        });
    </script>
@endsection
