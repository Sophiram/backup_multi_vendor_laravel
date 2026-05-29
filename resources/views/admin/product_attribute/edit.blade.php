@extends('admin.layouts.layout')

@section('admin_page_title')
    Edit Default Attribute - Admin Panel
@endsection

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1">Edit Default Attribute</h3>
                <p class="text-muted small mb-0">Modify and update global product variant specifications and properties.</p>
            </div>
            <div>
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm rounded-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-2">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h5 class="fw-bold text-dark mb-0">Modify Attribute Values</h5>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-3 shadow-sm"
                                role="alert">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                                    <strong class="text-danger small">Please resolve the following conflicts:</strong>
                                </div>
                                <ul class="mb-0 small ps-3 text-danger">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close small" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-3 shadow-sm d-flex align-items-center gap-2 text-success small"
                                role="alert">
                                <i class="fa-solid fa-circle-check text-success fs-5"></i>
                                <div>{{ session('success') }}</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('update.attribute', $attribute_info->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Product</label>
                                <select name="product_id" class="form-select rounded-3 py-2" required>
                                    @foreach (\App\Models\Product::all() as $product)
                                        <option value="{{ $product->id }}"
                                            {{ $attribute_info->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->product_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Attribute Type</label>
                                <select name="attribute_id" class="form-select rounded-3 py-2" required>
                                    @foreach (\App\Models\Attribute::all() as $attr)
                                        <option value="{{ $attr->id }}"
                                            {{ $attribute_info->attribute_id == $attr->id ? 'selected' : '' }}>
                                            {{ $attr->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Additional Price</label>
                                <input type="number" step="0.01" name="additional_price"
                                    class="form-control rounded-3 py-2" value="{{ $attribute_info->additional_price }}"
                                    required>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary rounded-3 py-2 fw-bold">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Update Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
