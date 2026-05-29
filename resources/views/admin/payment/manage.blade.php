@extends('admin.layouts.layout')

@section('admin_page_title', 'Manage Payment - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h3 class="fw-bold text-dark mb-1">Payment Methods & Gateways</h3>
                <p class="text-muted small mb-0">Configure and manage your payment options and payment gateways.</p>
            </div>
            <div>
                <a href="{{ route('admin.payment.add') }}" class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-bold w-100">
                    <i class="fa-solid fa-plus me-1"></i> Add Payment Method
                </a>
            </div>
        </div>

        <h5 class="fw-bold text-dark mb-3">All Payment Methods</h5>

        <!-- Grid Section -->
        <div class="row g-4 mb-5">
            @foreach ($paymentMethods as $method)
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 position-relative">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-light p-2 rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 60px; height: 40px; overflow: hidden;">
                                @if ($method->logo)
                                    <img src="{{ asset('storage/' . $method->logo) }}" alt="Logo"
                                        style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                @else
                                    <span
                                        class="fw-bold text-primary font-monospace small">{{ substr($method->name, 0, 3) }}</span>
                                @endif
                            </div>
                            <div class="form-check form-switch p-0 m-0">
                                <input class="form-check-input ms-0 shadow-none" type="checkbox" role="switch"
                                    {{ $method->status ? 'checked' : '' }} disabled>
                            </div>
                        </div>

                        <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $method->name }}">{{ $method->name }}
                        </h6>
                        <p class="text-muted small mb-3">{{ ucfirst(str_replace('_', ' ', $method->type)) }}</p>

                        <div class="mt-auto d-flex justify-content-between align-items-center border-top border-light pt-3">
                            <span
                                class="badge {{ $method->status ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} border px-2 py-1 rounded-pill"
                                style="font-size: 0.7rem;">
                                {{ $method->status ? 'Active' : 'Disabled' }}
                            </span>
                            <button type="button" class="btn btn-sm btn-light rounded-2 text-secondary fw-semibold"
                                onclick="editPayment(
            '{{ $method->id }}',
            '{{ $method->name }}',
            '{{ $method->type }}'
        )">
                                <i class="fa-solid fa-gear me-1"></i> Configure
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editPaymentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editPaymentForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Payment Method</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Method Name</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Payment Type</label>
                                <select name="type" id="edit_type" class="form-select" required>
                                    <option value="direct_integration">Direct Integration (API)</option>
                                    <option value="manual_bank">Manual Bank Account</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Update Logo</label>
                                <input type="file" name="logo" class="form-control">
                                <small class="text-muted">Leave blank to keep existing logo.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $paymentMethods->links() }}
        </div>
    </div>
    <script>
        function editPayment(id, name, type) {
            // កំណត់ URL
            document.getElementById('editPaymentForm').action = '/admin/payment/update/' + id;

            // បំពេញទិន្នន័យចូល Input
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_type').value = type;

            // បើក Modal
            var myModal = new bootstrap.Modal(document.getElementById('editPaymentModal'));
            myModal.show();
        }

        // ប្រើ SweetAlert នៅពេលជោគជ័យ
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false,
                position: 'center'
            });
        @endif
    </script>
@endsection
