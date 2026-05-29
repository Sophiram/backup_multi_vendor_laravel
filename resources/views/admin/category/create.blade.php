@extends('admin.layouts.layout')

@section('admin_page_title', 'Create Category - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4 py-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h5 class="card-title fw-bold text-dark mb-0">Create New Category</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('store.category') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="category_name" class="form-label fw-bold text-secondary">Category Name</label>
                        <input type="text" name="category_name" id="category_name"
                            class="form-control form-control-lg rounded-3 @error('category_name') is-invalid @enderror"
                            placeholder="e.g. Electronics" required>
                        @error('category_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-bold px-5">
                        <i data-lucide="save" style="width: 18px; height: 18px;"></i> Save Category
                    </button>
                </form>
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
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3b82f6',
                timer: 2500
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: '{!! implode('<br>', $errors->all()) !!}',
                confirmButtonColor: '#d33'
            });
        @endif
    </script>
@endsection
