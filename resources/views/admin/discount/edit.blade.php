@extends('admin.layouts.layout')

@section('admin_page_title', 'Edit Discount')

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <h3 class="fw-bold mb-4">Edit Discount: {{ $discount->title }}</h3>

        <form action="{{ route('admin.discount.update', $discount->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card border-0 shadow-sm p-4 rounded-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Discount Title</label>
                        <input type="text" name="title" class="form-control" value="{{ $discount->title }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Coupon Code</label>
                        <input type="text" name="code" class="form-control" value="{{ $discount->code }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Discount Value</label>
                        <input type="number" name="value" class="form-control" value="{{ $discount->value }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ $discount->status ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$discount->status ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Discount</button>
                    <a href="{{ route('admin.discount.manage') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    
@endsection
