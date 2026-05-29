@extends('user.layouts.layout')

@section('user_page_title')
    History - User Panel
@endsection

@section('user_layout')
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1" style="color: #0f172a;">
                    <i data-lucide="shopping-bag" class="me-2 text-primary" style="vertical-align: middle;"></i>Order History
                </h3>
                <p class="text-muted small mb-0">Review and track all your past and current purchases.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius: 20px; background: #ffffff; overflow: hidden;">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" style="border-collapse: separate;">
                            <thead style="background-color: #f8fafc; border-bottom: 1px solid #edf2f7;">
                                <tr class="text-secondary"
                                    style="font-size: 13px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">
                                    <th class="ps-4 py-3 border-0">Order ID</th>
                                    <th class="py-3 border-0">Date</th>
                                    <th class="py-3 border-0">Status</th>
                                    <th class="py-3 border-0">Total</th>
                                    <th class="pe-4 py-3 border-0 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr class="align-middle transition-all" style="border-bottom: 1px solid #f1f5f9;">
                                        <td class="ps-4 py-3 fw-bold text-dark" style="font-size: 14.5px;">
                                            #{{ $order->order_number }}
                                        </td>
                                        <td class="text-secondary" style="font-size: 14px;">
                                            {{ $order->created_at->format('M d, Y') }}
                                        </td>
                                        <td>
                                            @if ($order->status == 'completed' || $order->status == 'complete')
                                                <span
                                                    class="badge rounded-pill fw-semibold bg-success-subtle text-success border border-success-subtle"
                                                    style="padding: 6px 14px; font-size: 12px;">
                                                    <span class="d-inline-block rounded-circle bg-success me-1"
                                                        style="width: 6px; height: 6px; vertical-align: middle;"></span>
                                                    Completed
                                                </span>
                                            @else
                                                <span
                                                    class="badge rounded-pill fw-semibold bg-warning-subtle text-warning border border-warning-subtle"
                                                    style="padding: 6px 14px; font-size: 12px;">
                                                    <span class="d-inline-block rounded-circle bg-warning me-1"
                                                        style="width: 6px; height: 6px; vertical-align: middle;"></span>
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-dark" style="font-size: 14.5px;">
                                            ${{ number_format($order->total_price, 2) }}
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('admin.order.show', $order->id) }}"
                                                class="btn btn-light btn-sm rounded-3 fw-medium px-3 text-secondary border-0"
                                                style="background: #f1f5f9; transition: all 0.2s;">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                                <div class="p-3 rounded-circle bg-light text-secondary mb-3">
                                                    <i data-lucide="package-search"
                                                        style="width: 40px; height: 40px; stroke-width: 1.5; opacity: 0.7;"></i>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1">No Orders Found</h6>
                                                <p class="small text-muted mb-0">You haven't placed any orders yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8fafc !important;
        }

        .btn-light:hover {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }
    </style>

    <script>
        // អានឡុក Icons
        lucide.createIcons();
    </script>
@endsection
