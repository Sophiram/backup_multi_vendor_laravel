<?php

use Livewire\Volt\Component;
use App\Models\Order;

new class extends Component {
    public $order;

    public function mount(Order $order)
    {
        // ប្រើ load ដើម្បីទាញយក Relation មកជាមួយតែម្តង (Eager Loading)
        $this->order = $order->load(['items.product', 'payment']);
    }
}; ?>

<div class="container py-5">
    <div class="card p-5 shadow-lg border-0 mx-auto rounded-4" style="max-width: 800px; background-color: #fff;">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="fw-bold text-primary">RECEIPT</h1>
                <p class="text-muted mb-0">Order ID: <span class="text-dark">{{ $order->order_number }}</span></p>
            </div>
            <div class="text-end">
                <h4 class="fw-bold">QuickCart</h4>
                <p class="mb-0">Phnom Penh, Cambodia</p>
            </div>
        </div>

        <!-- Order Details -->
        <div class="row mb-5">
            <div class="col-6">
                <h6 class="text-uppercase text-muted fw-bold">Bill To:</h6>
                <p class="fw-bold mb-1">{{ auth()->user()->name }}</p>
                <p class="small text-muted">{{ auth()->user()->email }}</p>
            </div>
            <div class="col-6 text-end">
                <h6 class="text-uppercase text-muted fw-bold">Date:</h6>
                <p class="fw-bold">{{ $order->created_at->format('d M, Y') }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="table table-hover mb-4">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Product</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-end pe-3">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td class="ps-3">{{ $item->product->name ?? 'Product' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end pe-3">${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="d-flex justify-content-end mb-4">
            <div style="width: 250px;">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total:</span>
                    <span class="fw-bold">${{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between text-success">
                    <span>Payment Status:</span>
                    <span class="fw-bold text-uppercase">{{ $order->payment->status ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Buttons -->
        <div class="text-center mt-5 no-print">
            <button onclick="window.print()" class="btn btn-primary px-4 py-2 shadow-sm">
                <i class="bi bi-printer me-2"></i>Print Receipt
            </button>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 py-2 ms-2">Back to Home</a>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background-color: white !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>
