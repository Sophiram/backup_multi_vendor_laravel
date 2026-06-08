@extends('vendor.layouts.layout')

@section('vendor_page_title')
    Sales Report - Vendor Panel
@endsection

@section('vendor_layout')
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 pb-4 border-bottom animate-fade-up">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center">
                <div
                    class="icon-shape-sm bg-primary-soft text-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                    <i data-feather="pie-chart" class="icon-md"></i>
                </div>
                Sales Overview
            </h3>
            <p class="text-muted small mb-0 mt-1 ms-1">Analyze your revenue, order volume, and monthly performance.</p>
        </div>
        <a href="{{ route('vendor.report.export') }}"
            class="btn btn-gradient-primary btn-sm px-4 py-2 rounded-pill fw-semibold shadow-sm d-inline-flex align-items-center transition-all hover-lift">
            <i data-feather="download" class="me-2 icon-sm"></i> Export Report
        </a>
    </div>

    {{-- បន្ថែមផ្នែក Filter នៅទីនេះ --}}
    <div class="card border-0 custom-shadow rounded-4 bg-white mb-4 animate-fade-up" style="animation-delay: 0.05s;">
        <div class="card-body p-3 p-md-4">
            <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-sm-4 col-md-3">
                    <label class="fw-bold small text-secondary mb-2 letter-spacing-1 text-uppercase">Filter by Year</label>
                    <select name="year" class="form-select border-0 bg-light rounded-3 shadow-sm py-2">
                        <option value="2026" {{ request('year', date('Y')) == '2026' ? 'selected' : '' }}>2026</option>
                        <option value="2025" {{ request('year') == '2025' ? 'selected' : '' }}>2025</option>
                        <option value="2024" {{ request('year') == '2024' ? 'selected' : '' }}>2024</option>
                    </select>
                </div>

                <div class="col-12 col-sm-4 col-md-3">
                    <label class="fw-bold small text-secondary mb-2 letter-spacing-1 text-uppercase">Filter by Month</label>
                    <select name="month" class="form-select border-0 bg-light rounded-3 shadow-sm py-2">
                        <option value="">All Months</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-12 col-sm-4 col-md-auto d-flex gap-2">
                    <button type="submit"
                        class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center transition-all hover-lift">
                        <i data-feather="filter" class="me-2 icon-sm"></i> Apply
                    </button>

                    @if (request()->has('year') || request()->has('month'))
                        <a href="{{ url()->current() }}"
                            class="btn btn-light px-3 py-2 rounded-3 shadow-sm text-danger d-flex align-items-center transition-all hover-lift"
                            title="Clear Filters">
                            <i data-feather="x" class="icon-sm"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
    {{-- បញ្ចប់ផ្នែក Filter --}}

    <div class="row g-4 mb-5">
        <div class="col-12 col-sm-6 col-lg-4 animate-fade-up" style="animation-delay: 0.1s;">
            <div
                class="card border-0 custom-shadow h-100 rounded-4 bg-white transition-all hover-lift overflow-hidden position-relative">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="text-secondary fw-bold text-uppercase small mb-0 letter-spacing-1">Total Earnings</h6>
                        <div
                            class="bg-success-soft rounded-circle d-flex align-items-center justify-content-center icon-shape">
                            <i data-feather="dollar-sign" class="text-success icon-lg"></i>
                        </div>
                    </div>
                    <h2 class="fw-bolder text-dark mb-1 display-6">${{ number_format($total_earnings ?? 0, 2) }}</h2>
                    <div class="d-flex align-items-center mt-3">
                        <span
                            class="badge bg-success-soft text-success rounded-pill px-2 py-1 d-flex align-items-center me-2">
                            <i data-feather="check-circle" class="icon-xs me-1"></i> Verified
                        </span>
                        <span class="text-muted small">Revenue for selected period</span>
                    </div>
                </div>
                <div class="position-absolute bottom-0 start-0 w-100 bg-success" style="height: 3px; opacity: 0.8;"></div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-4 animate-fade-up" style="animation-delay: 0.2s;">
            <div
                class="card border-0 custom-shadow h-100 rounded-4 bg-white transition-all hover-lift overflow-hidden position-relative">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="text-secondary fw-bold text-uppercase small mb-0 letter-spacing-1">Items Sold</h6>
                        <div
                            class="bg-info-soft rounded-circle d-flex align-items-center justify-content-center icon-shape">
                            <i data-feather="package" class="text-info icon-lg"></i>
                        </div>
                    </div>
                    <h2 class="fw-bolder text-dark mb-1 display-6">{{ number_format($total_items_sold ?? 0) }}</h2>
                    <div class="d-flex align-items-center mt-3">
                        <span class="badge bg-info-soft text-info rounded-pill px-2 py-1 d-flex align-items-center me-2">
                            <i data-feather="trending-up" class="icon-xs me-1"></i> Volume
                        </span>
                        <span class="text-muted small">Total products shipped</span>
                    </div>
                </div>
                <div class="position-absolute bottom-0 start-0 w-100 bg-info" style="height: 3px; opacity: 0.8;"></div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-4 animate-fade-up" style="animation-delay: 0.3s;">
            <div
                class="card border-0 custom-shadow h-100 rounded-4 bg-white transition-all hover-lift overflow-hidden position-relative">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="text-secondary fw-bold text-uppercase small mb-0 letter-spacing-1">Total Orders</h6>
                        <div
                            class="bg-warning-soft rounded-circle d-flex align-items-center justify-content-center icon-shape">
                            <i data-feather="shopping-cart" class="text-warning icon-lg"></i>
                        </div>
                    </div>
                    <h2 class="fw-bolder text-dark mb-1 display-6">{{ number_format($total_orders ?? 0) }}</h2>
                    <div class="d-flex align-items-center mt-3">
                        <span
                            class="badge bg-warning-soft text-warning rounded-pill px-2 py-1 d-flex align-items-center me-2">
                            <i data-feather="activity" class="icon-xs me-1"></i> Processed
                        </span>
                        <span class="text-muted small">All completed orders</span>
                    </div>
                </div>
                <div class="position-absolute bottom-0 start-0 w-100 bg-warning" style="height: 3px; opacity: 0.8;"></div>
            </div>
        </div>
    </div>

    <div class="card border-0 custom-shadow rounded-4 bg-white overflow-hidden animate-fade-up"
        style="animation-delay: 0.4s;">
        <div
            class="card-header bg-transparent border-bottom-0 p-4 pb-2 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
            <div class="d-flex align-items-center">
                <div
                    class="icon-shape-sm bg-primary-soft text-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                    <i data-feather="calendar" class="icon-sm"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark">Monthly Breakdown</h5>
                    <span class="text-muted small">Detailed sales overview for {{ request('year', date('Y')) }}</span>
                </div>
            </div>
        </div>

        <div class="table-responsive p-3 pt-0">
            <table class="table table-borderless align-middle mb-0 custom-table text-nowrap">
                <thead class="text-muted">
                    <tr>
                        <th class="ps-4 py-3 small text-uppercase fw-bold letter-spacing-1">Time Period</th>
                        <th class="py-3 small text-uppercase fw-bold letter-spacing-1">Order Volume</th>
                        <th class="py-3 pe-4 text-end small text-uppercase fw-bold letter-spacing-1">Gross Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($monthly_sales as $sale)
                        <tr class="table-row-hover">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="calendar-badge me-3 text-center shadow-sm">
                                        <span class="d-block text-white bg-primary small fw-bold py-1 rounded-top"
                                            style="font-size: 0.7rem;">{{ request('year', date('Y')) }}</span>
                                        <span
                                            class="d-block fw-bolder text-dark bg-white py-1 rounded-bottom">{{ \Carbon\Carbon::create()->month($sale->month)->format('M') }}</span>
                                    </div>
                                    <span
                                        class="fw-semibold text-dark">{{ \Carbon\Carbon::create()->month($sale->month)->format('F') }}</span>
                                </div>
                            </td>
                            <td class="py-3">
                                <div
                                    class="d-inline-flex align-items-center bg-light rounded-pill px-3 py-2 border border-light">
                                    <i data-feather="shopping-bag" class="text-secondary me-2"
                                        style="width: 14px; height: 14px;"></i>
                                    <span class="fw-semibold text-dark">{{ $sale->count }}</span>
                                    <span class="text-muted ms-1 small">orders</span>
                                </div>
                            </td>
                            <td class="pe-4 py-3 text-end">
                                <span class="fw-bolder text-success fs-5">
                                    ${{ number_format($sale->total, 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <div class="text-muted d-flex flex-column align-items-center justify-content-center">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3"
                                        style="width: 80px; height: 80px;">
                                        <i data-feather="inbox" class="text-secondary"
                                            style="width: 40px; height: 40px;"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">No Sales Data Found</h5>
                                    <p class="small mb-0">Try adjusting your filters or check back later once sales are
                                        made.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        /* Base Typography & Colors */
        .text-dark {
            color: #1e293b !important;
        }

        .text-secondary {
            color: #64748b !important;
        }

        .text-success {
            color: #10b981 !important;
        }

        .text-primary {
            color: #4f46e5 !important;
        }

        /* Indigo primary */
        .text-info {
            color: #0ea5e9 !important;
        }

        .text-warning {
            color: #f59e0b !important;
        }

        /* Soft Backgrounds for Glassmorphism feel */
        .bg-success-soft {
            background-color: rgba(16, 185, 129, 0.1) !important;
        }

        .bg-primary-soft {
            background-color: rgba(79, 70, 229, 0.1) !important;
        }

        .bg-info-soft {
            background-color: rgba(14, 165, 233, 0.1) !important;
        }

        .bg-warning-soft {
            background-color: rgba(245, 158, 11, 0.1) !important;
        }

        /* Gradients */
        .btn-gradient-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: white;
            border: none;
        }

        .btn-gradient-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #2563eb 100%);
            color: white;
        }

        /* Icon Sizing Utilities */
        .icon-xs {
            width: 14px;
            height: 14px;
        }

        .icon-sm {
            width: 18px;
            height: 18px;
        }

        .icon-md {
            width: 20px;
            height: 20px;
        }

        .icon-lg {
            width: 24px;
            height: 24px;
        }

        /* Shape Utilities */
        .icon-shape {
            width: 48px;
            height: 48px;
        }

        .icon-shape-sm {
            width: 38px;
            height: 38px;
        }

        /* Calendar Badge for Table */
        .calendar-badge {
            width: 45px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }

        /* Typography */
        .letter-spacing-1 {
            letter-spacing: 0.5px;
        }

        /* Custom Shadows & Interactions */
        .custom-shadow {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03) !important;
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08) !important;
        }

        /* Table Styling */
        .custom-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .custom-table tbody tr:last-child {
            border-bottom: none;
        }

        .table-row-hover:hover {
            background-color: #f8fafc;
            border-radius: 8px;
        }

        .custom-table th {
            border-bottom: 2px solid #e2e8f0;
            color: #94a3b8;
        }

        /* Load Animations */
        .animate-fade-up {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Feather Fixes */
        .feather {
            display: inline-block;
            vertical-align: middle;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
@endsection
