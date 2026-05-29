@extends('layouts.app')

@section('content')
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

            @if ($cart && $cart->items()->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Product</th>
                                    <th class="text-center py-3 px-4 font-semibold text-gray-700">Price</th>
                                    <th class="text-center py-3 px-4 font-semibold text-gray-700">Quantity</th>
                                    <th class="text-right py-3 px-4 font-semibold text-gray-700">Total</th>
                                    <th class="text-center py-3 px-4 font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cart->items as $item)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-4">
                                                @if ($item->product->images()->first())
                                                    <img src="{{ Storage::url($item->product->images()->first()->image_path) }}"
                                                        alt="{{ $item->product->product_name }}"
                                                        class="w-16 h-16 object-cover rounded">
                                                @else
                                                    <img src="https://via.placeholder.com/100" alt="Product"
                                                        class="w-16 h-16 object-cover rounded">
                                                @endif
                                                <div>
                                                    <a href="{{ route('products.show', $item->product->id) }}"
                                                        class="font-semibold text-gray-900 hover:text-indigo-600">
                                                        {{ $item->product->product_name }}
                                                    </a>
                                                    <p class="text-sm text-gray-600">SKU: {{ $item->product->sku }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-4 px-4">${{ number_format($item->price, 2) }}</td>
                                        <td class="text-center py-4 px-4">
                                            <form action="{{ route('cart.update', $item->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="number" name="quantity" value="{{ $item->quantity }}"
                                                    min="1"
                                                    class="w-16 px-2 py-1 border border-gray-300 rounded text-center">
                                                <button type="submit"
                                                    class="ml-2 text-indigo-600 text-sm hover:text-indigo-700">Update</button>
                                            </form>
                                        </td>
                                        <td class="text-right py-4 px-4 font-semibold text-gray-900">
                                            ${{ number_format($item->price * $item->quantity, 2) }}
                                        </td>
                                        <td class="text-center py-4 px-4">
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-700">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Cart Summary -->
                    <div class="bg-white rounded-lg shadow-sm p-6 h-fit">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>

                        @php
                            $subtotal = $cart->items->sum(function ($item) {
                                return $item->price * $item->quantity;
                            });
                            $tax = $subtotal * 0.1; // Assuming 10% tax
                            $total = $subtotal + $tax;
                        @endphp

                        <div class="space-y-3 border-b pb-4 mb-4">
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
                        </div>

                        <div class="flex justify-between text-lg font-bold text-gray-900 mb-6">
                            <span>Total</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>

                        <a href="{{ route('checkout.index') }}"
                            class="block w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition text-center mb-3">
                            Proceed to Checkout
                        </a>

                        <form action="{{ route('cart.clear') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="block w-full text-indigo-600 px-4 py-2 rounded-md border border-indigo-600 hover:bg-indigo-50 transition">
                                Clear Cart
                            </button>
                        </form>

                        <a href="{{ route('products.index') }}"
                            class="block w-full text-gray-600 px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-50 transition text-center mt-3">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Your cart is empty</h2>
                    <p class="text-gray-600 mb-6">Start shopping to add items to your cart</p>
                    <a href="{{ route('products.index') }}"
                        class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition">
                        Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
