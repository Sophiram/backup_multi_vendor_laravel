@extends('vendor.layouts.layout')

@section('vendor_page_title')
    Create Product - Vendor Panel
@endsection

@section('vendor_layout')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-0">Add New Product</h3>
                    <p class="text-muted small mb-0">Upload a new product with multiple variants and attributes to your store.</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-warning alert-dismissible fade show shadow-sm rounded-3 mb-4">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please fix the following errors:</div>
                    <ul class="mb-0 sm-thin">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('vendor.product.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                @csrf
                <div class="row g-4">

                    <div class="col-12 col-lg-8">

                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="fw-bold text-dark mb-0">General Information</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label for="product_name" class="form-label fw-bold text-secondary small">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" name="product_name" class="form-control shadow-none py-2" placeholder="e.g., Lenovo IdeaPad 3 15 Thin" value="{{ old('product_name') }}" required>
                                </div>

                                <div class="mb-0">
                                    <label for="description" class="form-label fw-bold text-secondary small">Description</label>
                                    <textarea name="description" cols="30" rows="7" class="form-control shadow-none" placeholder="Provide detailed specifications of the product...">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="fw-bold text-dark mb-0">Product Gallery</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-0">
                                    <label for="images" class="form-label fw-bold text-secondary small">Upload Images</label>
                                    <div class="border border-dashed p-4 text-center rounded-3 bg-light position-relative" style="border-style: dashed !important;">
                                        <i class="bi bi-images fs-1 text-muted mb-2 d-block"></i>
                                        <input type="file" name="images[]" class="form-control shadow-none position-absolute top-0 start-0 w-100 h-100 opacity-0 style-pointer" multiple id="image-input" style="cursor: pointer;">
                                        <span class="text-primary fw-bold">Click to upload</span> or drag and drop files here
                                        <small class="text-muted d-block mt-1">Supports: JPEG, PNG, JPG, GIF (Max: 10MB)</small>
                                    </div>
                                    <div id="image-preview-container" class="d-flex flex-wrap gap-2 mt-3"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold text-dark mb-0">Product Variants & Attributes</h5>
                                <button type="button" class="btn btn-outline-primary btn-sm fw-bold px-3 rounded-2" id="add-attribute-btn">
                                    <i class="bi bi-plus-circle me-1"></i> Add Row
                                </button>
                            </div>
                            <div class="card-body p-3 bg-light-subtle">
                                <div id="attributes-container">
                                    <div class="p-3 mb-3 bg-white rounded-3 border position-relative attribute-row shadow-sm">
                                        <button type="button" class="btn btn-sm btn-link text-danger position-absolute top-0 end-0 mt-2 me-2 p-0 remove-row-btn shadow-none d-none" title="Remove Row">
                                            <i class="bi bi-x-circle-fill fs-5"></i>
                                        </button>

                                        <div class="row g-3">
                                            <div class="col-12 col-md-4">
                                                <label class="form-label mb-1 fw-bold text-secondary small">Attribute Type</label>
                                                <select name="attributes[0][attribute_id]" class="form-select form-select-sm attribute-selector shadow-none py-2">
                                                    <option value="">-- Select Option --</option>
                                                    @foreach ($availableAttributes as $attr)
                                                        <option value="{{ $attr->id }}">{{ $attr->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label class="form-label mb-1 fw-bold text-secondary small">Value</label>
                                                <select name="attributes[0][attribute_value_id]" class="form-select form-select-sm value-selector shadow-none py-2" disabled>
                                                    <option value="">-- Select Value --</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label class="form-label mb-1 fw-bold text-secondary small">Additional Price</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light text-muted fw-bold">$</span>
                                                    <input type="number" step="0.01" name="attributes[0][additional_price]" class="form-control shadow-none py-2" placeholder="0.00">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-12 col-lg-4">

                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="fw-bold text-dark mb-0">Organization</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label for="store_id" class="form-label fw-bold text-secondary small">Select Store <span class="text-danger">*</span></label>
                                    <select name="store_id" class="form-select shadow-none py-2" required>
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>{{ $store->store_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="p-2 border rounded-3 bg-light">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">Category</label>
                                        <select name="category_id" id="category_id" class="form-select" required>
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">Subcategory</label>
                                        <select name="subcategory_id" id="subcategory_id" class="form-select">
                                            <option value="">Select Subcategory</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="fw-bold text-dark mb-0">Pricing & Inventory</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label for="sku" class="form-label fw-bold text-secondary small">Product SKU <span class="text-danger">*</span></label>
                                    <input type="text" name="sku" class="form-control shadow-none" placeholder="e.g., LXD3402" value="{{ old('sku') }}" required>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label for="regular_price" class="form-label fw-bold text-secondary small">Regular Price ($) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="regular_price" class="form-control shadow-none" value="{{ old('regular_price') }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="discounted_price" class="form-label fw-bold text-secondary small">Discount ($)</label>
                                        <input type="number" step="0.01" name="discounted_price" class="form-control shadow-none" value="{{ old('discounted_price') }}">
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-6">
                                        <label for="tax_rate" class="form-label fw-bold text-secondary small">Tax (%)</label>
                                        <input type="number" step="0.01" name="tax_rate" class="form-control shadow-none" value="{{ old('tax_rate', 0.0) }}">
                                    </div>
                                    <div class="col-6">
                                        <label for="stock_quantity" class="form-label fw-bold text-secondary small">Stock Qty <span class="text-danger">*</span></label>
                                        <input type="number" name="stock_quantity" class="form-control shadow-none" value="{{ old('stock_quantity', 0) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="fw-bold text-dark mb-0">SEO Settings</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label for="slug" class="form-label fw-bold text-secondary small">URL Slug</label>
                                    <input type="text" name="slug" class="form-control shadow-none" placeholder="lenovo-ideapad-3" value="{{ old('slug') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="meta_title" class="form-label fw-bold text-secondary small">Meta Title</label>
                                    <input type="text" name="meta_title" class="form-control shadow-none" value="{{ old('meta_title') }}">
                                </div>

                                <div class="mb-0">
                                    <label for="meta_description" class="form-label fw-bold text-secondary small">Meta Description</label>
                                    <input type="text" name="meta_description" class="form-control shadow-none" value="{{ old('meta_description') }}">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow-sm rounded-3">
                            <i class="bi bi-cloud-arrow-up-fill me-2"></i>Publish Product
                        </button>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let rowIndex = 1;
            const container = document.getElementById('attributes-container');
            const addBtn = document.getElementById('add-attribute-btn');
            const imageInput = document.getElementById('image-input');
            const previewContainer = document.getElementById('image-preview-container');

            // 📷 Image Preview Management
            imageInput.addEventListener('change', function() {
                previewContainer.innerHTML = '';
                if (this.files) {
                    Array.from(this.files).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'position-relative border rounded p-1 bg-white';
                            div.style.width = '80px';
                            div.style.height = '80px';
                            div.innerHTML = `<img src="${e.target.result}" class="w-100 h-100 object-fit-cover rounded">`;
                            previewContainer.appendChild(div);
                        }
                        reader.readAsDataURL(file);
                    });
                }
            });

            // --- Subcategory Logic ---
            document.getElementById('category_id').addEventListener('change', function() {
                const categoryId = this.value;
                const subcategorySelect = document.getElementById('subcategory_id');
                subcategorySelect.innerHTML = '<option value="">Loading...</option>';

                if (!categoryId) {
                    subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
                    return;
                }

                fetch(`/api/categories/${categoryId}/subcategories`)
                    .then(response => response.json())
                    .then(data => {
                        subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
                        data.forEach(sub => {
                            subcategorySelect.innerHTML += `<option value="${sub.id}">${sub.subcategory_name}</option>`;
                        });
                    })
                    .catch(error => {
                        subcategorySelect.innerHTML = '<option value="">Error loading subcategories</option>';
                    });
            });

            function updateRemoveButtons() {
                const rows = container.querySelectorAll('.attribute-row');
                rows.forEach((row) => {
                    const btn = row.querySelector('.remove-row-btn');
                    btn.classList.toggle('d-none', rows.length <= 1);
                });
            }

            // 1. AJAX Fetch: Parent Attribute to Child Values
            container.addEventListener('change', function(e) {
                if (e.target.classList.contains('attribute-selector')) {
                    const attrId = e.target.value;
                    const row = e.target.closest('.attribute-row');
                    const valueSelect = row.querySelector('.value-selector');

                    if (!attrId) {
                        valueSelect.innerHTML = '<option value="">-- Select Value --</option>';
                        valueSelect.disabled = true;
                        return;
                    }

                    valueSelect.innerHTML = '<option value="">Loading...</option>';
                    valueSelect.disabled = false; // បើកឱ្យឃើញ Loading...

                    fetch(`/api/attributes/${attrId}/values`)
                        .then(response => response.json())
                        .then(data => {
                            valueSelect.innerHTML = '<option value="">-- Select Value --</option>';
                            data.forEach(val => {
                                valueSelect.innerHTML += `<option value="${val.id}">${val.value}</option>`;
                            });
                        })
                        .catch(error => {
                            valueSelect.innerHTML = '<option value="">Error loading values</option>';
                        });
                }
            });

            // 2. Dynamic Append Row
            addBtn.addEventListener('click', function() {
                const firstRow = document.querySelector('.attribute-row');
                const clone = firstRow.cloneNode(true);

                // កំណត់ Name attribute ថ្មីដោយប្រើ rowIndex
                clone.querySelector('.attribute-selector').name = `attributes[${rowIndex}][attribute_id]`;
                clone.querySelector('.value-selector').name = `attributes[${rowIndex}][attribute_value_id]`;
                clone.querySelector('input[type="number"]').name = `attributes[${rowIndex}][additional_price]`;

                // Reset values សម្រាប់ជួរថ្មី
                clone.querySelector('.attribute-selector').value = '';
                clone.querySelector('.value-selector').innerHTML = '<option value="">-- Select Value --</option>';
                clone.querySelector('.value-selector').disabled = true;
                clone.querySelector('input[type="number"]').value = '';

                container.appendChild(clone);
                rowIndex++;
                updateRemoveButtons();
            });

            // 3. Dynamic Remove Row
            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-row-btn')) {
                    e.target.closest('.attribute-row').remove();
                    updateRemoveButtons();
                }
            });
        });
    </script>
@endsection
