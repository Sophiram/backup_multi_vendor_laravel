@extends('admin.layouts.layout')

@section('admin_page_title', 'Create Discount - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1">Create New Discount</h3>
                <p class="text-muted small mb-0">Set up coupon codes or automatic promotional discounts.</p>
            </div>

            <div class="d-flex">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm rounded-3 w-100 w-sm-auto">
                    <i data-lucide="arrow-left" style="width: 16px; margin-right: 4px;"></i> Back to List
                </a>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.discount.store') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <h5 class="fw-bold mb-3 text-dark">General Information</h5>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Discount Title</label>
                            <input type="text" class="form-control rounded-3 py-2" name="title"
                                value="{{ old('title') }}" placeholder="e.g., Summer Flash Sale" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">Coupon Code</label>
                                <div class="input-group">
                                    <input type="text" class="form-control rounded-start-3 py-2 text-uppercase"
                                        id="coupon_code" name="code" value="{{ old('code') }}"
                                        placeholder="e.g., SUMMER50">
                                    <button class="btn btn-outline-primary" type="button"
                                        onclick="generateCode()">Generate</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">Discount Type</label>
                                <select class="form-select rounded-3 py-2" name="type" required>
                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>
                                        Percentage (%)</option>
                                    <option value="fixed_amount" {{ old('type') == 'fixed_amount' ? 'selected' : '' }}>Fixed
                                        Amount ($)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">Discount Value</label>
                                <input type="number" step="0.01" class="form-control rounded-3 py-2" name="value"
                                    value="{{ old('value') }}" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">Minimum Requirement ($)</label>
                                <input type="number" step="0.01" class="form-control rounded-3 py-2"
                                    name="min_requirement" value="{{ old('min_requirement') }}" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h5 class="fw-bold mb-3 text-dark">Active Schedule</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">Start Date</label>
                                <input type="datetime-local" class="form-control rounded-3 py-2" name="start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">End Date</label>
                                <input type="datetime-local" class="form-control rounded-3 py-2" name="end_date">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <h5 class="fw-bold mb-3 text-dark">Status</h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="status" name="status"
                                checked>
                            <label class="form-check-label small fw-bold" for="status">Active</label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-3 py-2 fw-bold">
                            <i data-lucide="save" style="width: 16px;"></i> Save Discount
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // បង្ហាញ Success Popup នៅពេលបង្កើតជោគជ័យ
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Discount Created!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3b82f6',
                timer: 2500
            });
        @endif

        // បង្ហាញ Error Popup ប្រសិនបើមាន Validation Error
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: '{{ $errors->first() }}',
                confirmButtonColor: '#d33'
            });
        @endif

        // មុខងារ Generate Code
        function generateCode() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let code = 'DISC-';
            for (let i = 0; i < 6; i++) {
                code += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('coupon_code').value = code;
        }

        document.addEventListener("DOMContentLoaded", function() {
            lucide.createIcons();
        });
    </script>

@endsection
