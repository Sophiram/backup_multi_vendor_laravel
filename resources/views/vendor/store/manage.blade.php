@extends('vendor.layouts.layout')

@section('vendor_page_title')
    Manage Store - Vendor Panel
@endsection

@section('vendor_layout')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark small text-uppercase tracking-wider">All Store Created By
                        You</h5>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 m-3 mb-0">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 m-3 mb-0">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card-body p-0 mt-3">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0 text-nowrap">
                            <thead class="table-light border-bottom text-secondary"
                                style="font-size: 12px; font-weight: 700;">
                                <tr>
                                    <th class="ps-4 py-3" style="width: 60px;">#</th>
                                    <th class="py-3">Store Name</th>
                                    <!-- បន្ថែម d-none d-md-table-cell ត្រង់នេះ -->
                                    <th class="py-3 d-none d-md-table-cell">Slug</th>
                                    <th class="py-3 d-none d-md-table-cell">Description</th>
                                    <th class="py-3 text-end pe-4" style="width: 140px;">Action</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 13.5px;">
                                @foreach ($stores as $store)
                                    <tr class="border-bottom">
                                        <td class="ps-4 fw-bold text-secondary">#{{ $store->id }}</td>

                                        <td>
                                            <span class="fw-bold text-dark store-name-text">{{ $store->store_name }}</span>
                                        </td>

                                        <!-- បន្ថែម d-none d-md-table-cell ត្រង់នេះ -->
                                        <td class="d-none d-md-table-cell">
                                            <span
                                                class="badge bg-light text-secondary border px-2 py-1 rounded store-slug-text"
                                                data-slug="{{ $store->slug }}">
                                                {{ $store->slug }}
                                            </span>
                                        </td>

                                        <!-- បន្ថែម d-none d-md-table-cell ត្រង់នេះ -->
                                        <td class="d-none d-md-table-cell">
                                            <small class="text-muted text-wrap d-block text-truncate store-details-text"
                                                style="max-width: 300px;">
                                                {{ $store->details }}
                                            </small>
                                        </td>

                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-2 align-items-center">
                                                <button type="button"
                                                    class="btn btn-sm btn-light border text-primary edit-action-btn rounded-2 open-edit-store-modal"
                                                    data-id="{{ $store->id }}" title="Edit Store">
                                                    <i class="align-middle" data-feather="edit"
                                                        style="width: 15px; height: 15px;"></i>
                                                </button>
                                                <form action="{{ route('delete.store', $store->id) }}" method="POST"
                                                    class="d-inline mb-0 delete-store-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-sm btn-light border text-danger delete-action-btn rounded-2 delete-store-btn"
                                                        style="padding: 0.4rem 0.6rem;" title="Delete Store">
                                                        <i class="align-middle" data-feather="trash"
                                                            style="width: 15px; height: 15px;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editStoreModal" tabindex="-1" aria-labelledby="editStoreModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-white border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark" id="editStoreModalLabel">
                        <i class="bi bi-pencil-square text-primary me-2"></i>Edit Store Details
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="edit-store-form" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Store Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="store_name" id="modal_store_name"
                                    class="form-control shadow-none py-2" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Store Slug</label>
                                <input type="text" name="slug" id="modal_store_slug"
                                    class="form-control shadow-none py-2" placeholder="ទុកទទេដើម្បីបង្កើតស្វ័យប្រវត្តិ">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Store Details /
                                    Description</label>
                                <textarea name="details" id="modal_store_details" class="form-control shadow-none py-2" rows="4"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top p-3">
                        <button type="button" class="btn btn-secondary px-3 fw-bold rounded-2"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold rounded-2 shadow-sm">Update
                            Store</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .edit-action-btn:hover {
            background-color: #e3f2fd !important;
            color: #0d6efd !important;
        }

        .delete-action-btn:hover {
            background-color: #f8d7da !important;
            color: #dc3545 !important;
        }

        .text-light-muted {
            color: #ccc;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==========================================
            // ១. ផ្នែកគ្រប់គ្រងការលុប (SWEETALERT2 POP-UP)
            // ==========================================
            document.querySelectorAll('.delete-store-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    const form = this.closest('.delete-store-form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this store and its related products!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn btn-danger px-4 fw-bold me-2',
                            cancelButton: 'btn btn-secondary px-4 fw-bold'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // ==========================================
            // ២. ផ្នែកគ្រប់គ្រងការកែប្រែ (EDIT STORE IN MODAL)
            // ==========================================
            const editStoreModal = new bootstrap.Modal(document.getElementById('editStoreModal'));
            const editStoreForm = document.getElementById('edit-store-form');
            const modalStoreName = document.getElementById('modal_store_name');
            const modalStoreSlug = document.getElementById('modal_store_slug');

            document.querySelectorAll('.open-edit-store-modal').forEach(button => {
                button.addEventListener('click', function() {
                    const storeId = this.getAttribute('data-id');
                    const row = this.closest('tr');

                    // ទាញយកទិន្នន័យពីជួរតារាង Store
                    const storeName = row.querySelector('.store-name-text').textContent.trim();
                    const slug = row.querySelector('.store-slug-text').getAttribute('data-slug');
                    const details = row.querySelector('.store-details-text').textContent.trim();

                    // កំណត់ URL ទៅឱ្យ Form Action
                    editStoreForm.action = `/vendor/store/update/${storeId}`;

                    // ចាក់ទិន្នន័យចូលក្នុង ផ្ទាំង Pop-up Inputs
                    modalStoreName.value = storeName;
                    modalStoreSlug.value = slug;
                    document.getElementById('modal_store_details').value = details;

                    // បើកផ្ទាំង Pop-up Modal
                    editStoreModal.show();
                });
            });

            // ==========================================
            // ៣. មុខងារ Auto-Slug នៅក្នុងផ្ទាំង Modal
            // ==========================================
            modalStoreName.addEventListener('input', function() {
                let slug = this.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');

                modalStoreSlug.value = slug;
            });
        });
    </script>
@endsection
