@extends('layouts.app')

@section('content')
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-md shadow-sm flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 height-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-green-800">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-md shadow-sm flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 height-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-red-800">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
            </div>

            @if ($orders->count() > 0)
                <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-4 text-sm font-semibold text-gray-700">Order #</th>
                                <th class="px-6 py-4 text-sm font-semibold text-gray-700">Date</th>
                                <th class="px-6 py-4 text-sm font-semibold text-gray-700">Items</th>
                                <th class="px-6 py-4 text-sm font-semibold text-gray-700">Total</th>
                                <th class="px-6 py-4 text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-4 text-sm font-semibold text-gray-700 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($orders as $order)
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('order.show', $order->id) }}"
                                            class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm transition">
                                            #{{ $order->order_number }}
                                        </a>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $order->created_at->format('M d, Y') }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                    </td>

                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                        ${{ number_format($order->total_amount, 2) }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider
                                        @if ($order->status === 'pending') bg-yellow-50 text-yellow-800 border border-yellow-200
                                        @elseif($order->status === 'processing') bg-blue-50 text-blue-800 border border-blue-200
                                        @elseif($order->status === 'shipped') bg-purple-50 text-purple-800 border border-purple-200
                                        @elseif($order->status === 'delivered') bg-green-50 text-green-800 border border-green-200
                                        @else bg-red-50 text-red-800 border border-red-200 @endif">
                                            {{ $order->status }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-right">
                                        <div class="flex items-center justify-end space-x-3">
                                            <a href="{{ route('order.show', $order->id) }}"
                                                class="text-indigo-600 hover:text-indigo-700 font-medium transition">
                                                View Details
                                            </a>

                                            @if (in_array($order->status, ['pending', 'processing']))
                                                <form action="{{ route('order.cancel', $order->id) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to cancel this order?');" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-700 font-medium transition">
                                                        Cancel
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-12 text-center max-w-xl mx-auto border border-gray-100 mt-10">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">No orders yet</h2>
                    <p class="text-gray-500 mb-6 text-sm">Looks like you haven't made your choice yet. Start shopping to place your first order!</p>
                    <a href="{{ route('products.index') }}"
                        class="inline-block bg-indigo-600 text-white font-semibold px-6 py-2.5 rounded-md hover:bg-indigo-700 shadow-sm transition">
                        Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
