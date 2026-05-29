@extends('admin.layouts.layout')

@section('admin_page_title', 'Create Sub Category - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-3 px-md-4 py-4">
        <div class="mb-3">
            <a href="{{ route('subcategory.manage') }}" class="text-decoration-none text-muted small">
                <i data-lucide="arrow-left" style="width: 16px;"></i> Back to List
            </a>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <h5 class="fw-bold text-dark mb-0">Create Sub Category</h5>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <form action="{{ route('store.subcategory') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="subcategory_name" class="form-label fw-bold text-secondary small">Sub
                                        Category Name</label>
                                    <input type="text" name="subcategory_name" id="subcategory_name"
                                        class="form-control form-control-lg rounded-3 @error('subcategory_name') is-invalid @enderror"
                                        value="{{ old('subcategory_name') }}" placeholder="e.g. Laptops, Gaming PCs"
                                        required>
                                    @error('subcategory_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6 mb-3">
                                    <label for="category_id" class="form-label fw-bold text-secondary small">Parent
                                        Category</label>
                                    <select name="category_id" id="category_id"
                                        class="form-select form-select-lg rounded-3 @error('category_id') is-invalid @enderror"
                                        required>
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
                            </div>

                            <div class="d-flex flex-column-reverse flex-sm-row gap-2 justify-content-end mt-3">
                                <a href="{{ route('subcategory.manage') }}"
                                    class="btn btn-light btn-lg rounded-3 fw-bold px-4 w-100 w-sm-auto">Cancel</a>
                                <button type="submit"
                                    class="btn btn-primary btn-lg rounded-3 fw-bold px-4 w-100 w-sm-auto">
                                    <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Create
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
            lucide.createIcons();
        });

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Created!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3b82f6',
                timer: 2000
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                html: '{!! implode('<br>', $errors->all()) !!}',
                confirmButtonColor: '#d33'
            });
        @endif
    </script>
@endsection
