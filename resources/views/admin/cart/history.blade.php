@extends('admin.layouts.layout')

@section('admin_page_title', 'History Cart - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div class="mb-3 mb-md-0">
                <h3 class="fw-bold text-slate-800 mb-1">History Cart Management</h3>
                <p class="text-muted small mb-0">Review and manage customer cart history and abandoned checkouts.</p>
            </div>

            <div>
                <a href="{{ route('admin.cart.export', request()->query()) }}"
                    class="btn btn-sm btn-success rounded-3 shadow-sm">
                    <i class="fa-solid fa-file-excel me-1"></i> Export Excel
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.75rem;">Total
                        Abandoned</small>
                    <h4 class="fw-extrabold text-danger mb-0">{{ number_format($totalAbandoned) }}</h4>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.75rem;">Converted to
                        Order</small>
                    <h4 class="fw-extrabold text-success mb-0">{{ number_format($totalConverted) }}</h4>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <form action="{{ route('admin.cart.history') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-12 col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i
                                    class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-start-0"
                                placeholder="Search customer..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-auto">
                        <select name="status" class="form-select form-select-sm bg-light text-muted"
                            onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Cart
                            </option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Converted
                            </option>
                            <option value="abandoned" {{ request('status') == 'abandoned' ? 'selected' : '' }}>Abandoned
                            </option>
                        </select>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase font-monospace" style="font-size: 0.75rem;">
                        <tr>
                            <th class="ps-4 py-3">Cart ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Updated</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($carts as $cart)
                            @php
                                $statusClass =
                                    [
                                        'converted' => 'bg-success-subtle text-success',
                                        'abandoned' => 'bg-danger-subtle text-danger',
                                        'active' => 'bg-warning-subtle text-warning',
                                    ][$cart->status] ?? 'bg-secondary-subtle text-secondary';
                            @endphp
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">#CRT-{{ $cart->id }}</td>
                                <td>
                                    <span class="d-block fw-bold text-dark">{{ $cart->user->name ?? 'Guest' }}</span>
                                    <small class="text-muted">{{ $cart->user->email ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $cart->items_count }} Items</td>
                                <td class="fw-bold">${{ number_format($cart->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ $statusClass }} rounded-pill px-2 py-1">
                                        {{ ucfirst($cart->status) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $cart->updated_at->format('d M, Y') }}</td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                      
                                        <button type="button" class="btn btn-sm btn-light text-warning"
                                            onclick="openEditModal('{{ $cart->id }}', '{{ $cart->status }}')"
                                            title="Edit Cart">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('admin.cart.delete', $cart->id) }}" method="POST"
                                            id="delete-form-{{ $cart->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-light text-danger"
                                                onclick="confirmDelete('{{ $cart->id }}')" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white py-3">
                {{ $carts->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editCartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editCartForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">Edit Cart Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Update Status</label>
                            <select name="status" id="edit-status" class="form-select" required>
                                <option value="active">Active Cart</option>
                                <option value="completed">Converted</option>
                                <option value="abandoned">Abandoned</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        function openEditModal(id, currentStatus) {
            const form = document.getElementById('editCartForm');
            form.action = '/admin/cart/update/' + id; // ពិនិត្យមើល URL នេះអោយត្រូវតាម Route របស់អ្នក
            document.getElementById('edit-status').value = currentStatus;
            new bootstrap.Modal(document.getElementById('editCartModal')).show();
        }
    </script>
@endsection
