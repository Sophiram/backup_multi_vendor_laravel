@extends('vendor.layouts.layout')

@section('vendor_page_title')
    Order Details #{{ $order->order_number }}
@endsection

@section('vendor_layout')
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="fw-bolder text-dark mb-1 d-flex align-items-center gap-2">
                    <i data-feather="file-text" style="width: 24px; height: 24px;" class="text-primary"></i>
                    Order Details
                </h3>
                <p class="text-muted small mb-0">
                    Order Number:
                    <span class="fw-bold text-primary bg-soft-primary px-2 py-1 rounded mt-1 d-inline-block">
                        #{{ $order->order_number }}
                    </span>
                </p>
            </div>
            <a href="{{ route('vendor.orders.history') }}"
                class="btn btn-light border shadow-sm rounded-3 fw-medium d-flex align-items-center gap-2 text-secondary transition-all">
                <i data-feather="arrow-left" style="width: 16px; height: 16px;"></i> Back to History
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark fs-6 text-uppercase" style="letter-spacing: 0.5px;">
                        Items in this Order
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap custom-table">
                            <thead class="bg-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                                <tr>
                                    <th class="ps-4 py-3 border-bottom-0 fw-semibold">Product</th>
                                    <th class="py-3 border-bottom-0 fw-semibold text-end">Price</th>
                                    <th class="py-3 border-bottom-0 fw-semibold text-center">Qty</th>
                                    <th class="pe-4 py-3 border-bottom-0 fw-semibold text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="rounded-3 bg-light border p-1 d-flex align-items-center justify-content-center overflow-hidden"
                                                    style="width: 54px; height: 54px;">
                                                    @if ($item->product?->images?->first())
                                                        <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}"
                                                            class="w-100 h-100 object-fit-cover rounded-2"
                                                            alt="Product Image">
                                                    @else
                                                        <i data-feather="image" class="text-muted opacity-50"></i>
                                                    @endif
                                                </div>
                                                <div class="d-flex flex-column"
                                                    style="white-space: normal; min-width: 150px;">
                                                    <span
                                                        class="fw-bold text-dark lh-sm mb-1">{{ $item->product->product_name ?? 'Product Deleted' }}</span>
                                                    <span class="text-muted" style="font-size: 12px;">
                                                        <i data-feather="store" style="width: 10px; height: 10px;"
                                                            class="me-1"></i>
                                                        {{ $item->product->store->store_name ?? 'N/A' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 text-end fw-medium text-secondary font-monospace">
                                            ${{ number_format($item->price, 2) }}
                                        </td>
                                        <td class="py-3 text-center fw-bold text-dark">
                                            x{{ $item->quantity }}
                                        </td>
                                        <td class="pe-4 py-3 text-end fw-bold text-dark font-monospace">
                                            ${{ number_format($item->price * $item->quantity, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 fs-6 text-uppercase border-bottom pb-2" style="letter-spacing: 0.5px;">Order
                        Summary</h5>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary"
                            style="width: 40px; height: 40px;">
                            <i data-feather="user" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div>
                            <label class="text-muted small fw-medium d-block mb-1">Customer Name</label>
                            <span class="fw-bold text-dark">{{ $order->user->name ?? 'Unknown' }}</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary"
                            style="width: 40px; height: 40px;">
                            <i data-feather="activity" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div>
                            <label class="text-muted small fw-medium d-block mb-1">Current Status</label>
                            <span
                                class="badge rounded-pill fw-medium px-3 py-1 status-badge-{{ strtolower($order->status) }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 fs-6 text-uppercase border-bottom pb-2" style="letter-spacing: 0.5px;">Shipping
                        Info</h5>

                    @if ($order->shipping)
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="mb-3 d-flex flex-column gap-1">
                                <label class="text-muted small fw-medium">Shipping Company</label>
                                <span class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <i data-feather="truck" style="width: 14px; height: 14px;" class="text-secondary"></i>
                                    {{ $order->shipping->shippingCompany->name ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="mb-3 d-flex flex-column gap-1">
                                <label class="text-muted small fw-medium">Tracking Number</label>
                                <span
                                    class="fw-bold text-primary bg-white border px-2 py-1 rounded-2 d-inline-block text-truncate">
                                    {{ $order->shipping->tracking_number }}
                                </span>
                            </div>
                            <div class="d-flex flex-column gap-1 border-top pt-2 mt-2">
                                <label class="text-muted small fw-medium">Shipping Cost</label>
                                <span class="fw-bold text-success font-monospace">
                                    ${{ number_format($order->shipping->shipping_cost, 2) }}
                                </span>
                            </div>
                        </div>
                    @elseif ($order->status == 'processing')
                        <form action="{{ route('vendor.orders.updateStatus', $order->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="shipped">

                            <div class="mb-3">
                                <label class="small fw-semibold text-dark mb-1">Shipping Company <span
                                        class="text-danger">*</span></label>
                                <select name="shipping_company_id" class="form-select form-control-custom shadow-none"
                                    required onchange="updateCost(this)">
                                    <option value="">-- Select Company --</option>
                                    @foreach ($shippingCompanies as $company)
                                        <option value="{{ $company->id }}" data-fee="{{ $company->shipping_fee }}">
                                            {{ $company->name }} (${{ number_format($company->shipping_fee, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="small fw-semibold text-dark mb-1">Tracking Number <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="tracking_number"
                                    class="form-control form-control-custom shadow-none @error('tracking_number') is-invalid @enderror"
                                    placeholder="e.g. TRK123456789" required value="{{ old('tracking_number') }}">
                                @error('tracking_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="small fw-semibold text-dark mb-1">Calculated Cost ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">$</span>
                                    <input type="number" name="shipping_cost" id="shipping_cost"
                                        class="form-control form-control-custom border-start-0 bg-light shadow-none font-monospace fw-bold text-success"
                                        step="0.01" value="0.00" readonly required>
                                </div>
                            </div>

                            <button type="submit"
                                class="btn btn-premium w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2">
                                <i data-feather="send" style="width: 16px; height: 16px;"></i> Confirm Shipping
                            </button>
                        </form>
                    @else
                        <div class="text-center py-4 opacity-50">
                            <i data-feather="package" style="width: 40px; height: 40px;" class="mb-2 text-muted"></i>
                            <p class="text-muted small mb-0">No shipping info required yet.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <style>
        /* Form & Button Overrides */
        .btn-premium {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7) !important;
            color: #ffffff !important;
            border: none;
            border-radius: 8px !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-premium:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }

        .form-control-custom {
            border: 1px solid #dee2e6 !important;
            border-radius: 8px !important;
            padding: 0.6rem 1rem;
            transition: all 0.2s ease-in-out;
        }

        .form-control-custom:focus {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
        }

        /* Table Enhancements */
        .custom-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .custom-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Status Badges */
        .status-badge-completed {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .status-badge-pending {
            background-color: #fff3e0;
            color: #ef6c00;
            border: 1px solid #ffe0b2;
        }

        .status-badge-processing {
            background-color: #e0f7fa;
            color: #00838f;
            border: 1px solid #b2ebf2;
        }

        .status-badge-shipped {
            background-color: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
        }

        .status-badge-cancelled {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .bg-soft-primary {
            background-color: #e3f2fd !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Feather Icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });

        // Shipping Cost Calculation
        function updateCost(select) {
            const costInput = document.getElementById('shipping_cost');
            if (select.selectedIndex > 0) {
                const selectedOption = select.options[select.selectedIndex];
                const fee = selectedOption.getAttribute('data-fee');
                costInput.value = fee ? parseFloat(fee).toFixed(2) : '0.00';
            } else {
                costInput.value = '0.00';
            }
        }
    </script>
@endsection
