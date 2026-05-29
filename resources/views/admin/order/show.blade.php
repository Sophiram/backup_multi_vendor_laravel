@extends('admin.layouts.layout')

@section('admin_page_title', 'Order Details - ' . ($order->order_number ?? 'N/A'))

@section('admin_layout')
    <div class="container-fluid px-4 py-4">
        <a href="{{ route('admin.order.history') }}" class="text-decoration-none text-muted mb-3 d-inline-block">
            <i class="fa-solid fa-arrow-left"></i> Back to Orders
        </a>

        @if (isset($order))
            <div class="row">
                {{-- Items Table --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div
                            class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">Items (#{{ $order->order_number }})</h5>
                            <span class="badge bg-primary-subtle text-primary">{{ ucfirst($order->status) }}</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Product</th>
                                            <th>Quantity</th>
                                            <th class="text-end pe-4">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->items as $item)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-semibold">
                                                        {{ $item->product->product_name ?? 'Product Deleted' }}</div>
                                                </td>
                                                <td>{{ $item->quantity }}</td>
                                                <td class="text-end pe-4">${{ number_format($item->price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="2" class="text-end fw-bold ps-4">Total Amount</td>
                                            <td class="text-end pe-4 fw-bold">${{ number_format($order->total_amount, 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">Customer Information</h6>
                            <p class="mb-1"><strong>Name:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                            <p class="mb-0 text-muted">{{ $order->user->email ?? 'N/A' }}</p>
                            <hr>
                            <h6 class="fw-bold mb-3">Shipping Address</h6>
                            <p class="text-muted small">{{ $order->shipping_address ?? 'No address provided' }}</p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 text-uppercase text-muted" style="font-size: 0.75rem;">Payment Status
                            </h6>
                            <form action="{{ route('admin.order.payment.update', $order->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="input-group">
                                    <select name="payment_status" class="form-select" required>
                                        <option value="pending"
                                            {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>
                                            Paid</option>
                                        <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>
                                            Failed</option>
                                    </select>
                                    <button type="submit" class="btn btn-success">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-danger">Order not found.</div>
        @endif
    </div>

    {{-- SweetAlert Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                timer: 2500,
                showConfirmButton: false,
                // ដាក់កូដទាំងនេះដើម្បីឱ្យវាបង្ហាញនៅចំកណ្តាលអេក្រង់
                position: 'center',
                toast: false, // ដាក់ជា false ដើម្បីឱ្យវាលេចធ្លោនៅកណ្តាល
                background: '#ffffff',
                customClass: {
                    popup: 'animated fadeInDown'
                }
            });
        </script>
    @endif
@endsection
