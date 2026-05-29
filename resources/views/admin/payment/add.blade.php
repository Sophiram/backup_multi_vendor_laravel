@extends('admin.layouts.layout')

@section('admin_page_title', 'Add Payment Method - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <div class="mb-4">
            <h3 class="fw-bold text-dark">Add New Payment Method</h3>
            <p class="text-muted">Please fill in the information below to add a new payment method to the system.</p>
        </div>
        {{-- ផ្នែកបង្ហាញសារជោគជ័យ ឬកំហុស --}}
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    timer: 2000,
                    showConfirmButton: false
                });
            </script>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm border-0 p-4">
            <form action="{{ route('admin.payment.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-12 col-md-6 mb-3"> <label class="form-label fw-bold">Method Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. ABA Pay, Wing, Stripe"
                            required>
                        <small class="text-muted">The name to be displayed to customers.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Payment Type</label>
                        <select name="type" class="form-select" required>
                            <option value="" disabled selected>Select type...</option>
                            <option value="direct_integration">Direct Integration (API)</option>
                            <option value="manual_bank">Manual Bank Account</option>
                        </select>
                        <small class="text-muted">Select the payment integration mode.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Upload Logo</label>
                        <input type="file" name="logo" class="form-control">
                        <small class="text-muted">Small image (JPG, PNG) for display during checkout.</small>
                    </div>
                </div>

                <div class="mt-4 d-grid d-md-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        <i class="fa-solid fa-save me-1"></i> Save Payment Method
                    </button>
                    <a href="{{ route('admin.payment.manage') }}" class="btn btn-outline-secondary px-4 py-2">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

@endsection
