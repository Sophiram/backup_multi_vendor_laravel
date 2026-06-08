@extends('admin.layouts.layout')

@section('admin_page_title', 'Advanced Dashboard - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-2 py-2">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h4 class="fw-bolder text-dark mb-1 fs-3">Enterprise Dashboard</h4>
                <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                    Welcome back, <span class="fw-bold" style="color: #6366f1;">{{ Auth::user()->name }}</span>! Here is your
                    real-time ecosystem pipeline.
                </p>
            </div>
            <div class="d-flex gap-2 flex-column flex-sm-row">
                <button onclick="refreshDashboard()"
                    class="btn btn-light border rounded-3 px-3 py-2.5 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 shadow-sm">
                    <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i> Refresh Data
                </button>
            </div>
        </div>

        <div class="row g-4 mb-4">
            @php
                $metrics = [
                    [
                        'title' => 'Categories',
                        'value' => $categoryCount ?? 0,
                        'bg' => 'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)',
                        'icon' => 'grid',
                        'iconBg' => 'rgba(255, 255, 255, 0.2)',
                    ],
                    [
                        'title' => 'Products',
                        'value' => $productCount ?? 0,
                        'bg' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                        'icon' => 'shopping-bag',
                        'iconBg' => 'rgba(255, 255, 255, 0.2)',
                    ],
                    [
                        'title' => 'Pending Vendors',
                        'value' => $pendingVendorCount ?? 0,
                        'bg' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                        'icon' => 'users',
                        'iconBg' => 'rgba(255, 255, 255, 0.2)',
                    ],
                    [
                        'title' => 'Total Orders',
                        'value' => $orderCount ?? 0,
                        'bg' => 'linear-gradient(135deg, #f43f5e 0%, #e11d48 100%)',
                        'icon' => 'shopping-cart',
                        'iconBg' => 'rgba(255, 255, 255, 0.2)',
                    ],
                ];
            @endphp

            @foreach ($metrics as $metric)
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm p-4 rounded-4 metric-card h-100"
                        style="background: {{ $metric['bg'] }}; color: white;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-white-50 text-uppercase fw-bold d-block mb-2"
                                    style="font-size: 0.75rem; letter-spacing: 1px;">{{ $metric['title'] }}</span>
                                <h3 class="fw-bolder mb-0 text-white"
                                    style="font-size: 2rem; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    {{ number_format($metric['value']) }}
                                </h3>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 custom-icon-box"
                                style="width: 56px; height: 56px; background-color: {{ $metric['iconBg'] }}; backdrop-filter: blur(8px);">
                                <i data-lucide="{{ $metric['icon'] }}" style="width: 26px; height: 26px; color: white;"></i>
                            </div>
                        </div>
                        <div class="position-absolute rounded-circle bg-white"
                            style="width: 100px; height: 100px; opacity: 0.05; top: -20px; right: -20px;"></div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <div
                        class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-2">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Sales Performance</h5>
                            <span class="text-muted small">Revenue growth over time</span>
                        </div>
                        <div class="position-relative">
                            <input type="text" id="dateRangePicker"
                                class="btn btn-sm rounded-pill px-3 fw-medium text-start"
                                style="background-color: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; min-width: 220px;"
                                placeholder="Select Date Range">
                            <i data-lucide="calendar"
                                style="width: 14px; height: 14px; position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #7e22ce; pointer-events: none;"></i>
                        </div>
                    </div>
                    <div id="salesChart" style="min-height: 330px; width: 100%;"></div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <div class="mb-3">
                        <h5 class="fw-bold text-dark mb-1">Product Share</h5>
                        <span class="text-muted small">Sales distribution by category</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center" style="min-height: 300px;">
                        <div id="distributionChart" style="width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-8">
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden h-100">
                    <div
                        class="card-header bg-white border-bottom-0 pt-4 px-4 pb-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0">Recent Platform Orders</h5>
                        <a href="{{ route('admin.order.history') }}" class="text-decoration-none fw-bold"
                            style="color: #ec4899; font-size: 0.875rem;">View All
                            <i data-lucide="arrow-right" class="ms-1" style="width: 16px; height: 16px;"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 custom-table">
                                <thead class="text-uppercase fw-bold text-secondary"
                                    style="font-size: 0.725rem; letter-spacing: 1px; background: linear-gradient(to right, #f8fafc, #f1f5f9);">
                                    <tr>
                                        <th class="ps-4 py-3 border-0">Customer</th>
                                        <th class="py-3 border-0">Order Ref</th>
                                        <th class="py-3 border-0">Amount</th>
                                        <th class="pe-4 py-3 text-end border-0">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentOrders ?? [] as $order)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-3 d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                                        style="width: 40px; height: 40px; background: linear-gradient(135deg, #6366f1, #a855f7); color: white; border-radius: 10px; font-size: 0.85rem;">
                                                        {{ strtoupper(substr($order->user->name ?? 'CU', 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <span class="fw-bold text-dark d-block"
                                                            style="font-size: 0.9rem;">{{ $order->user->name ?? 'N/A' }}</span>
                                                        <span
                                                            class="text-muted small">{{ $order->user->email ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <span
                                                    class="badge bg-light text-secondary border font-monospace">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                                            </td>
                                            <td class="fw-bold py-3">${{ number_format($order->total_amount, 2) }}</td>
                                            <td class="text-end pe-4 py-3">
                                                @if (strtolower($order->status) == 'complete' || strtolower($order->status) == 'completed')
                                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold"
                                                        style="background-color: #e2fbe8; color: #10b981; border: 1px solid #bbf7d0;">
                                                        Complete
                                                    </span>
                                                @elseif (strtolower($order->status) == 'pending')
                                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold"
                                                        style="background-color: #fff8e6; color: #f59e0b; border: 1px solid #fef3c7;">
                                                        Pending
                                                    </span>
                                                @elseif (strtolower($order->status) == 'processing')
                                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold"
                                                        style="background-color: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;">
                                                        Processing
                                                    </span>
                                                @else
                                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold"
                                                        style="background-color: #fee2e2; color: #ef4444; border: 1px solid #fecaca;">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center justify-content-center">
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3"
                                                        style="width: 64px; height: 64px;">
                                                        <i data-lucide="inbox"
                                                            style="width: 32px; height: 32px; color: #94a3b8;"></i>
                                                    </div>
                                                    <span class="text-muted fw-medium">No recent orders found.</span>
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

            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                    <h5 class="fw-bold text-dark mb-3">Top Selling Products</h5>
                    <div class="d-flex flex-column gap-3">
                        @forelse ($topProducts ?? [] as $product)
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light rounded-3 p-1 d-flex align-items-center justify-content-center"
                                        style="width: 48px; height: 48px; overflow: hidden;">
                                        @if ($product->images && $product->images->first())
                                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                                                alt="{{ $product->product_name }}" class="rounded-2"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <img src="https://placehold.co/48x48?text=No+Image" alt="No Image"
                                                class="rounded-2" style="width: 100%; height: 100%; object-fit: cover;">
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">
                                            {{ $product->product_name }}</h6>
                                        <small class="text-muted">{{ number_format($product->total_qty) }} Sales</small>
                                    </div>
                                </div>
                                <span
                                    class="fw-bolder text-end text-success">${{ number_format($product->total_revenue, 2) }}</span>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i data-lucide="inbox" class="text-muted mb-2" style="width: 32px; height: 32px;"></i>
                                <p class="text-muted small mb-0">No top selling products in this period.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body {
            background-color: #f8fafc;
        }

        .metric-card {
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
            z-index: 1;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08) !important;
        }

        .custom-table tbody tr:hover {
            background-color: #f8fafc;
            transform: translateX(3px);
            transition: all 0.2s ease;
        }

        .hover-bg:hover {
            background-color: #f8fafc;
            cursor: pointer;
        }

        /* ជួសជុល និងបន្ថែម Blur Effect ទៅកាន់ SweetAlert */
        .swal2-backdrop-show {
            backdrop-filter: blur(8px) !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // 1. Dynamic Date Range Picker Setup
            flatpickr("#dateRangePicker", {
                mode: "range",
                dateFormat: "Y-m-d",
                defaultDate: [
                    "{{ isset($startDate) ? $startDate->format('Y-m-d') : '2026-01-01' }}",
                    "{{ isset($endDate) ? $endDate->format('Y-m-d') : '2026-12-31' }}"
                ],
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const startDate = instance.formatDate(selectedDates[0], "Y-m-d");
                        const endDate = instance.formatDate(selectedDates[1], "Y-m-d");

                        Swal.fire({
                            title: 'Fetching Data...',
                            text: 'Please wait a moment',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        window.location.href = `?start_date=${startDate}&end_date=${endDate}`;
                    }
                }
            });

            @php
                $defaultValues = '[31000, 40000, 28000, 51000, 42000, 109000, 100000, 120000, 85000, 95000, 140000, 162000]';
                $defaultLabels = '["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]';

                if (isset($values)) {
                    $decodedValues = is_string($values) ? json_decode($values, true) : (is_object($values) ? $values->toArray() : $values);
                    $finalValues = !empty($decodedValues) && count($decodedValues) > 0 ? json_encode($decodedValues) : $defaultValues;
                } else {
                    $finalValues = $defaultValues;
                }

                if (isset($labels)) {
                    $decodedLabels = is_string($labels) ? json_decode($labels, true) : (is_object($labels) ? $labels->toArray() : $labels);
                    $finalLabels = !empty($decodedLabels) && count($decodedLabels) > 0 ? json_encode($decodedLabels) : $defaultLabels;
                } else {
                    $finalLabels = $defaultLabels;
                }
            @endphp

            // 2. Area Chart (Dynamic Sales Performance)
            const salesOptions = {
                series: [{
                    name: 'Gross Sales',
                    data: {!! $finalValues !!}
                }],
                chart: {
                    type: 'area',
                    height: 330,
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#6366f1'],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        colorStops: [{
                                offset: 0,
                                color: '#6366f1',
                                opacity: 0.35
                            },
                            {
                                offset: 100,
                                color: '#ec4899',
                                opacity: 0.0
                            }
                        ]
                    }
                },
                xaxis: {
                    categories: {!! $finalLabels !!}
                },
                tooltip: {
                    theme: 'dark'
                }
            };
            new ApexCharts(document.querySelector("#salesChart"), salesOptions).render();

            // 3. Donut Chart (Dynamic Category Distribution)
            const distributionOptions = {
                series: {!! isset($chartPieValues) ? json_encode($chartPieValues) : '[44, 55, 13, 33]' !!},
                labels: {!! isset($chartPieLabels)
                    ? json_encode($chartPieLabels)
                    : '["Electronics", "Clothing", "Home Appliances", "Others"]' !!},
                chart: {
                    type: 'donut',
                    height: 300
                },
                colors: ['#6366f1', '#10b981', '#f59e0b', '#ec4899'],
                legend: {
                    position: 'bottom'
                },
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Products',
                                    formatter: () => '{{ number_format($productCount ?? 1420) }}'
                                }
                            }
                        }
                    }
                }
            };
            new ApexCharts(document.querySelector("#distributionChart"), distributionOptions).render();
        });

        function refreshDashboard() {
            Swal.fire({
                title: 'Updating Dashboard',
                html: `
                    <div class="d-flex flex-column align-items-center my-2">
                        <div class="spinner-border mb-3" role="status"
                             style="width: 3.5rem; height: 3.5rem; color: #6366f1; border-width: 4px;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-secondary mb-0 small fw-medium" style="letter-spacing: 0.5px;">
                            Synchronizing live ecosystem data...
                        </p>
                    </div>
                `,
                showConfirmButton: false,
                allowOutsideClick: false,
                background: '#ffffff',
                backdrop: `rgba(15, 23, 42, 0.2)`,
                /* កែសម្រួលចេញជាកូដពណ៌ធម្មតា ចំណែក Blur គឺគ្រប់គ្រងដោយ CSS ខាងលើ */
                customClass: {
                    popup: 'rounded-4 border-0 p-4 shadow-lg',
                    title: 'fw-bold text-dark fs-4 mb-0'
                },
                didOpen: () => {
                    setTimeout(() => {
                        location.reload();
                    }, 1200);
                }
            });
        }
    </script>
@endsection
