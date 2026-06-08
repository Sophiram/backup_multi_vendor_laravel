@extends('admin.layouts.layout')

@section('admin_page_title', 'Order Details - ' . ($order->order_number ?? 'N/A'))

@section('admin_layout')
    <div class="container-fluid px-2 py-2">
        <a href="{{ route('admin.order.history') }}"
            class="text-decoration-none text-secondary d-inline-flex align-items-center gap-1.5 mb-4 small fw-semibold transition-all hover-text-dark">
            <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i> Back to Orders History
        </a>

        @if (isset($order))
            <div class="row g-4">
                {{-- Left Column: Order Items Matrix Grid --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div
                            class="card-header bg-white border-bottom border-light py-3 px-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark" style="font-size: 1.05rem;">Items List</h5>
                                <span class="text-muted font-monospace small">Token Reference ID:
                                    #{{ $order->order_number }}</span>
                            </div>

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
                                class="badge rounded-pill px-2.5 py-1.5 text-uppercase font-monospace fw-semibold bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle"
                                style="font-size: 0.72rem;">
                                {{ $order->status }}
                            </span>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                                    <thead class="table-light text-uppercase"
                                        style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                        <tr>
                                            <th class="ps-4 py-3 text-muted fw-bold">Product Blueprint Details</th>
                                            <th class="py-3 text-muted fw-bold" style="width: 100px;">Quantity</th>
                                            <th class="pe-4 py-3 text-end text-muted fw-bold" style="width: 150px;">Unit
                                                Valuation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->items as $item)
                                            <tr>
                                                <td class="ps-4">
                                                    <span
                                                        class="fw-semibold text-dark d-block mb-0">{{ $item->product->product_name ?? 'Product Record Purged' }}</span>
                                                </td>
                                                <td class="text-secondary fw-medium font-monospace">{{ $item->quantity }}
                                                </td>
                                                <td class="pe-4 text-end fw-bold text-dark font-monospace">
                                                    ${{ number_format($item->price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light border-top-0">
                                        <tr>
                                            <td colspan="2" class="text-end fw-bold text-secondary ps-4 py-3">Gross Total
                                                Amount:</td>
                                            <td class="text-end pe-4 fw-bold text-dark font-monospace py-3"
                                                style="font-size: 1rem;">${{ number_format($order->total_amount, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Information Sidebar Control Panel --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="text-secondary p-1.5 bg-light rounded-2 d-inline-flex">
                                    <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                                </span>
                                <h6 class="fw-bold text-dark mb-0">Customer Information</h6>
                            </div>
                            <div class="bg-light-subtle rounded-3 p-2.5 border border-light">
                                <span class="d-block fw-semibold text-dark mb-0"
                                    style="font-size: 0.9rem;">{{ $order->user->name ?? 'N/A' }}</span>
                                <small class="text-muted text-break d-block mt-0.5"
                                    style="font-size: 0.8rem;">{{ $order->user->email ?? 'N/A' }}</small>
                            </div>

                            <hr class="my-3 opacity-25">

                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="text-secondary p-1.5 bg-light rounded-2 d-inline-flex">
                                    <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i>
                                </span>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">Shipping Address</h6>
                            </div>
                            <p class="text-muted small mb-0 lh-base bg-light-subtle rounded-3 p-2.5 border border-light">
                                {{ $order->shipping_address ?? 'No formal address coordinates provided.' }}</p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="text-secondary p-1.5 bg-light rounded-2 d-inline-flex">
                                    <i data-lucide="credit-card" style="width: 16px; height: 16px;"></i>
                                </span>
                                <h6 class="fw-bold text-dark mb-0">Update Settlement Status</h6>
                            </div>

                            @php
                                $currentPaymentStatus = $order->payment->status ?? 'pending';
                            @endphp

                            {{-- ត្រួតពិនិត្យលក្ខខណ្ឌ៖ បើស្ថានភាពជា paid នោះនឹងបង្ហាញតែ Badge ព័ត៌មានជាប់សោរ --}}
                            @if (strtolower($currentPaymentStatus) === 'paid')
                                <div
                                    class="d-flex align-items-center justify-content-between p-2.5 bg-success-subtle text-success border border-success-subtle rounded-3 font-monospace small fw-semibold">
                                    <span class="d-inline-flex align-items-center gap-1.5">
                                        <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                                        PAID / SETTLED
                                    </span>
                                    <span
                                        class="text-uppercase text-muted small px-2 py-0.5 bg-white rounded border fw-bold"
                                        style="font-size: 0.65rem;">LOCKED</span>
                                </div>
                            @else
                                <form action="{{ route('admin.order.payment.update', $order->id) }}" method="POST"
                                    class="m-0">
                                    @csrf
                                    @method('PUT')
                                    <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden border">
                                        <select name="payment_status"
                                            class="form-select border-0 bg-light-subtle font-monospace small fw-medium py-2"
                                            required>
                                            <option value="pending"
                                                {{ strtolower($currentPaymentStatus) === 'pending' ? 'selected' : '' }}>
                                                Pending
                                                Protocol
                                            </option>
                                            <option value="paid"
                                                {{ strtolower($currentPaymentStatus) === 'paid' ? 'selected' : '' }}>
                                                Paid / Settled</option>
                                            <option value="failed"
                                                {{ strtolower($currentPaymentStatus) === 'failed' ? 'selected' : '' }}>
                                                Failed / Voided</option>
                                            <option value="refunded"
                                                {{ strtolower($currentPaymentStatus) === 'refunded' ? 'selected' : '' }}>
                                                Refunded / Returned</option>
                                        </select>
                                        <button type="submit"
                                            class="btn btn-success fw-semibold px-3 border-0 d-inline-flex align-items-center gap-1">
                                            <i data-lucide="refresh-cw" style="width: 13px; height: 13px;"></i> Update
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center gap-2" role="alert">
                <i data-lucide="alert-circle" style="width: 18px; height: 18px;"></i>
                <span class="small fw-semibold">Target order dataset records could not be found inside active database
                    nodes.</span>
            </div>
        @endif
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

        // បង្ហាញផ្ទាំង Alert ពេលជោគជ័យ
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Operation Completed',
                text: "{!! session('success') !!}",
                timer: 2500,
                showConfirmButton: false,
                position: 'center',
                customClass: {
                    popup: 'animated fadeInDown rounded-4 shadow'
                }
            });
        @endif

        // បង្ហាញផ្ទាំង Alert ពេលមានបញ្ហា ឬការហាមឃាត់
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Action Denied',
                text: "{!! session('error') !!}",
                timer: 3000,
                showConfirmButton: true,
                confirmButtonColor: '#ef4444',
                customClass: {
                    popup: 'animated fadeInDown rounded-4 shadow'
                }
            });
        @endif
    </script>
@endsection
