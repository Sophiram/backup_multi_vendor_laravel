@extends('user.layouts.layout')

@section('user_page_title')
    History - User Panel
@endsection

@section('user_layout')
    <div class="container-fluid px-2 px-md-4 py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1 d-flex align-items-center" style="color: #0f172a;">
                    <i data-lucide="shopping-bag" class="me-2 text-primary"></i>
                    Order History
                </h3>
                <p class="text-muted small mb-0">Review and track all your past and current purchases.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius: 20px; background: #ffffff; overflow: hidden;">
                    <div class="table-responsive">
                        <table class="table custom-table align-middle mb-0 text-nowrap" style="border-collapse: separate;">
                            <thead style="background-color: #f8fafc; border-bottom: 1px solid #edf2f7;">
                                <tr class="text-secondary"
                                    style="font-size: 13px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">
                                    <th class="ps-4 py-3 border-0">Order ID</th>
                                    <th class="py-3 border-0">Date</th>
                                    <th class="py-3 border-0">Order Status</th>
                                    <th class="py-3 border-0">Payment Status</th>
                                    <th class="py-3 border-0">Total</th>
                                    <th class="pe-4 py-3 border-0 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr class="align-middle transition-all" style="border-bottom: 1px solid #f1f5f9;"
                                        id="order-row-{{ $order->id }}">
                                        <td class="ps-4 py-3 fw-bold text-dark" style="font-size: 14.5px;">
                                            #{{ $order->order_number ?? $order->id }}
                                        </td>
                                        <td class="text-secondary" style="font-size: 14px;">
                                            {{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }}
                                        </td>

                                        <td>
                                            @if (in_array(strtolower($order->status), ['completed', 'complete']))
                                                <span
                                                    class="badge rounded-pill fw-semibold bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                                    <span
                                                        class="d-inline-block rounded-circle bg-success me-1 status-dot"></span>
                                                    Completed
                                                </span>
                                            @elseif(strtolower($order->status) == 'cancelled')
                                                <span
                                                    class="badge rounded-pill fw-semibold bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                                    <span
                                                        class="d-inline-block rounded-circle bg-danger me-1 status-dot"></span>
                                                    Cancelled
                                                </span>
                                            @else
                                                <span
                                                    class="badge rounded-pill fw-semibold bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">
                                                    <span
                                                        class="d-inline-block rounded-circle bg-warning me-1 status-dot"></span>
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            @if (in_array(strtolower($order->payment->status), ['paid', 'completed', 'success']))
                                                <span
                                                    class="badge rounded-pill fw-semibold bg-success-subtle text-success border border-success-subtle px-3 py-2 payment-status">
                                                    <i data-lucide="check-circle" class="icon-xs me-1"></i> Paid
                                                </span>
                                            @elseif(strtolower($order->payment->status) == 'failed')
                                                <span
                                                    class="badge rounded-pill fw-semibold bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 payment-status">
                                                    <i data-lucide="x-circle" class="icon-xs me-1"></i> Failed
                                                </span>
                                            @else
                                                <span
                                                    class="badge rounded-pill fw-semibold bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 payment-status">
                                                    <i data-lucide="clock" class="icon-xs me-1"></i> Pending
                                                </span>
                                            @endif
                                        </td>

                                        <td class="fw-bold text-dark" style="font-size: 14.5px;">
                                            ${{ number_format($order->total_amount ?? 0, 2) }}
                                        </td>

                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('user.order.show', $order->id) }}"
                                                    class="btn btn-view btn-sm px-3 d-inline-flex align-items-center justify-content-center">
                                                    <i data-lucide="eye" class="icon-sm"></i>
                                                    <span class="d-none d-sm-inline ms-1">View</span>
                                                </a>

                                                @if (
                                                    !in_array(strtolower($order->payment->status), ['paid', 'completed', 'success']) &&
                                                        strtolower($order->status) !== 'cancelled')
                                                    <a href="{{ route('payment.qr', $order->id) }}"
                                                        class="btn btn-pay btn-sm px-3 d-inline-flex align-items-center justify-content-center">
                                                        <i data-lucide="qr-code" class="icon-sm"></i>
                                                        <span class="d-none d-sm-inline ms-1">Pay Now</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                                <div class="p-3 rounded-circle bg-light text-secondary mb-3">
                                                    <i data-lucide="package-search"
                                                        style="width: 40px; height: 40px; stroke-width: 1.5; opacity: 0.7;"></i>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1">No Orders Found</h6>
                                                <p class="small text-muted mb-3">You haven't placed any orders yet. Start
                                                    shopping now!</p>
                                                <a href="{{ route('home') }}" class="btn btn-primary px-4 shadow-sm"
                                                    style="border-radius: 8px;">
                                                    Start Shopping
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if ($orders->hasPages())
                            <div class="px-4 py-3 border-top">
                                {{ $orders->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .custom-table tbody tr:hover {
            background-color: #f8fafc !important;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            vertical-align: middle;
        }

        .btn-view {
            background: #eef2ff;
            color: #4338ca;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-view:hover {
            background: #4338ca !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(67, 56, 202, 0.2);
        }

        .btn-pay {
            background: #e6f4ea;
            color: #137333;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-pay:hover {
            background: #137333 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(19, 115, 51, 0.2);
        }

        .icon-sm {
            width: 16px;
            height: 16px;
        }

        .icon-xs {
            width: 14px;
            height: 14px;
            vertical-align: middle;
        }

        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }

        .page-link {
            color: #4338ca !important;
            border: none !important;
            border-radius: 8px !important;
            margin: 0 2px;
        }

        .page-item.active .page-link {
            background-color: #4338ca !important;
            color: #ffffff !important;
        }

        .page-link:hover {
            background-color: #eef2ff !important;
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
