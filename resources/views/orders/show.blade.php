@extends('layouts.app')

@section('content')
    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg shadow-sm font-medium text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg shadow-sm font-medium text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-6">
                <a href="{{ route('order.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium flex items-center gap-1">
                    ← Back to Orders
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-8 mb-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6 pb-6 border-b">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">#{{ $order->order_number }}</h1>
                        <p class="text-sm text-gray-500 mt-1">Order Date: {{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                    <span class="inline-flex px-3.5 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider
                        @if ($order->status === 'pending') bg-yellow-50 text-yellow-800 border border-yellow-200
                        @elseif($order->status === 'processing') bg-blue-50 text-blue-800 border border-blue-200
                        @elseif($order->status === 'shipped') bg-purple-50 text-purple-800 border border-purple-200
                        @elseif($order->status === 'delivered') bg-green-50 text-green-800 border border-green-200
                        @else bg-red-50 text-red-800 border border-red-200 @endif">
                        {{ $order->status }}
                    </span>
                </div>

                <div class="mb-8">
                    <h2 class="text-base font-bold text-gray-900 mb-4">Order Items</h2>
                    <div class="space-y-3">
                        @foreach ($order->items as $item)
                            <div class="flex items-center justify-between p-4 bg-gray-50/70 rounded-xl border border-gray-100">
                                <div class="flex items-center gap-4 flex-1">
                                    @if ($item->product && $item->product->images->first())
                                        <img src="{{ Storage::url($item->product->images->first()->image_path) }}"
                                             alt="{{ $item->product->product_name }}" class="w-16 h-16 object-cover rounded-lg border bg-white">
                                    @else
                                        <img src="https://via.placeholder.com/100" alt="Product"
                                             class="w-16 h-16 object-cover rounded-lg border bg-white">
                                    @endif
                                    <div>
                                        @if($item->product)
                                            <a href="{{ route('products.show', $item->product->id) }}"
                                                class="font-semibold text-gray-900 hover:text-indigo-600 transition text-sm">
                                                {{ $item->product->product_name }}
                                            </a>
                                            <p class="text-xs text-gray-500 mt-0.5">SKU: {{ $item->product->sku }}</p>
                                        @else
                                            <span class="text-sm text-gray-400 italic">Product no longer available</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                    <p class="font-bold text-gray-900 text-sm mt-0.5">${{ number_format($item->total, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-6 mb-8">
                    <div class="max-w-xs ml-auto space-y-2.5 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal:</span>
                            <span class="font-medium text-gray-900">${{ number_format($order->total_amount * 0.9, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax (10%):</span>
                            <span class="font-medium text-gray-900">${{ number_format($order->total_amount * 0.1, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-gray-900 border-t border-dashed pt-2.5 mt-2">
                            <span>Total:</span>
                            <span>${{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 pb-6 border-t border-gray-100 pt-6">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 mb-2">Shipping Address</h3>
                        <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line bg-gray-50/50 p-3 rounded-lg border border-gray-100">{{ $order->shipping_address }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 mb-2">Billing Address</h3>
                        <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line bg-gray-50/50 p-3 rounded-lg border border-gray-100">{{ $order->billing_address }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-100 pt-6">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 mb-2">Payment Method</h3>
                        <p class="text-gray-600 text-sm bg-gray-50/50 p-3 rounded-lg border border-gray-100 inline-block">
                            {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 mb-2">Status Timeline</h3>
                        <div class="text-gray-600 text-sm bg-gray-50/50 p-3 rounded-lg border border-gray-100 space-y-1">
                            @if ($order->shipped_at)
                                <div><span class="font-medium">Shipped:</span> {{ \Carbon\Carbon::parse($order->shipped_at)->format('M d, Y') }}</div>
                            @endif
                            @if ($order->delivered_at)
                                <div><span class="font-medium">Delivered:</span> {{ \Carbon\Carbon::parse($order->delivered_at)->format('M d, Y') }}</div>
                            @endif
                            @if (!$order->shipped_at && !$order->delivered_at)
                                <span class="text-gray-400 italic text-xs">No updates yet</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if (in_array($order->status, ['pending', 'processing']))
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 flex justify-end">
                    <form action="{{ route('order.cancel', $order->id) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to cancel this order?')">
                        @csrf
                        <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 px-5 py-2.5 rounded-lg font-semibold text-sm transition-colors">
                            Cancel Order
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
