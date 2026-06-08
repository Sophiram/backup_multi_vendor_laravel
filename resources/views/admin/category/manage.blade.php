@extends('admin.layouts.layout')

@section('admin_page_title', 'Manage Category - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-2 py-2">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">Manage Categories</h4>
                <p class="text-muted small mb-0">View and organize all product categories.</p>
            </div>
            <a href="{{ route('category.create') }}"
                class="btn btn-primary rounded-3 px-3 py-2 fw-medium d-inline-flex align-items-center gap-2">
                <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Add Category
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h5 class="fw-bold text-dark mb-0">All Categories</h5>
            </div>

            @if (session('success'))
                <div
                    class="mx-4 mt-3 alert alert-success alert-dismissible fade show border-0 rounded-3 d-flex align-items-center gap-2 text-success small shadow-sm">
                    <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                        <thead class="table-light text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                            <tr>
                                <th class="ps-4 py-3 text-muted fw-bold" style="width: 80px;">#</th>
                                <th class="py-3 text-muted fw-bold" style="width: 100px;">Image</th>
                                <th class="py-3 text-muted fw-bold">Category Name</th>
                                <th class="py-3 text-muted fw-bold" style="width: 120px;">Status</th>
                                <th class="pe-4 py-3 text-end text-muted fw-bold" style="width: 150px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $index => $cat)
                                <tr>
                                    <td class="ps-4 font-monospace text-secondary">{{ $index + 1 }}</td>

                                    <td>
                                        @if ($cat->image)
                                            <img src="{{ asset($cat->image) }}" alt="{{ $cat->category_name }}"
                                                class="rounded-3 border shadow-sm shadow-sm"
                                                style="width: 45px; height: 45px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded-3 border d-flex align-items-center justify-content-center"
                                                style="width: 45px; height: 45px;">
                                                <i data-lucide="image" class="text-muted"
                                                    style="width: 18px; height: 18px;"></i>
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="fw-semibold text-dark">{{ $cat->category_name }}</span>
                                    </td>

                                    <td>
                                        @if ($cat->status == 'active')
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-2">Active</span>
                                        @else
                                            <span
                                                class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-2">Inactive</span>
                                        @endif
                                    </td>

                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button"
                                                class="btn btn-sm btn-light border text-primary rounded-2 p-2 d-inline-flex align-items-center"
                                                data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                                                data-id="{{ $cat->id }}" data-name="{{ $cat->category_name }}"
                                                data-status="{{ $cat->status }}" title="Edit">
                                                <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                                            </button>
                                            <form action="{{ route('delete.cat', $cat->id) }}" method="POST"
                                                class="delete-form d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-sm btn-light border delete-btn text-danger rounded-2 p-2 d-inline-flex align-items-center"
                                                    title="Delete">
                                                    <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i data-lucide="inbox" class="d-block mx-auto mb-2 text-secondary"
                                            style="width: 32px; height: 32px;"></i>
                                        No categories found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <form id="editCategoryForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2"
                            id="editCategoryModalLabel">
                            <i data-lucide="edit" class="text-primary" style="width: 20px; height: 20px;"></i> Edit Category
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label for="edit_category_name"
                                class="form-label fw-semibold small text-secondary mb-2">Category Name</label>
                            <input type="text" name="category_name" id="edit_category_name"
                                class="form-control rounded-3" placeholder="Enter category name" required>
                        </div>

                        <div class="mb-4">
                            <label for="edit_category_image"
                                class="form-label fw-semibold small text-secondary mb-2">Category Image</label>
                            <input type="file" name="image" id="edit_category_image" class="form-control rounded-3"
                                accept="image/*">
                            <div class="form-text text-muted small">Leave blank if you don't want to change the current
                                image.</div>
                        </div>

                        <div class="mb-1">
                            <label for="edit_category_status"
                                class="form-label fw-semibold small text-secondary mb-2">Status</label>
                            <select name="status" id="edit_category_status" class="form-select rounded-3" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-header border-0 d-flex justify-content-end gap-2 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light rounded-3 fw-medium"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4 fw-medium">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize Lucide Icons
            lucide.createIcons();

            // មុខងារលុបដោយប្រើ SweetAlert2
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('.delete-form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this category!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });

        // បង្ហាញ Success Popup ប្រសិនបើមាន Session
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        // Logic ពេលបើក Modal ផ្ទេរទិន្នន័យចាស់ចូល Input នីមួយៗ
        const editModal = document.getElementById('editCategoryModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const status = button.getAttribute('data-status'); // 💡 ចាប់យកតម្លៃ Status ចាស់ពីប៊ូតុង

                const form = document.getElementById('editCategoryForm');
                form.action = "{{ route('update.cat', ':id') }}".replace(':id', id);

                const input = document.getElementById('edit_category_name');
                input.value = name;

                const statusSelect = document.getElementById(
                    'edit_category_status'); // 💡 បញ្ចូលតម្លៃទៅឱ្យ Select Option
                statusSelect.value = status;

                // សម្អាត Input file រាល់ពេលបើក Modal ថ្មី
                document.getElementById('edit_category_image').value = '';

                // Re-trigger lucide icons inside modal
                setTimeout(() => {
                    lucide.createIcons();
                }, 150);
            });
        }
    </script>
@endsection
