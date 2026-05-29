@extends('admin.layouts.layout')

@section('admin_page_title')
    Manage Discount - Admin Panel
@endsection

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1">Manage Discounts</h3>
                <p class="text-muted small mb-0">Track, edit, and manage all your store promotional coupons and offers.</p>
            </div>

            <div class="d-flex">
                <a href="{{ route('admin.discount.create') }}"
                    class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-bold w-100 w-md-auto">
                    <i data-lucide="plus" style="width: 16px; margin-right: 4px;"></i> Create New Discount
                </a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-3" style="background-color: #f8fafc;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                            style="width: 42px; height: 42px;">
                            <i data-lucide="ticket" style="width: 20px;"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-bold text-uppercase d-block mb-0"
                                style="font-size: 0.72rem; letter-spacing: 0.5px;">Active Coupons</small>
                            <h4 class="fw-extrabold text-dark mb-0">12 Codes</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-3" style="background-color: #f8fafc;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center"
                            style="width: 42px; height: 42px;">
                            <i data-lucide="trending-up" style="width: 20px;"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-bold text-uppercase d-block mb-0"
                                style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Revenue Saved</small>
                            <h4 class="fw-extrabold text-dark mb-0">$3,450.20</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-3" style="background-color: #f8fafc;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-warning-subtle text-warning d-flex align-items-center justify-content-center"
                            style="width: 42px; height: 42px;">
                            <i data-lucide="clock" style="width: 20px;"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-bold text-uppercase d-block mb-0"
                                style="font-size: 0.72rem; letter-spacing: 0.5px;">Expiring Soon</small>
                            <h4 class="fw-extrabold text-dark mb-0">3 Codes</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <div class="row g-2 align-items-center justify-content-between">
                    <div class="col-12 col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i data-lucide="search" style="width: 16px;"></i>
                            </span>
                            <input type="text" class="form-control bg-light border-start-0"
                                placeholder="Search by coupon code or title...">
                        </div>
                    </div>
                    <div class="col-12 col-md-auto d-flex gap-2">
                        <select class="form-select form-select-sm bg-light text-muted">
                            <option value="">Filter Type</option>
                            <option value="percentage">Percentage</option>
                            <option value="fixed_amount">Fixed Amount</option>
                            <option value="free_shipping">Free Shipping</option>
                        </select>
                        <select class="form-select form-select-sm bg-light text-muted">
                            <option value="">Filter Status</option>
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                            <option value="scheduled">Scheduled</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="table-light text-uppercase font-monospace" style="font-size: 0.75rem;">
                        <tr>
                            <th class="ps-4 py-3 text-muted">Title & Code</th>
                            <th class="py-3 text-muted">Type</th>
                            <th class="py-3 text-muted">Value</th>
                            <th class="py-3 text-muted">Usage Limit</th>
                            <th class="py-3 text-muted">Duration</th>
                            <th class="py-3 text-muted">Status</th>
                            <th class="pe-4 py-3 text-end text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($discounts as $discount)
                            <tr>
                                <td class="ps-4">
                                    <span class="d-block fw-bold text-dark mb-0">{{ $discount->title }}</span>
                                    <span
                                        class="badge bg-light text-primary border border-primary-subtle px-2 py-0.5 text-uppercase"
                                        style="font-size: 0.75rem;">{{ $discount->code }}</span>
                                </td>
                                <td>{{ str_replace('_', ' ', ucfirst($discount->type)) }}</td>
                                <td class="fw-bold text-slate-900">
                                    {{ $discount->value }}{{ $discount->type == 'percentage' ? '%' : '$' }} Off
                                </td>
                                <td>
                                    <div class="small text-dark fw-semibold">{{ $discount->usage_count ?? 0 }} /
                                        {{ $discount->usage_limit_total ?? '∞' }}</div>
                                </td>
                                <td>
                                    <small class="d-block text-secondary fw-medium">Start:
                                        {{ $discount->start_date }}</small>
                                    <small class="d-block text-muted" style="font-size: 0.75rem;">End:
                                        {{ $discount->end_date ?? 'Unlimited' }}</small>
                                </td>
                                <td>{!! $discount->status_badge !!}</td>
                                <td class="pe-4 text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-light rounded-2 text-primary me-1 edit-btn"
                                            data-bs-toggle="modal" data-bs-target="#editDiscountModal"
                                            data-route="{{ route('admin.discount.update', $discount->id) }}"
                                            data-title="{{ $discount->title }}" data-code="{{ $discount->code }}"
                                            data-value="{{ $discount->value }}" data-status="{{ $discount->status }}">
                                            <i data-lucide="edit-3" style="width: 16px;"></i>
                                        </button>
                                        <form action="{{ route('admin.discount.destroy', $discount->id) }}" method="POST"
                                            class="d-inline delete-form">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-light rounded-2 text-danger delete-btn">
                                                <i data-lucide="trash-2" style="width: 16px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No discounts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="modal fade" id="editDiscountModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content border-0 shadow rounded-4">
                            <form id="editDiscountForm" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header border-bottom-0">
                                    <h5 class="modal-title fw-bold">Edit Discount</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Discount Title</label>
                                            <input type="text" name="title" id="edit_title" class="form-control"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Coupon Code</label>
                                            <input type="text" name="code" id="edit_code" class="form-control"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Discount Value</label>
                                            <input type="number" name="value" id="edit_value" class="form-control"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Status</label>
                                            <select name="status" id="edit_status" class="form-select">
                                                <option value="1">Active</option>
                                                <option value="0">Expired</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update Discount</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="card-footer bg-white py-3 border-top border-light d-flex align-items-center justify-content-between">
                <small class="text-muted">
                    Showing {{ $discounts->firstItem() ?? 0 }} to {{ $discounts->lastItem() ?? 0 }} of
                    {{ $discounts->total() }} entries
                </small>
                <nav aria-label="Discount pagination">
                    {{ $discounts->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // SweetAlert for Delete
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('.delete-form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Edit Modal Handling
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                // បញ្ជូន Route ទៅ Form
                document.getElementById('editDiscountForm').action = this.getAttribute('data-route');

                // បញ្ចូលទិន្នន័យចូល Input
                document.getElementById('edit_title').value = this.getAttribute('data-title');
                document.getElementById('edit_code').value = this.getAttribute('data-code');
                document.getElementById('edit_value').value = this.getAttribute('data-value');
                document.getElementById('edit_status').value = this.getAttribute('data-status');
            });
        });
    </script>
@endsection
