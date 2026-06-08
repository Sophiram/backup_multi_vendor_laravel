@extends('admin.layouts.layout')

@section('admin_page_title', 'Manage Sub Category - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-2 py-2">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">Sub Category Management</h4>
                <p class="text-muted small mb-0">Manage and organize all sub-category relationships.</p>
            </div>
            <a href="{{ route('subcategory.create') }}"
                class="btn btn-primary rounded-3 px-3 py-2 fw-medium d-inline-flex align-items-center gap-2">
                <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Add New Sub Category
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h5 class="fw-bold text-dark mb-0">All Sub Categories</h5>
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
                                <th class="py-3 text-muted fw-bold">Sub Category Name</th>
                                <th class="py-3 text-muted fw-bold">Parent Category</th>
                                <th class="py-3 text-muted fw-bold" style="width: 120px;">Status</th>
                                <th class="pe-4 py-3 text-end text-muted fw-bold" style="width: 150px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subcategories as $subcat)
                                <tr>
                                    <td class="ps-4 font-monospace text-secondary">{{ $loop->iteration }}</td>

                                    <td>
                                        @if ($subcat->image)
                                            <img src="{{ asset($subcat->image) }}" alt="{{ $subcat->subcategory_name }}"
                                                class="rounded-3 border shadow-sm"
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
                                        <span class="fw-semibold text-dark">{{ $subcat->subcategory_name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-2 fw-medium">
                                            <i data-lucide="folder" class="me-1 text-secondary"
                                                style="width: 14px; height: 14px; vertical-align: middle;"></i>
                                            {{ $subcat->category->category_name ?? 'N/A' }}
                                        </span>
                                    </td>

                                    <td>
                                        @if ($subcat->status == 'active')
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
                                                class="btn btn-sm btn-light border edit-btn text-primary rounded-2 p-2 d-inline-flex align-items-center"
                                                data-id="{{ $subcat->id }}" data-name="{{ $subcat->subcategory_name }}"
                                                data-cat-id="{{ $subcat->category_id }}"
                                                data-status="{{ $subcat->status }}"
                                                data-url="{{ route('update.subcat', $subcat->id) }}" title="Edit">
                                                <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                                            </button>
                                            <form action="{{ route('delete.subcat', $subcat->id) }}" method="POST"
                                                class="d-inline form-delete">
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
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i data-lucide="inbox" class="d-block mx-auto mb-2 text-secondary"
                                            style="width: 32px; height: 32px;"></i>
                                        No sub-categories found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSubCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                            <i data-lucide="edit" class="text-primary" style="width: 20px; height: 20px;"></i> Edit Sub
                            Category
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Sub Category Name</label>
                            <input type="text" name="subcategory_name" id="edit_name"
                                class="form-control rounded-3 @error('subcategory_name') is-invalid @enderror"
                                placeholder="Enter sub category name" required>
                            @error('subcategory_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Parent Category</label>
                            <select name="category_id" id="edit_category_id" class="form-select rounded-3" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit_image" class="form-label fw-semibold small text-secondary">Sub Category
                                Image</label>
                            <input type="file" name="image" id="edit_image" class="form-control rounded-3"
                                accept="image/*">
                            <div class="form-text text-muted small">Leave blank if you don't want to change the current
                                image.</div>
                        </div>

                        <div class="mb-1">
                            <label for="edit_status" class="form-label fw-semibold small text-secondary">Status</label>
                            <select name="status" id="edit_status" class="form-select rounded-3" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-header border-0 d-flex justify-content-end gap-2 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light rounded-3 fw-medium"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4 fw-medium">Save Changes</button>
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

            // SweetAlert2 Delete Confirmation
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this subcategory!",
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

            // Edit Button Trigger Logic (Modal Setup)
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    let url = this.getAttribute('data-url');
                    let name = this.getAttribute('data-name');
                    let catId = this.getAttribute('data-cat-id');
                    let status = this.getAttribute('data-status'); // 💡 ចាប់យកតម្លៃ Status ចាស់

                    document.getElementById('editForm').action = url;
                    document.getElementById('edit_name').value = name;
                    document.getElementById('edit_category_id').value = catId;
                    document.getElementById('edit_status').value =
                        status; // 💡 បញ្ចូលទៅក្នុង Select Option

                    // សម្អាតតម្លៃរូបភាពចាស់ក្នុង Input File រាល់ពេលបើកផ្ទាំងថ្មី
                    document.getElementById('edit_image').value = '';

                    const editModal = new bootstrap.Modal(document.getElementById(
                        'editSubCategoryModal'));
                    editModal.show();

                    // Re-trigger lucide within modal if any
                    setTimeout(() => {
                        lucide.createIcons();
                    }, 150);
                });
            });
        });
    </script>

    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var errorModal = new bootstrap.Modal(document.getElementById('editSubCategoryModal'));
                errorModal.show();
            });
        </script>
    @endif
@endsection
