@extends('admin.layouts.layout')

@section('admin_page_title', 'Dashboard - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-0">Dashboard Overview</h3>
                <p class="text-muted small">Welcome back, {{ Auth::user()->name }}!</p>
            </div>
            <a href="{{ route('admin.export.report') }}" class="btn btn-primary rounded-pill shadow-sm">
                <i data-lucide="download" style="width: 18px;"></i>
                <span class="d-none d-md-inline ms-2">Export Report</span>
            </a>
        </div>

        <div class="row">
            @php
                $metrics = [
                    [
                        'title' => 'Categories',
                        'value' => $categoryCount,
                        'bg' => '#e0e7ff',
                        'icon' => 'grid',
                        'text' => '#4338ca',
                    ],
                    [
                        'title' => 'Products',
                        'value' => $productCount,
                        'bg' => '#dcfce7',
                        'icon' => 'shopping-bag',
                        'text' => '#15803d',
                    ],
                    [
                        'title' => 'Pending',
                        'value' => $pendingVendorCount,
                        'bg' => '#fef9c3',
                        'icon' => 'user-check',
                        'text' => '#a16207',
                    ],
                    [
                        'title' => 'Orders',
                        'value' => $orderCount,
                        'bg' => '#fee2e2',
                        'icon' => 'shopping-cart',
                        'text' => '#b91c1c',
                    ],
                ];
            @endphp

            @foreach ($metrics as $metric)
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm mb-4 p-4 rounded-4 metric-card"
                        style="background-color: {{ $metric['bg'] }};">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted text-uppercase fw-bold mb-1"
                                    style="font-size: 0.7rem; letter-spacing: 0.5px;">{{ $metric['title'] }}</p>
                                <h2 class="fw-bold mb-0" style="color: {{ $metric['text'] }};">
                                    {{ number_format($metric['value']) }}</h2>
                            </div>
                            <div class="p-3 bg-white rounded-circle shadow-sm">
                                <i data-lucide="{{ $metric['icon'] }}"
                                    style="color: {{ $metric['text'] }}; width: 22px; height: 22px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-4">Sales Performance</h5>
                    <div style="height: 350px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Recent Orders</h5>
            </div>
            <div class="table-responsive p-4">
                <table class="table table-hover align-middle mb-0">
                    <thead class="text-muted small">
                        <tr>
                            <th>CUSTOMER</th>
                            <th>ORDER ID</th>
                            <th>TOTAL</th>
                            <th class="text-end">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentOrders as $order)
                            <tr>
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px; font-weight: 700; color: #64748b;">
                                            {{ strtoupper(substr($order->user->name, 0, 2)) }}
                                        </div>
                                        <span class="fw-semibold">{{ $order->user->name }}</span>
                                    </div>
                                </td>
                                <td class="text-secondary">#{{ $order->id }}</td>
                                <td class="fw-bold">${{ number_format((float) $order->total_amount, 2) }}</td>
                                <td class="text-end">
                                    <span
                                        class="badge {{ $order->status == 'complete' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill px-3 py-2">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .metric-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .metric-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        }

        /* បន្ថែមពីលើអ្វីដែលអ្នកមាន */
        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            h3 {
                font-size: 1.25rem !important;
            }

            /* កាត់បន្ថយគម្លាតនៅលើទូរសព្ទ */
            .card {
                margin-bottom: 1rem !important;
            }

            /* ធ្វើឱ្យប៊ូតុងតូចជាងមុនបន្តិចលើទូរសព្ទ */
            .btn {
                padding: 0.5rem 1rem !important;
                font-size: 0.85rem !important;
            }

            /* ធានាថា Chart មិនបែក Layout */
            #salesChart {
                width: 100% !important;
                height: 100% !important;
            }

            /* តុបតែងតារាងឱ្យស្អាតលើ Mobile */
            .table td,
            .table th {
                white-space: nowrap;
                /* ការពារកុំឱ្យអក្សរចុះបន្ទាត់ផ្តេសផ្តាស */
                padding: 1rem 0.75rem !important;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Re-initialize Lucide icons for the content
            lucide.createIcons();

            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! $labels !!},
                    datasets: [{
                        data: {!! $values !!},
                        borderColor: '#4338ca',
                        backgroundColor: 'rgba(67, 56, 202, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
