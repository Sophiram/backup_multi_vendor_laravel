@extends('admin.layouts.layout')

@section('admin_page_title', 'Manage Category - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4 py-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1">Manage Categories</h3>
                <p class="text-muted small mb-0">View and organize all product categories.</p>
            </div>
            <a href="{{ route('category.create') }}" class="btn btn-primary rounded-3 px-4 py-2 fw-bold">
                <i data-lucide="plus" style="width: 16px;"></i> Add Category
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase font-monospace" style="font-size: 0.75rem;">
                        <tr>
                            <th class="ps-4 py-3 text-muted">#</th>
                            <th class="py-3 text-muted">Category Name</th>
                            <th class="pe-4 py-3 text-end text-muted">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $index => $cat)
                            <tr>
                                <td class="ps-4 text-secondary font-monospace">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $cat->category_name }}</td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-light btn-sm rounded-2 text-primary"
                                            data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                                            data-id="{{ $cat->id }}" data-name="{{ $cat->category_name }}">
                                            <i data-lucide="edit-3" style="width: 16px;"></i>
                                        </button>
                                        <form action="{{ route('delete.cat', $cat->id) }}" method="POST"
                                            class="delete-form d-inline">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                class="btn btn-light btn-sm rounded-2 text-danger delete-btn">
                                                <i data-lucide="trash-2" style="width: 16px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header border-bottom-0">
                                <h5 class="modal-title fw-bold" id="editCategoryModalLabel">Edit Category</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form id="editCategoryForm" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="edit_category_name" class="form-label fw-bold">Category Name</label>
                                        <input type="text" name="category_name" id="edit_category_name"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary fw-bold">Update Category</button>
                                </div>
                            </form>
                        </div>
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

        // មុខងារលុបដោយប្រើ SweetAlert2
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('.delete-form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // បង្ហាញ Success Popup
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        const editModal = document.getElementById('editCategoryModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');

            const form = document.getElementById('editCategoryForm');

            // ប្រើ route() របស់ Laravel ជំនួសការសរសេរ Path ផ្ទាល់
            // ចំណាំ៖ ប្រសិនបើ route របស់អ្នកត្រូវការ parameter ឈ្មោះអ្វី សូមដាក់ឱ្យត្រូវ
            form.action = "{{ route('update.cat', ':id') }}".replace(':id', id);

            const input = document.getElementById('edit_category_name');
            input.value = name;
        });
    </script>
@endsection
