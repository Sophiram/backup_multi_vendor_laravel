@extends('admin.layouts.layout')

@section('admin_page_title', 'Settings - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <div class="mb-4">
            <h3 class="fw-bold text-slate-800 mb-1">Home Page Settings</h3>
            <p class="text-muted small">Configure your home page layout, featured promotions, and special offers.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="card-body">
                        {{-- Alerts --}}
                        @if ($errors->any())
                            <div class="alert alert-danger border-0 rounded-3 mb-3 small">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success border-0 rounded-3 mb-3 small d-flex align-items-center">
                                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('admin.homepagesetting.update', $homepagesetting->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Discounted Product</label>
                                    <select name="discounted_product_id" class="form-select select2 rounded-3 py-2">
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ $homepagesetting->discounted_product_id == $product->id ? 'selected' : '' }}>
                                                {{ $product->product_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Discount Percentage (%)</label>
                                    <input type="number" name="discount_percent"
                                        value="{{ $homepagesetting->discount_percent }}" class="form-control rounded-3 py-2"
                                        placeholder="e.g. 20">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Discount Heading</label>
                                <input type="text" name="discount_heading"
                                    value="{{ $homepagesetting->discount_heading }}" class="form-control rounded-3 py-2"
                                    placeholder="Summer Sale 2026">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Discount Sub Text</label>
                                <input type="text" name="discount_subheading"
                                    value="{{ $homepagesetting->discount_subheading }}" class="form-control rounded-3 py-2"
                                    placeholder="Up to 50% off on all items">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Featured Product 1</label>
                                    <select name="featured_product_1_id" class="form-select select2 rounded-3 py-2">
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ $homepagesetting->featured_product_1_id == $product->id ? 'selected' : '' }}>
                                                {{ $product->product_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Featured Product 2</label>
                                    <select name="featured_product_2_id" class="form-select select2 rounded-3 py-2">
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ $homepagesetting->featured_product_2_id == $product->id ? 'selected' : '' }}>
                                                {{ $product->product_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary rounded-3 py-2 fw-bold">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Update Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
