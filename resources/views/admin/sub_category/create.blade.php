@extends('admin.layouts.layout')

@section('admin_page_title', 'Create Sub Category - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-3 px-md-4 py-4">
        <div class="mb-3">
            <a href="{{ route('subcategory.manage') }}"
                class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 hover-opacity">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Back to List
            </a>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="plus-circle" class="text-primary" style="width: 20px; height: 20px;"></i> Create
                            Sub Category
                        </h5>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <form action="{{ route('store.subcategory') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <label for="subcategory_name" class="form-label fw-semibold text-secondary small mb-2">
                                        Sub Category Name
                                    </label>
                                    <input type="text" name="subcategory_name" id="subcategory_name"
                                        class="form-control rounded-3 @error('subcategory_name') is-invalid @enderror"
                                        value="{{ old('subcategory_name') }}" placeholder="e.g. Laptops, Gaming PCs"
                                        required>
                                    @error('subcategory_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="category_id" class="form-label fw-semibold text-secondary small mb-2">
                                        Parent Category
                                    </label>
                                    <select name="category_id" id="category_id"
                                        class="form-select rounded-3 @error('category_id') is-invalid @enderror" required>
                                        <option value="" selected disabled>Select a parent category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="image" class="form-label fw-semibold text-secondary small mb-2">
                                        Sub Category Image
                                    </label>
                                    <input type="file" name="image" id="image"
                                        class="form-control rounded-3 @error('image') is-invalid @enderror"
                                        accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="status" class="form-label fw-semibold text-secondary small mb-2">
                                        Status
                                    </label>
                                    <select name="status" id="status"
                                        class="form-select rounded-3 @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div
                                class="d-flex flex-column-reverse flex-sm-row gap-2 justify-content-end mt-4 pt-3 border-top border-light">
                                <a href="{{ route('subcategory.manage') }}"
                                    class="btn btn-light rounded-3 fw-medium px-4 w-100 w-sm-auto">
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="btn btn-primary rounded-3 fw-medium px-4 w-100 w-sm-auto d-inline-flex align-items-center justify-content-center gap-2">
                                    <i data-lucide="save" style="width: 18px; height: 18px;"></i> Save Sub Category
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize Lucide Icons
            lucide.createIcons();
        });

        // Handle Success Session
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Created!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3b82f6',
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        // Handle Form Validation Errors
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                html: '<div class="text-start small text-secondary">{!! implode('<br>• ', $errors->all()) !!}</div>',
                confirmButtonColor: '#ef4444'
            });
        @endif
    </script>
@endsection
