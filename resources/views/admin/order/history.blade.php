@extends('admin.layouts.layout')

@section('admin_page_title', 'Order History - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4 py-4">
        {{-- Header & Stats --}}
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h3 class="fw-bolder text-dark mb-0">Order Management</h3>
                <p class="text-muted small">Track and manage all customer orders efficiently.</p>
            </div>
            <div class="text-end">
                <span class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Updated:
                    {{ now()->format('d M, H:i') }}</span>
            </div>
        </div>

        <div class="row g-4 mb-5">
            @foreach (['Completed' => [$stats['completed'], 'success'], 'Processing' => [$stats['processing'], 'primary'], 'On Delivery' => [$stats['delivery'], 'info'], 'Cancelled' => [$stats['cancelled'], 'danger']] as $label => $data)
                <div class="col-12 col-sm-6 col-xl-3">
                    <div
                        class="card border-0 shadow-sm p-4 rounded-4 bg-white border-start border-4 border-{{ $data[1] }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted fw-semibold text-uppercase">{{ $label }}</small>
                            <i class="fa-solid fa-chart-line text-{{ $data[1] }} opacity-50"></i>
                        </div>
                        <h2 class="fw-bolder mt-2 mb-0">{{ number_format($data[0]) }}</h2>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Filter & Export Section --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <form action="{{ route('admin.order.history') }}" method="GET" class="row g-2">
                            <div class="col-auto">
                                <input type="date" name="from_date" class="form-control form-control-sm"
                                    value="{{ request('from_date') }}" onchange="this.form.submit()">
                            </div>
                            <div class="col-auto">
                                <input type="date" name="to_date" class="form-control form-control-sm"
                                    value="{{ request('to_date') }}" onchange="this.form.submit()">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-sm btn-dark">Filter</button>
                                <a href="{{ route('admin.order.history') }}"
                                    class="btn btn-sm btn-outline-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('admin.order.export', request()->all()) }}" class="btn btn-sm btn-success">
                            <i class="fa-solid fa-file-excel me-1"></i> Export Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Table --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <th class="ps-4 py-3">ORDER ID</th>
                            <th>DATE</th>
                            <th>CUSTOMER</th>
                            <th>TOTAL</th>
                            <th>STATUS</th>
                            <th>PAYMENT</th>
                            <th class="text-end pe-4">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold">#{{ $order->order_number }}</td>
                                <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $order->user->name ?? 'N/A' }}</div>
                                </td>
                                <td class="fw-bold text-dark">${{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'completed' => 'success',
                                            'processing' => 'primary',
                                            'delivery' => 'info',
                                            'cancelled' => 'danger',
                                        ];
                                        $color = $statusColors[strtolower($order->status)] ?? 'secondary';
                                    @endphp
                                    <span
                                        class="badge bg-{{ $color }}-subtle text-{{ $color }} rounded-pill px-3 py-2">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>

                                <td>
                                    @php
                                        $paymentColor = $order->payment_status == 'paid' ? 'success' : 'warning';
                                    @endphp
                                    <span
                                        class="badge bg-{{ $paymentColor }}-subtle text-{{ $paymentColor }} rounded-pill px-3 py-2">
                                        {{ ucfirst($order->payment_status ?? 'Unpaid') }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.order.show', $order->id) }}"
                                        class="btn btn-sm btn-light rounded-circle shadow-sm">
                                        <i class="fa-solid fa-eye text-primary"></i>
                                    </a>

                                    <form id="delete-form-{{ $order->id }}"
                                        action="{{ route('admin.order.delete', $order->id) }}" method="POST"
                                        style="display: none;">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" onclick="confirmDelete({{ $order->id }})"
                                        class="btn btn-sm btn-light rounded-circle shadow-sm ms-2">
                                        <i class="fa-solid fa-trash text-danger"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endsection
