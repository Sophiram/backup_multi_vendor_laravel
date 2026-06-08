@extends('admin.layouts.layout')

@section('admin_page_title', 'Order History - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-2 py-2">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">Order Management</h4>
                <p class="text-muted small mb-0">Track, monitor, and manage all customer order activities efficiently.</p>
            </div>
            <div class="text-md-end">
                <span class="text-uppercase text-muted fw-semibold font-monospace"
                    style="font-size: 0.7rem; letter-spacing: 0.05em;">
                    Live Sync: {{ now()->format('d M, H:i') }}
                </span>
            </div>
        </div>

        {{-- Stats Cards Matrices Panel --}}
        <div class="row g-3 mb-4">
            @foreach (['Completed' => [$stats['completed'], 'success', 'check-circle'], 'Processing' => [$stats['processing'], 'primary', 'loader'], 'On Delivery' => [$stats['delivery'], 'info', 'truck'], 'Cancelled' => [$stats['cancelled'], 'danger', 'x-circle']] as $label => $data)
                <div class="col-12 col-sm-6 col-xl-3">
                    <div
                        class="card border-0 shadow-sm p-3 bg-white border-start border-4 border-{{ $data[1] }} rounded-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted fw-bold text-uppercase"
                                style="font-size: 0.72rem; letter-spacing: 0.05em;">{{ $label }}</small>
                            <span class="text-{{ $data[1] }} opacity-75 d-inline-flex">
                                <i data-lucide="{{ $data[2] }}" style="width: 16px; height: 16px;"></i>
                            </span>
                        </div>
                        <h3 class="fw-bold text-dark mt-2 mb-0">{{ number_format($data[0]) }}</h3>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Data Filter Control Architecture --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <div class="row g-3 align-items-center justify-content-between">
                    <div class="col-12 col-md-8">
                        <form action="{{ route('admin.order.history') }}" method="GET" class="row g-2 align-items-center">
                            <div class="col-6 col-sm-auto">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted small px-2">From</span>
                                    <input type="date" name="from_date" class="form-control bg-light small rounded-end-3"
                                        value="{{ request('from_date') }}" onchange="this.form.submit()">
                                </div>
                            </div>
                            <div class="col-6 col-sm-auto">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted small px-2">To</span>
                                    <input type="date" name="to_date" class="form-control bg-light small rounded-end-3"
                                        value="{{ request('to_date') }}" onchange="this.form.submit()">
                                </div>
                            </div>
                            <div class="col-12 col-sm-auto d-flex gap-1.5 mt-2 mt-sm-0">
                                <button type="submit" class="btn btn-sm btn-dark rounded-3 px-3 fw-medium">Filter</button>
                                <a href="{{ route('admin.order.history') }}"
                                    class="btn btn-sm btn-light border rounded-3 px-3 fw-medium text-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                    <div class="col-12 col-md-4 text-md-end">
                        <a href="{{ route('admin.order.export', request()->all()) }}"
                            class="btn btn-success btn-sm rounded-3 w-100 w-md-auto d-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold shadow-sm">
                            <i data-lucide="file-spreadsheet" style="width: 15px; height: 15px;"></i> Export Excel
                        </a>
                    </div>
                </div>
            </div>

            {{-- Responsive Core Data Table Grid --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                    <thead class="table-light text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                        <tr>
                            <th class="ps-4 py-3 text-muted fw-bold">Order ID</th>
                            <th class="py-3 text-muted fw-bold">Placement Date</th>
                            <th class="py-3 text-muted fw-bold">Customer Profile</th>
                            <th class="py-3 text-muted fw-bold">Total Valuation</th>
                            <th class="py-3 text-muted fw-bold">Order Status</th>
                            <th class="py-3 text-muted fw-bold">Payment</th>
                            <th class="pe-4 py-3 text-end text-muted fw-bold" style="width: 130px;">Action Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold font-monospace text-secondary">#{{ $order->order_number }}</td>
                                <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span class="fw-semibold text-dark d-block">{{ $order->user->name ?? 'N/A' }}</span>
                                </td>
                                <td class="fw-bold text-dark">${{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'delivered' => 'success',
                                            'processing' => 'primary',
                                            'shipped' => 'info',
                                            'cancelled' => 'danger',
                                            'pending' => 'warning',
                                        ];
                                        $color = $statusColors[strtolower($order->status)] ?? 'secondary';
                                    @endphp
                                    <span
                                        class="badge rounded-pill px-2.5 py-1.5 text-uppercase font-monospace fw-semibold bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle"
                                        style="font-size: 0.72rem;">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $currentPaymentStatus = $order->payment->status ?? 'pending';
                                        $paymentColors = [
                                            'paid' => 'success',
                                            'pending' => 'warning',
                                            'failed' => 'danger',
                                            'refunded' => 'secondary',
                                        ];
                                        $paymentColor = $paymentColors[strtolower($currentPaymentStatus)] ?? 'warning';
                                    @endphp

                                    {{-- ត្រួតពិនិត្យលក្ខខណ្ឌ៖ បើស្ថានភាពជា paid នោះនឹងបង្ហាញតែ Badge មិនឱ្យដូរឡើយ --}}
                                    @if (strtolower($currentPaymentStatus) === 'paid')
                                        <span
                                            class="badge rounded-pill text-uppercase font-monospace fw-semibold bg-success-subtle text-success border border-success-subtle px-2"
                                            style="font-size: 0.72rem; display: inline-flex; align-items: center; justify-content: center; width: 120px; height: 28px;">
                                            Paid
                                        </span>
                                    @else
                                        <form action="{{ route('admin.order.payment.update', $order->id) }}" method="POST"
                                            onchange="this.submit()" class="m-0">
                                            @csrf
                                            <select name="payment_status"
                                                class="form-select form-select-sm rounded-pill font-monospace fw-semibold text-uppercase bg-{{ $paymentColor }}-subtle text-{{ $paymentColor }} border-0 px-2"
                                                style="font-size: 0.72rem; cursor: pointer; width: 120px;">
                                                <option value="pending"
                                                    {{ strtolower($currentPaymentStatus) === 'pending' ? 'selected' : '' }}>
                                                    Pending</option>
                                                <option value="paid"
                                                    {{ strtolower($currentPaymentStatus) === 'paid' ? 'selected' : '' }}>
                                                    Paid
                                                </option>
                                                <option value="failed"
                                                    {{ strtolower($currentPaymentStatus) === 'failed' ? 'selected' : '' }}>
                                                    Failed</option>
                                                <option value="refunded"
                                                    {{ strtolower($currentPaymentStatus) === 'refunded' ? 'selected' : '' }}>
                                                    Refunded</option>
                                            </select>
                                        </form>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end gap-1.5">
                                        <a href="{{ route('admin.order.show', $order->id) }}"
                                            class="btn btn-light btn-sm border rounded-2 text-primary p-2 d-inline-flex align-items-center"
                                            title="View Details">
                                            <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                        </a>

                                        <form id="delete-form-{{ $order->id }}"
                                            action="{{ route('admin.order.delete', $order->id) }}" method="POST"
                                            class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete({{ $order->id }})"
                                                class="btn btn-light btn-sm border rounded-2 text-danger p-2 d-inline-flex align-items-center"
                                                title="Purge Record">
                                                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted small">No structural order logs
                                    available inside system records.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-top border-light py-3">
                {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Render Structural Vector Graphics Icons Engine
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // Execution Handler for Destructive Delete Sequences
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you absolute sure?',
                text: "This process cannot be reverted via database nodes!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, purge record!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                    result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endsection
