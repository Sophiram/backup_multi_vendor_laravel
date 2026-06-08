@extends('user.layouts.layout')

@section('user_page_title')
    Order #{{ $order->order_number ?? $order->id }} - User Panel
@endsection

@section('user_layout')
    <div class="container-fluid px-2 px-md-4 py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1 d-flex align-items-center" style="color: #0f172a;">
                    <i data-lucide="file-text" class="me-2 text-primary"></i>
                    Order #{{ $order->order_number ?? $order->id }}
                </h3>
                <p class="text-muted small mb-0">View complete details of your order and shipping status.</p>
            </div>
            <a href="{{ route('user.order.history') }}" class="btn btn-back btn-sm px-3 d-inline-flex align-items-center">
                <i data-lucide="arrow-left" class="icon-sm me-1"></i> Back to History
            </a>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100 summary-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                            <div class="icon-wrapper bg-primary-subtle text-primary me-3">
                                <i data-lucide="info"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Order Information</h6>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Date Placed:</span>
                            <span class="fw-medium">
                                {{ $order->created_at ? $order->created_at->format('M d, Y - h:i A') : 'N/A' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Order Status:</span>
                            <div>
                                @if (in_array(strtolower($order->status), ['completed', 'complete']))
                                    <span
                                        class="badge rounded-pill fw-semibold bg-success-subtle text-success border border-success-subtle px-3 py-1">
                                        <span class="d-inline-block rounded-circle bg-success me-1 status-dot"></span>
                                        Completed
                                    </span>
                                @elseif(strtolower($order->status) == 'cancelled')
                                    <span
                                        class="badge rounded-pill fw-semibold bg-danger-subtle text-danger border border-danger-subtle px-3 py-1">
                                        <span class="d-inline-block rounded-circle bg-danger me-1 status-dot"></span>
                                        Cancelled
                                    </span>
                                @else
                                    <span
                                        class="badge rounded-pill fw-semibold bg-warning-subtle text-warning border border-warning-subtle px-3 py-1">
                                        <span class="d-inline-block rounded-circle bg-warning me-1 status-dot"></span>
                                        {{ ucfirst($order->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Payment Status Added Here -->
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Payment Status:</span>
                            <div>
                                @if (in_array(strtolower($order->payment->status), ['paid', 'completed', 'success']))
                                    <span
                                        class="badge rounded-pill fw-semibold bg-success-subtle text-success border border-success-subtle px-3 py-1">
                                        <i data-lucide="check-circle" class="icon-xs me-1"></i> Paid
                                    </span>
                                @elseif(strtolower($order->payment->status) == 'failed')
                                    <span
                                        class="badge rounded-pill fw-semibold bg-danger-subtle text-danger border border-danger-subtle px-3 py-1">
                                        <i data-lucide="x-circle" class="icon-xs me-1"></i> Failed
                                    </span>
                                @else
                                    <span
                                        class="badge rounded-pill fw-semibold bg-warning-subtle text-warning border border-warning-subtle px-3 py-1">
                                        <i data-lucide="clock" class="icon-xs me-1"></i> Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100 summary-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                            <div class="icon-wrapper bg-info-subtle text-info me-3">
                                <i data-lucide="truck"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Shipping Details</h6>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Carrier:</span>
                            <span class="fw-medium">
                                {{ $order->shipping->shipping_company ?? ($order->shipping_method ?? 'Pending') }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Tracking Number:</span>
                            <span class="fw-bold text-dark">
                                {{ $order->shipping->tracking_number ?? 'Not Available Yet' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm items-card">
            <div class="card-header bg-white border-bottom px-4 py-3 d-flex align-items-center">
                <i data-lucide="package" class="me-2 text-secondary icon-sm"></i>
                <h6 class="mb-0 fw-bold">Items in this Order</h6>
            </div>
            <div class="table-responsive">
                <table class="table custom-table align-middle mb-0 text-nowrap" style="border-collapse: separate;">
                    <thead style="background-color: #f8fafc; border-bottom: 1px solid #edf2f7;">
                        <tr class="text-secondary"
                            style="font-size: 13px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">
                            <th class="ps-4 py-3 border-0">Product</th>
                            <th class="py-3 border-0 text-center">Price</th>
                            <th class="py-3 border-0 text-center">Qty</th>
                            <th class="pe-4 py-3 border-0 text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($order->items as $item)
                            <tr class="align-middle transition-all" style="border-bottom: 1px solid #f1f5f9;">
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark" style="font-size: 14.5px;">
                                        {{ $item->product->name ?? 'Product Unavailable' }}
                                    </div>
                                    @if (!empty($item->variation_details))
                                        <span class="text-muted small d-block">{{ $item->variation_details }}</span>
                                    @endif
                                </td>
                                <td class="py-3 text-center text-secondary" style="font-size: 14.5px;">
                                    ${{ number_format($item->price ?? 0, 2) }}
                                </td>
                                <td class="py-3 text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        {{ $item->quantity ?? 0 }}
                                    </span>
                                </td>
                                <td class="pe-4 py-3 text-end fw-bold text-dark" style="font-size: 14.5px;">
                                    ${{ number_format(($item->price ?? 0) * ($item->quantity ?? 0), 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No items found in this order.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white p-4 border-top">
                <div class="row align-items-center justify-content-between g-3">

                    <div class="col-12 col-md-auto">
                        <p class="text-muted small mb-0 d-print-none">* Thank you for your business!</p>
                    </div>

                    <div class="col-12 col-md-auto d-flex flex-wrap align-items-center gap-3">
                        <a href="{{ route('receipt', ['order' => $order->id]) }}"
                            class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                            <i data-lucide="receipt" class="icon-sm"></i> View Receipt
                        </a>

                        <!-- Pay Now Button (លោតចេញតែពេលមិនទាន់បង់ប្រាក់) -->
                        @if (
                            !in_array(strtolower($order->payment->status), ['paid', 'completed', 'success']) &&
                                strtolower($order->status) !== 'cancelled')
                            <a href="{{ route('payment.qr', $order->id) }}"
                                class="btn btn-success px-4 d-inline-flex align-items-center gap-2 shadow-sm"
                                style="border-radius: 8px;">
                                <i data-lucide="qr-code" class="icon-sm"></i> Pay Now
                            </a>
                        @endif

                        <div class="rounded-3 bg-light p-3 d-flex align-items-center gap-3 ms-md-2 mt-3 mt-md-0">
                            <span class="text-secondary fw-semibold">Grand Total:</span>
                            <span class="fs-5 fw-bold text-primary">
                                ${{ number_format($order->total_amount ?? 0, 2) }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        /* Card Radii & Shadows */
        .summary-card,
        .items-card {
            border-radius: 20px;
            overflow: hidden;
            background: #ffffff;
        }

        /* Icon Wrappers for visual pop */
        .icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-wrapper i {
            width: 20px;
            height: 20px;
        }

        /* Table Row Hover */
        .custom-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .custom-table tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Status Dot & Icons */
        .status-dot {
            width: 6px;
            height: 6px;
            vertical-align: middle;
        }

        .icon-xs {
            width: 14px;
            height: 14px;
            vertical-align: middle;
        }

        /* Responsive Back Button */
        .btn-back {
            background: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-back:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }

        /* Icon Sizing */
        .icon-sm {
            width: 16px;
            height: 16px;
        }

        /* Mobile Scrollbar */
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
@endsection
