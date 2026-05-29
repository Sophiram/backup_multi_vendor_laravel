@extends('vendor.layouts.layout')

@section('vendor_page_title')
    Create New Store - Vendor Panel
@endsection

@section('vendor_layout')
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark small text-uppercase tracking-wider">
                        <i class="bi bi-shop text-primary me-2"></i>Create New Store
                    </h5>
                </div>

                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('create.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="store_name" class="form-label fw-bold text-secondary small text-uppercase">Store Name <span class="text-danger">*</span></label>
                                <input type="text" name="store_name" id="store_name"
                                    class="form-control shadow-none py-2 @error('store_name') is-invalid @enderror"
                                    placeholder="Zendo Store" value="{{ old('store_name') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="slug" class="form-label fw-bold text-secondary small text-uppercase">Store Slug</label>
                                <input type="text" name="slug" id="slug"
                                    class="form-control shadow-none py-2 @error('slug') is-invalid @enderror"
                                    placeholder="zendo-store" value="{{ old('slug') }}">
                                <small class="text-muted" style="font-size: 11.5px;">* The slug will be auto-generated from the store name.</small>
                            </div>

                            <div class="col-12">
                                <label for="details" class="form-label fw-bold text-secondary small text-uppercase">Store Details / Description</label>
                                <textarea name="details" id="details" rows="6"
                                    class="form-control shadow-none py-2 @error('details') is-invalid @enderror"
                                    placeholder="Describe your store layout, products, or rules...">{{ old('details') }}</textarea>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary fw-bold py-2 w-100 rounded-3 shadow-sm text-uppercase tracking-wider" style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                                    <i class="bi bi-plus-circle me-1"></i> Create Store
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const storeNameInput = document.getElementById('store_name');
            const slugInput = document.getElementById('slug');

            storeNameInput.addEventListener('input', function () {
                // បម្លែងអក្សរទៅជាអក្សរតូច, លុបចន្លោះមិនចាំបាច់ និងជំនួសដកឃ្លាដោយសញ្ញា (-)
                let slug = this.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '') // លុបតួអក្សរពិសេសក្រៅពីអក្សរ លេខ និងចន្លោះ
                    .replace(/\s+/g, '-')         // ជំនួសរាល់ដកឃ្លាដោយសញ្ញា -
                    .replace(/-+/g, '-');         // បង្រួមសញ្ញា - ដែលនៅជាប់គ្នាឱ្យសល់តែមួយ

                slugInput.value = slug;
            });
        });
    </script>
@endsection
