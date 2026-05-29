@extends('admin.layouts.layout')

@section('admin_page_title', 'Manage Sub Category - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1">Sub Category Management</h3>
                <p class="text-muted small mb-0">Manage all sub-category relationships.</p>
            </div>
            <a href="{{ route('subcategory.create') }}"
                class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-bold flex-shrink-100 w-sm-auto">
                <i data-lucide="plus" style="width: 16px;"></i> Add New
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h5 class="fw-bold text-dark mb-0">All Sub Categories</h5>
            </div>

            @if (session('success'))
                <div
                    class="mx-4 mt-3 alert alert-success alert-dismissible fade show border-0 rounded-3 d-flex align-items-center gap-2 text-success small shadow-sm">
                    <i data-lucide="check-circle" style="width: 18px;"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-sm" style="font-size: 0.9rem;">
                        <thead class="table-light text-uppercase font-monospace" style="font-size: 0.75rem;">
                            <tr>
                                <th class="ps-4 py-3 text-muted">#</th>
                                <th class="py-3 text-muted">Sub Category Name</th>
                                <th class="py-3 text-muted">Parent Category</th>
                                <th class="pe-4 py-3 text-end text-muted">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subcategories as $subcat)
                                <tr>
                                    <td class="ps-4 font-monospace text-secondary">{{ $loop->iteration }}</td>
                                    <td><span class="fw-bold text-dark">{{ $subcat->subcategory_name }}</span></td>
                                    <td>
                                        <span class="badge bg-light text-secondary border px-2 py-1 rounded-2">
                                            {{ $subcat->category->category_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button"
                                                class="btn btn-light btn-sm rounded-2 text-primary edit-btn"
                                                data-id="{{ $subcat->id }}" data-name="{{ $subcat->subcategory_name }}"
                                                data-cat-id="{{ $subcat->category_id }}"
                                                data-url="{{ route('update.subcat', $subcat->id) }}"> <i
                                                    data-lucide="edit-3" style="width: 16px;"></i>
                                            </button>
                                            <form action="{{ route('delete.subcat', $subcat->id) }}" method="POST"
                                                class="d-inline">
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
                                    <td colspan="4" class="text-center py-4 text-muted">No sub-categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="modal fade" id="editSubCategoryModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 rounded-4 shadow">
                                <form id="editForm" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Edit Sub Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-secondary">Sub Category Name</label>
                                            <input type="text" name="subcategory_name" id="edit_name"
                                                class="form-control form-control-lg rounded-3 @error('subcategory_name') is-invalid @enderror"
                                                required>
                                            @error('subcategory_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-secondary">Parent Category</label>
                                            <select name="category_id" id="edit_category_id"
                                                class="form-select form-select-lg rounded-3" required>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->category_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-3"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary rounded-3 px-4">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <script>
        // អនុវត្តសម្រាប់ Lucide Icons
        document.addEventListener("DOMContentLoaded", function() {
            lucide.createIcons();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
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

        // បង្ហាញ Success Message ប្រសិនបើមាន
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        // Edit Logic (Modal)
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                let url = this.getAttribute('data-url'); // យក URL ដែលបាន generate មកពី PHP
                let name = this.getAttribute('data-name');
                let catId = this.getAttribute('data-cat-id');

                document.getElementById('editForm').action = url; // កំណត់ action ទៅតាម URL នោះ
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_category_id').value = catId;

                new bootstrap.Modal(document.getElementById('editSubCategoryModal')).show();
            });
        });
    </script>

    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('editSubCategoryModal'));
                myModal.show();
            });
        </script>
    @endif


@endsection
