<?php

use Livewire\Volt\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public function with()
    {
        // ទាញយក Orders ទាំងអស់របស់អ្នកប្រើដែលបាន Login ដោយរៀបតាមលំដាប់ថ្មីទៅចាស់
        return [
            'orders' => Order::where('user_id', Auth::id())
                ->latest()
                ->paginate(10)
        ];
    }
}; ?>

<div class="container py-5">
    <h4 class="fw-bold mb-4">My Order History</h4>
    <div class="card shadow-sm border-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td class="fw-bold">{{ $order->order_number }}</td>
                    <td>{{ $order->created_at->format('d M, Y') }}</td>
                    <td>${{ number_format($order->total_amount, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : 'warning' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('receipt', $order->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-3">{{ $orders->links() }}</div>
    </div>
</div>
