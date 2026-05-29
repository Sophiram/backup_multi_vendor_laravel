@extends('admin.layouts.layout')

@section('admin_page_title', 'Edit Category - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4 py-4">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title fw-bold mb-0">Edit Category</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('update.cat', $category_info->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="category_name" class="form-label fw-bold">Category Name</label>
                                <input type="text" name="category_name" id="category_name"
                                    class="form-control form-control-lg rounded-3 @error('category_name') is-invalid @enderror"
                                    value="{{ old('category_name', $category_info->category_name) }}" required>
                                @error('category_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('category.manage') }}"
                                    class="btn btn-outline-secondary btn-lg w-50 rounded-3">Cancel</a>
                                <button type="submit" class="btn btn-primary btn-lg w-50 rounded-3">
                                    <i data-lucide="refresh-cw" style="width: 18px; height: 18px;"></i> Update
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
        // បង្ហាញ Success Popup
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3b82f6',
                timer: 2000
            });
        @endif

        // បង្ហាញ Error Popup
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                html: '{!! implode('<br>', $errors->all()) !!}',
                confirmButtonColor: '#d33'
            });
        @endif
    </script>
@endsection
