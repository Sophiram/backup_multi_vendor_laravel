@extends('vendor.layouts.layout')

@section('vendor_page_title')
    Sales Report - Vendor Panel
@endsection

@section('vendor_layout')
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-dark mb-1">Sales Reports</h3>
            <p class="text-muted small">Track your business growth, total earnings, and monthly sales performance.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="card-title text-secondary fw-bold small text-uppercase tracking-wider mb-0">Total Earnings</h5>
                        <div class="stat text-success bg-light-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i data-feather="dollar-sign"></i>
                        </div>
                    </div>
                    <h1 class="fw-bold text-dark mb-0">${{ number_format($total_earnings ?? 0, 2) }}</h1>
                    <span class="text-muted small">From successful deliveries</span>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="card-title text-secondary fw-bold small text-uppercase tracking-wider mb-0">Items Sold</h5>
                        <div class="stat text-primary bg-light-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i data-feather="package"></i>
                        </div>
                    </div>
                    <h1 class="fw-bold text-dark mb-0">{{ number_format($total_items_sold ?? 0) }}</h1>
                    <span class="text-muted small">Products dynamic count</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark text-uppercase small">
                        <i class="bi bi-bar-chart-line text-primary me-2"></i>Monthly Performance ({{ date('Y') }})
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="table-light border-bottom text-secondary" style="font-size: 12px; font-weight: 700;">
                            <tr>
                                <th class="ps-4 py-3">Month</th>
                                <th class="py-3">Orders Count</th>
                                <th class="py-3 pe-4 text-end" style="width: 200px;">Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 13.5px;">
                            @forelse ($monthly_sales as $sale)
                                <tr class="border-bottom">
                                    <td class="ps-4 fw-semibold text-dark">
                                        {{ \Carbon\Carbon::create()->month($sale->month)->format('F') }}
                                    </td>
                                    <td class="text-secondary">{{ $sale->count }} Orders</td>
                                    <td class="pe-4 fw-bold text-success text-end">${{ number_format($sale->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="bi bi-graph-down fs-1 d-block mb-2 text-light-muted"></i>No sales data available for this year.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-light-success { background-color: #e8f5e9 !important; color: #2e7d32 !important; }
        .bg-light-primary { background-color: #e3f2fd !important; color: #0d47a1 !important; }
        .text-light-muted { color: #ccc; }
    </style>
@endsection
