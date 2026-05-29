@extends('user.layouts.layout')

@section('user_page_title', 'Dashboard - User Panel')

@section('user_layout')
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1" style="color: #0f172a;">User Dashboard</h3>
                <p class="text-muted small mb-0">Overview of your account activities and metrics.</p>
            </div>
        </div>

        <div class="card border-0 p-4 mb-4 shadow-sm position-relative overflow-hidden"
             style="border-radius: 20px; background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);">
            <div class="row align-items-center">
                <div class="col-md-8 position-relative" style="z-index: 2;">
                    <h4 class="fw-bold text-dark mb-2">Welcome back, {{ Auth::user()->name }}! 👋</h4>
                    <p class="text-secondary mb-0" style="max-width: 550px; font-size: 14.5px; line-height: 1.6;">
                        This is your personal space. Easily manage your order tracking, process payment configurations, and control your affiliate link generation from one place.
                    </p>
                </div>
            </div>
            <div class="position-absolute end-0 top-0 translate-middle-y text-primary opacity-10 d-none d-md-block" style="font-size: 150px; right: 20px !important;">
                <i data-lucide="activity" style="width: 180px; height: 180px; stroke-width: 1;"></i>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-4">
                <div class="card p-4 border-0 shadow-sm card-hover h-100" style="border-radius: 18px; background: #ffffff; transition: transform 0.2s;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-medium d-block mb-1" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Total Orders</span>
                            <h2 class="fw-extrabold text-dark mb-0" style="font-size: 32px;">{{ $totalOrders }}</h2>
                        </div>
                        <div class="p-3 rounded-3" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                            <i data-lucide="shopping-cart" style="width: 24px; height: 24px; stroke-width: 2.5;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="card p-4 border-0 shadow-sm card-hover h-100" style="border-radius: 18px; background: #ffffff; transition: transform 0.2s;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fw-medium d-block mb-1" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Account Status</span>
                            <h2 class="fw-extrabold text-success mb-0 d-flex align-items-center gap-2" style="font-size: 28px;">
                                <span class="d-inline-block rounded-circle bg-success" style="width: 10px; height: 10px; animation: pulse 2s infinite;"></span>
                                Active
                            </h2>
                        </div>
                        <div class="p-3 rounded-3" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                            <i data-lucide="shield-check" style="width: 24px; height: 24px; stroke-width: 2.5;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-xl-4">
                <div class="card p-4 border-0 shadow-sm card-hover h-100" style="border-radius: 18px; background: #ffffff; transition: transform 0.2s;">
                    <div class="d-flex flex-column justify-content-between h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-medium d-block" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Referral Link</span>
                            <div class="text-primary"><i data-lucide="link" style="width: 18px; height: 18px;"></i></div>
                        </div>
                        <div class="input-group input-group-sm">
                            <input type="text" id="dbRefLink" class="form-control bg-light border-0 px-2 text-truncate" style="font-size: 12px; font-weight: 500;" value="{{ url('/ref/' . Auth::user()->id) }}" readonly>
                            <button class="btn btn-primary btn-sm px-3 fw-medium" onclick="copyDbLink()" id="btnDbCopy">Copy</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 p-4" style="border-radius: 20px; background: #ffffff;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 16px;">Recent Orders</h5>
                        <a href="{{ route('user.history') }}" class="btn btn-link text-primary btn-sm fw-medium text-decoration-none p-0">View All Orders →</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr class="text-secondary" style="font-size: 12px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">
                                    <th class="border-0 ps-0">Order ID</th>
                                    <th class="border-0">Date</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 text-end pe-0">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders->take(3) as $order)
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td class="fw-semibold text-dark ps-0 py-3" style="font-size: 14px;">#{{ $order->order_number }}</td>
                                    <td class="text-secondary" style="font-size: 13.5px;">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($order->status == 'completed' || $order->status == 'complete')
                                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 11px;">Completed</span>
                                        @else
                                            <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-2 py-1" style="font-size: 11px;">Pending</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-dark text-end pe-0" style="font-size: 14px;">${{ number_format($order->total_price, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">No recent orders found.</td>
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
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.05) !important;
        }
        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
    </style>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        function copyDbLink() {
            const linkInput = document.getElementById('dbRefLink');
            const btnCopy = document.getElementById('btnDbCopy');

            navigator.clipboard.writeText(linkInput.value).then(() => {
                btnCopy.innerText = 'Copied!';
                btnCopy.classList.remove('btn-primary');
                btnCopy.classList.add('btn-success');

                setTimeout(() => {
                    btnCopy.innerText = 'Copy';
                    btnCopy.classList.remove('btn-success');
                    btnCopy.classList.add('btn-primary');
                }, 2000);
            });
        }
    </script>
@endsection
