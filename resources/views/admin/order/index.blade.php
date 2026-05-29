@extends('admin.layouts.layout')

@section('admin_page_title', 'Order Management - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4 py-4">
        <div class="row g-4 mb-5">
            @foreach (['Completed' => [$stats['completed'], 'success'], 'Processing' => [$stats['processing'], 'primary'], 'On Delivery' => [$stats['delivery'], 'info'], 'Cancelled' => [$stats['cancelled'], 'danger']] as $label => $data)
                <div class="col-12 col-sm-6 col-xl-3">
                    <div
                        class="card border-0 shadow-sm p-4 rounded-4 bg-white border-start border-4 border-{{ $data[1] }}">
                        <small class="text-muted fw-semibold text-uppercase">{{ $label }}</small>
                        <h2 class="fw-bolder mt-2 mb-0">{{ number_format($data[0]) }}</h2>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold">Order Management</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted" style="font-size: 0.75rem;">
                            <th class="ps-4">ORDER ID</th>
                            <th>CUSTOMER</th>
                            <th>TOTAL</th>
                            <th>STATUS</th>
                            <th class="text-end pe-4">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold">#{{ $order->order_number }}</td>
                                <td>{{ $order->user->name ?? 'N/A' }}</td>
                                <td class="fw-bold">${{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <form action="{{ route('admin.order.update', $order->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="form-select form-select-sm"
                                            onchange="this.form.submit()">
                                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="processing"
                                                {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>
                                                Shipped</option>
                                            <option value="delivered"
                                                {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            <option value="cancelled"
                                                {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.order.show', $order->id) }}"
                                        class="btn btn-sm btn-light rounded-circle shadow-sm">
                                        <i class="fa-solid fa-eye text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
