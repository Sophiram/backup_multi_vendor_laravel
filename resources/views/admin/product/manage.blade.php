@extends('admin.layouts.layout')

@section('admin_page_title')
    Manage Product - Admin Panel
@endsection

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1">Product Management</h3>
                <p class="text-muted small mb-0">Add, edit, track inventory, and manage all retail products available on your
                    store.</p>
            </div>

        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3" style="background-color: #f8fafc;">
                    <small class="text-muted fw-bold text-uppercase d-block mb-1"
                        style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Products</small>
                    <h4 class="fw-extrabold text-dark mb-0">{{ $data['totalProducts'] }}</h4>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3" style="background-color: #f8fafc;">
                    <small class="text-muted fw-bold text-uppercase d-block mb-1"
                        style="font-size: 0.72rem; letter-spacing: 0.5px;">Active Visible</small>
                    <h4 class="fw-extrabold text-success mb-0">{{ $data['activeProducts'] }}</h4>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3" style="background-color: #f8fafc;">
                    <small class="text-muted fw-bold text-uppercase d-block mb-1"
                        style="font-size: 0.72rem; letter-spacing: 0.5px;">Low Stock Alert</small>
                    <h4 class="fw-extrabold text-warning mb-0">{{ $data['lowStock'] }}</h4>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3" style="background-color: #f8fafc;">
                    <small class="text-muted fw-bold text-uppercase d-block mb-1"
                        style="font-size: 0.72rem; letter-spacing: 0.5px;">Out of Stock</small>
                    <h4 class="fw-extrabold text-danger mb-0">{{ $data['outOfStock'] }}</h4>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <div class="row g-2 align-items-center justify-content-between">
                    <div class="col-12 col-md-4">
                        <form method="GET" action="{{ route('product.manage') }}">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i data-lucide="search" style="width: 16px;"></i>
                                </span>
                                <input type="text" name="search" class="form-control bg-light border-start-0"
                                    placeholder="Search by product name, SKU..." value="{{ request('search') }}">
                            </div>
                        </form>
                    </div>
                    <div class="col-12 col-md-auto">
                        <form method="GET" action="{{ route('product.manage') }}" class="d-flex gap-2">
                            <select name="category_id" class="form-select form-select-sm bg-light text-muted"
                                onchange="this.form.submit()">
                                <option value="">Filter Category</option>
                                @foreach (\App\Models\Category::all() as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="stock_status" class="form-select form-select-sm bg-light text-muted"
                                onchange="this.form.submit()">
                                <option value="">Filter Stock Status</option>
                                <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In
                                    Stock</option>
                                <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>
                                    Low Stock</option>
                                <option value="out_of_stock"
                                    {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="table-light text-uppercase font-monospace" style="font-size: 0.75rem;">
                        <tr>
                            <th class="ps-4 py-3 text-muted">Product Details</th>
                            <th class="py-3 text-muted">SKU / Code</th>
                            <th class="py-3 text-muted">Category</th>
                            <th class="py-3 text-muted">Price</th>
                            <th class="py-3 text-muted">Stock Qty</th>
                            <th class="py-3 text-muted">Status</th>
                            <th class="pe-4 py-3 text-end text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        @if ($product->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                                                alt="{{ $product->product_name }}" class="rounded-2"
                                                style="width: 45px; height: 45px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('assets/images/default-product.png') }}" alt="No Image"
                                                class="rounded-2" style="width: 45px; height: 45px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <span class="d-block fw-bold text-dark">{{ $product->product_name }}</span>
                                            <small class="text-muted">{{ $product->brand ?? 'No Brand' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->category->category_name }}</td>
                                <td class="fw-bold">${{ number_format($product->regular_price, 2) }}</td>
                                <td>{{ $product->stock_quantity }} units</td>
                                <td>
                                    <span
                                        class="badge {{ $product->status == 'published' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <button type="button" class="btn btn-light text-primary edit-btn"
                                        data-bs-toggle="modal" data-bs-target="#editProductModal"
                                        data-route="{{ route('product.update', $product->id) }}"
                                        data-name="{{ $product->product_name }}"
                                        data-price="{{ $product->regular_price }}"
                                        data-stock="{{ $product->stock_quantity }}" data-status="{{ $product->status }}">
                                        <i data-lucide="edit-3" style="width: 16px;"></i>
                                    </button>
                                    <form action="{{ route('product.destroy', $product->id) }}" method="POST"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-light text-danger delete-btn">
                                            <i data-lucide="trash-2" style="width: 16px;"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>




                <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content border-0 shadow rounded-4">
                            <form id="editProductForm" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header border-bottom-0">
                                    <h5 class="modal-title fw-bold">Edit Product</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Product Name</label>
                                            <input type="text" name="product_name" id="edit_name"
                                                class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Price</label>
                                            <input type="number" step="0.01" name="price" id="edit_price"
                                                class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Stock Quantity</label>
                                            <input type="number" name="stock_quantity" id="edit_stock"
                                                class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Status</label>
                                            <select name="status" id="edit_status" class="form-select">
                                                <option value="published">Published</option>
                                                <option value="draft">Draft</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update Product</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="card-footer bg-white py-3 border-top border-light d-flex align-items-center justify-content-between">
                <small class="text-muted">
                    Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of
                    {{ $products->total() }} entries
                </small>
                <nav aria-label="Pagination">
                    {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        </div>
    </div>
    @if (session('success'))
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: '{{ session('success') }}',
                    timer: 2500,
                    showConfirmButton: false,
                    // toast: true,      // លុបបន្ទាត់នេះចេញ ឬដាក់ false
                    position: 'center', // ដាក់ position ជា 'center'
                    background: '#ffffff',
                    color: '#333',
                    // ប្រើ animation សាមញ្ញសម្រាប់ center popup
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }
                });
            @endif

            // គ្រប់គ្រងការចុចប៊ូតុង Delete
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
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });


            // គ្រប់គ្រងការចុចប៊ូតុង Edit
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    // កំណត់ action របស់ form
                    document.getElementById('editProductForm').action = this.getAttribute(
                        'data-route');

                    // បញ្ចូលទិន្នន័យចូលក្នុង input
                    document.getElementById('edit_name').value = this.getAttribute('data-name');
                    document.getElementById('edit_price').value = this.getAttribute('data-price');
                    document.getElementById('edit_stock').value = this.getAttribute('data-stock');
                    document.getElementById('edit_status').value = this.getAttribute('data-status');
                });
            });

            // ឧទាហរណ៍នៃការបន្ថែម Loading ក្នុង Form Submit
            document.getElementById('editProductForm').addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Loading...';
            });

        });
    </script>
@endsection
