@extends('admin.layouts.layout')

@section('admin_page_title', 'Manage Attributes - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1">Manage Attributes</h3>
                <p class="text-muted small mb-0">Define and manage product attributes.</p>
            </div>
            <a href="{{ route('productattribute.create') }}" class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-bold">
                <i data-lucide="plus" style="width: 16px; height: 16px;"></i> Create New
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h5 class="fw-bold text-dark mb-0">Product Attributes</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase font-monospace" style="font-size: 0.75rem;">
                        <tr>
                            <th class="ps-4 text-muted">#</th>
                            <th class="text-muted">Product</th>
                            <th class="text-muted">Attribute</th>
                            <th class="text-muted">Price</th>
                            <th class="pe-4 text-end text-muted">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attributes as $attr)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration }}</td>
                                <td><span class="fw-bold">{{ $attr->product->product_name ?? 'N/A' }}</span></td>
                                <td>{{ $attr->attribute->name ?? 'N/A' }}</td>
                                <td>${{ number_format($attr->additional_price, 2) }}</td>
                                <td class="pe-4 text-end">
                                    <button type="button" class="btn btn-light btn-sm rounded-2 text-primary edit-prod-btn"
                                        data-bs-toggle="modal" data-bs-target="#editProdModal"
                                        data-route="{{ route('update.attribute', $attr->id) }}"
                                        data-product="{{ $attr->product_id }}" data-attribute="{{ $attr->attribute_id }}"
                                        data-price="{{ $attr->additional_price }}">
                                        <i data-lucide="edit-3" style="width: 16px;"></i>
                                    </button>

                                    <form action="{{ route('delete.attribute', $attr->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                            class="btn btn-light btn-sm rounded-2 text-danger delete-btn">
                                            <i data-lucide="trash-2" style="width: 16px;"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h5 class="fw-bold text-dark mb-0">Global Master Attributes</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4 text-muted">Attribute Name</th>
                            <th class="text-muted">Values</th>
                            <th class="pe-4 text-end text-muted">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($globalAttributes as $gAttr)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $gAttr->name }}</td>
                                <td>
                                    @foreach ($gAttr->values as $val)
                                        <span
                                            class="badge bg-light text-secondary border rounded-pill px-3">{{ $val->value }}</span>
                                    @endforeach
                                </td>
                                <td class="pe-4 text-end">
                                    <button type="button"
                                        class="btn btn-light btn-sm rounded-2 text-primary edit-global-btn"
                                        data-bs-toggle="modal" data-bs-target="#editGlobalModal"
                                        data-id="{{ $gAttr->id }}" data-name="{{ $gAttr->name }}"
                                        data-route="{{ route('admin.attribute.update', $gAttr->id) }}">
                                        <i data-lucide="edit-3" style="width: 16px;"></i>
                                    </button>
                                    <form action="{{ route('admin.attribute.destroy', $gAttr->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                            class="btn btn-light btn-sm rounded-2 text-danger delete-btn">
                                            <i data-lucide="trash-2" style="width: 16px;"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProdModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editProdForm" method="POST">@csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Product Attribute</h5>
                    </div>
                    <div class="modal-body">
                        <select name="product_id" id="edit_product_id" class="form-select mb-2">
                            @foreach (\App\Models\Product::all() as $p)
                                <option value="{{ $p->id }}">{{ $p->product_name }}</option>
                            @endforeach
                        </select>
                        <select name="attribute_id" id="edit_attribute_id" class="form-select mb-2">
                            @foreach (\App\Models\Attribute::all() as $a)
                                <option value="{{ $a->id }}">{{ $a->name }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="additional_price" id="edit_price" class="form-control">
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-primary">Update</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editGlobalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editGlobalForm" method="POST">@csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Global Attribute</h5>
                    </div>
                    <div class="modal-body"><input type="text" name="name" id="edit_name" class="form-control">
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-primary">Update</button></div>


                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            lucide.createIcons();
        });

        // Edit Product Script
        document.querySelectorAll('.edit-prod-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('editProdForm').action = this.getAttribute('data-route');
                document.getElementById('edit_product_id').value = this.getAttribute('data-product');
                document.getElementById('edit_attribute_id').value = this.getAttribute('data-attribute');
                document.getElementById('edit_price').value = this.getAttribute('data-price');
            });
        });

        // Edit Global Script
        document.querySelectorAll('.edit-global-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('editGlobalForm').action = this.getAttribute('data-route');
                document.getElementById('edit_name').value = this.getAttribute('data-name');
            });
        });

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('form');

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
    </script>
@endsection
