@extends('vendor.layouts.layout')

@section('vendor_page_title')
    Order History - Vendor Panel
@endsection

@section('vendor_layout')
    <!-- Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8 col-12 mb-3 mb-md-0">
            <h3 class="fw-bolder text-dark mb-1 d-flex align-items-center gap-2">
                <i data-feather="shopping-bag" style="width: 24px; height: 24px;" class="text-primary"></i>
                Order History
            </h3>
            <p class="text-muted small mb-0">Manage and track all customer orders placed across your stores.</p>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                <!-- Card Header & Search -->
                <div
                    class="card-header bg-white border-bottom py-4 d-flex align-items-center justify-content-between flex-column flex-md-row gap-3">
                    <h5 class="card-title mb-0 fw-bold text-dark fs-6 text-uppercase" style="letter-spacing: 0.5px;">
                        Recent Orders
                    </h5>
                    <div class="w-100" style="max-width: 300px;">
                        <form action="{{ route('vendor.orders.history') }}" method="GET" class="m-0">
                            <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-light border-0 text-muted px-3">
                                    <i data-feather="search" style="width: 16px; height: 16px;"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-0 bg-light py-2 shadow-none"
                                    value="{{ request('search') }}" placeholder="Search Order ID...">
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Responsive Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap custom-table">
                            <thead class="bg-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                                <tr>
                                    <th class="ps-4 py-3 border-bottom-0 fw-semibold">Order ID</th>
                                    <th class="py-3 border-bottom-0 fw-semibold">Customer</th>
                                    <th class="py-3 border-bottom-0 fw-semibold">Store</th>
                                    <th class="py-3 border-bottom-0 fw-semibold">Shipping</th>
                                    <th class="py-3 border-bottom-0 fw-semibold text-end">Sales</th>
                                    <th class="py-3 border-bottom-0 fw-semibold text-end">Net Earnings</th>
                                    <th class="py-3 border-bottom-0 fw-semibold text-center">Status</th>
                                    <th class="py-3 border-bottom-0 fw-semibold">Date</th>
                                    <th class="pe-4 py-3 border-bottom-0 fw-semibold text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody class="small border-top-0">
                                @forelse ($orders as $order)
                                    @php
                                        $vendorSales = $order->items->sum(fn($item) => $item->price * $item->quantity);
                                        $vendorNet = $order->items->sum('vendor_net_amount');

                                        $stores = $order->items
                                            ->map(fn($item) => $item->product->store->store_name ?? null)
                                            ->filter()
                                            ->unique();
                                    @endphp
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <span class="fw-bold text-primary bg-soft-primary px-2 py-1 rounded">
                                                #{{ $order->order_number }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex flex-column">
                                                <span
                                                    class="fw-bold text-dark">{{ $order->user->name ?? 'Unknown Customer' }}</span>
                                                <span class="text-muted"
                                                    style="font-size: 12px;">{{ $order->user->email ?? 'No email provided' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex flex-wrap gap-1"
                                                style="max-width: 180px; white-space: normal;">
                                                @foreach ($stores as $storeName)
                                                    <span class="badge bg-light text-dark border fw-medium text-truncate"
                                                        style="max-width: 100%;" title="{{ $storeName }}">
                                                        {{ $storeName }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            @if ($order->shipping)
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="fw-semibold text-dark">{{ $order->shipping->shipping_company }}</span>
                                                    <span class="text-primary" style="font-size: 12px;">
                                                        <i data-feather="package" style="width: 12px; height: 12px;"
                                                            class="me-1"></i>{{ $order->shipping->tracking_number }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="badge bg-light text-muted border">Unshipped</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-end fw-bold text-dark font-monospace">
                                            ${{ number_format($vendorSales, 2) }}
                                        </td>
                                        <td class="py-3 text-end fw-bold text-success font-monospace">
                                            ${{ number_format($vendorNet, 2) }}
                                        </td>
                                        <td class="py-3 text-center">
                                            <span
                                                class="badge rounded-pill fw-medium px-3 py-2 w-100 status-badge-{{ strtolower($order->status) }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-muted fw-medium">
                                            {{ $order->created_at ? $order->created_at->format('d M, Y') : 'N/A' }}
                                        </td>
                                        <td class="pe-4 py-3 text-end">
                                            <div class="d-flex justify-content-end align-items-center gap-2">
                                                <a href="{{ route('vendor.ordershow', $order->id) }}"
                                                    class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center p-2 transition-all"
                                                    title="View Details" style="border-radius: 8px;">
                                                    <i data-feather="eye" style="width: 16px; height: 16px;"></i>
                                                </a>

                                                <form action="{{ route('vendor.orders.updateStatus', $order->id) }}"
                                                    method="POST" class="m-0">
                                                    @csrf
                                                    <select name="status"
                                                        class="form-select form-select-sm shadow-none fw-medium border-secondary-subtle"
                                                        onchange="this.form.submit()"
                                                        style="border-radius: 8px; cursor: pointer; min-width: 115px;"
                                                        {{ in_array(strtolower($order->status), ['completed', 'cancelled']) ? 'disabled' : '' }}>

                                                        <option value="pending"
                                                            {{ $order->status == 'pending' ? 'selected' : '' }}>Pending
                                                        </option>
                                                        <option value="processing"
                                                            {{ $order->status == 'processing' ? 'selected' : '' }}>
                                                            Processing</option>
                                                        <option value="shipped"
                                                            {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped
                                                        </option>
                                                        <option value="completed"
                                                            {{ $order->status == 'completed' ? 'selected' : '' }}>Completed
                                                        </option>
                                                        <option value="cancelled"
                                                            {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled
                                                        </option>
                                                    </select>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <div
                                                class="d-flex flex-column align-items-center justify-content-center opacity-50">
                                                <i data-feather="inbox" style="width: 48px; height: 48px;"
                                                    class="mb-3 text-muted"></i>
                                                <p class="text-muted fw-medium fs-6 mb-0">No orders found yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination Footer -->
                <div class="card-footer bg-white border-top py-3 px-4">
                    <div
                        class="d-flex align-items-center justify-content-between flex-column flex-md-row gap-3 small text-muted">
                        <div class="fw-medium">
                            Showing <span class="text-dark fw-bold">{{ $orders->firstItem() ?? 0 }}</span>
                            to <span class="text-dark fw-bold">{{ $orders->lastItem() ?? 0 }}</span>
                            of <span class="text-dark fw-bold">{{ $orders->total() ?? 0 }}</span> entries
                        </div>
                        <div class="pagination-wrapper">
                            {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        /* Table Enhancements */
        .custom-table tbody tr {
            transition: all 0.2s ease-in-out;
        }

        /* .custom-table tbody tr:hover {
                    background-color: #f8f9fa;
                    transform: translateY(-1px);
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
                } */

        /* Status Badges */
        .status-badge-completed {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .status-badge-pending {
            background-color: #fff3e0;
            color: #ef6c00;
            border: 1px solid #ffe0b2;
        }

        .status-badge-processing {
            background-color: #e0f7fa;
            color: #00838f;
            border: 1px solid #b2ebf2;
        }

        .status-badge-shipped {
            background-color: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
        }

        .status-badge-cancelled {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        /* Generic Soft Backgrounds */
        .bg-soft-primary {
            background-color: #e3f2fd !important;
        }

        /* Pagination Reset for Bootstrap 5 */
        .pagination-wrapper .pagination {
            margin-bottom: 0;
        }

        .pagination-wrapper .page-link {
            border-radius: 6px;
            margin: 0 2px;
            border: 1px solid #dee2e6;
            color: #495057;
        }

        .pagination-wrapper .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }
    </style>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Re-initialize feather icons if they aren't globally bound
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            // SweetAlert2 Notifications
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#0d6efd',
                    customClass: {
                        popup: 'rounded-4'
                    },
                    timer: 3000,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#dc3545',
                    customClass: {
                        popup: 'rounded-4'
                    }
                });
            @endif
        });
    </script>
@endsection
