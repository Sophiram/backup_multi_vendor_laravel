@extends('vendor.layouts.layout')

@section('vendor_page_title')
    Manage Product - Vendor Panel
@endsection

@section('vendor_layout')
    <!-- 🌐 ហៅសមាសភាគ SweetAlert2 CSS & JS (CDN) សម្រាប់ Pop-up លុប -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-0">Product Management</h3>
                    <p class="text-muted small mb-0">View, edit, and monitor your store's inventory and product details.</p>
                </div>
                <a href="{{ route('vendor.product.create') }}" class="btn btn-primary fw-bold px-3 py-2 shadow-sm rounded-3">
                    <i class="bi bi-plus-circle me-1"></i> Add Product
                </a>
            </div>

            <!-- Notification Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- ដាក់នៅពីលើកាតតារាង ឬក្នុង Modal ដើម្បីដឹងថាខុសអ្វីខ្លះ -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Main Table Card -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark small text-uppercase tracking-wider">All Products List</h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="table-light border-bottom text-secondary"
                                style="font-size: 12px; font-weight: 700;">
                                <tr>
                                    <th class="ps-4 py-3" style="width: 60px;">ID</th>
                                    <th class="py-3">Product Details</th>
                                    <th class="py-3">Category info</th>
                                    <th class="py-3">Pricing</th>
                                    <th class="py-3">Stock Qty</th>
                                    <th class="py-3">Tax</th>
                                    <th class="py-3">Attributes</th>
                                    <th class="py-3 text-end pe-4" style="width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 13.5px;">
                                @foreach ($products as $product)
                                    <tr class="border-bottom">
                                        <!-- ID -->
                                        <td class="ps-4 fw-bold text-secondary">#{{ $product->id }}</td>

                                        <!-- Product Details -->
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark mb-0.5 project-name-text"
                                                    style="font-size: 14px;">{{ $product->product_name }}</span>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span
                                                        class="badge bg-light text-secondary border px-1.5 py-0.5 rounded project-sku-text"
                                                        style="font-size: 10px;" data-sku="{{ $product->sku }}">SKU:
                                                        {{ $product->sku }}</span>
                                                    @if ($product->slug)
                                                        <small class="text-muted" style="font-size: 11px;"><i
                                                                class="bi bi-link-45deg"></i>
                                                            {{ Str::limit($product->slug, 20) }}</small>
                                                    @endif
                                                </div>
                                                <small
                                                    class="text-muted text-wrap mt-1 d-block sm-thin text-truncate project-desc-text"
                                                    style="max-width: 250px; font-size: 11.5px;">{{ $product->description }}</small>
                                            </div>
                                        </td>

                                        <!-- Category & SubCategory -->
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark fw-semibold project-cat-text"
                                                    data-cat-id="{{ $product->category_id }}"><i
                                                        class="bi bi-folder2 me-1 text-primary"></i>Cat ID:
                                                    {{ $product->category_id }}</span>
                                                <small class="text-muted ps-3 project-subcat-text"
                                                    data-subcat-id="{{ $product->subcategory_id }}"
                                                    style="font-size: 11px;">Sub ID: {{ $product->subcategory_id }}</small>
                                            </div>
                                        </td>

                                        <!-- Pricing -->
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark fw-bold project-reg-price"
                                                    data-price="{{ $product->regular_price }}">${{ number_format($product->regular_price, 2) }}</span>
                                                <span class="text-danger fw-semibold project-disc-price"
                                                    data-disc-price="{{ $product->discounted_price }}">{{ $product->discounted_price ? '$' . number_format($product->discounted_price, 2) : '' }}</span>
                                            </div>
                                        </td>

                                        <!-- Stock Status -->
                                        <td>
                                            <span class="project-stock-qty" data-stock="{{ $product->stock_quantity }}">
                                                @if ($product->stock_quantity > 10)
                                                    <span
                                                        class="badge bg-success-subtle text-success px-2.5 py-1.5 rounded-2 fw-bold"><i
                                                            class="bi bi-box-seam me-1"></i>In Stock
                                                        ({{ $product->stock_quantity }})
                                                    </span>
                                                @elseif($product->stock_quantity > 0)
                                                    <span
                                                        class="badge bg-warning-subtle text-warning-emphasis px-2.5 py-1.5 rounded-2 fw-bold"><i
                                                            class="bi bi-exclamation-triangle me-1"></i>Low Stock
                                                        ({{ $product->stock_quantity }})</span>
                                                @else
                                                    <span
                                                        class="badge bg-danger-subtle text-danger px-2.5 py-1.5 rounded-2 fw-bold"><i
                                                            class="bi bi-x-circle me-1"></i>Out of Stock</span>
                                                @endif
                                            </span>
                                        </td>

                                        <!-- Tax Rate -->
                                        <td>
                                            <span class="text-secondary fw-medium project-tax-rate"
                                                data-tax="{{ $product->tax_rate }}">{{ $product->tax_rate ?? 0 }}%</span>
                                        </td>
                                        <!-- កន្លែងបង្ហាញ Attribute ក្នុងតារាង -->
                                        <!-- ក្នុង Tbody នៃ Manage Product -->
                                        <td class="project-attributes"
                                            data-attributes='{{ json_encode(
                                                $product->attributes->map(
                                                    fn($a) => [
                                                        'attribute_id' => $a->attribute_id,
                                                        'attribute_value_id' => $a->attribute_value_id,
                                                        'additional_price' => $a->additional_price,
                                                    ],
                                                ),
                                            ) }}'>
                                            @foreach ($product->attributes as $attr)
                                                <span
                                                    class="badge bg-light text-dark border">{{ $attr->attribute->name ?? '' }}:
                                                    {{ $attr->attributeValue->value ?? '' }}</span>
                                            @endforeach
                                        </td>

                                        <!-- Action Buttons Group -->
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-1.5 align-items-center">
                                                <!-- 🛠️ ប៊ូតុង Edit បើក Pop-up Modal -->
                                                <button type="button"
                                                    class="btn btn-sm btn-light border text-primary edit-action-btn rounded-2 p-1.5 open-edit-modal"
                                                    data-id="{{ $product->id }}" data-store-id="{{ $product->store_id }}"
                                                    title="Edit Product">
                                                    <i class="align-middle" data-feather="edit"
                                                        style="width: 15px; height: 15px;"></i>
                                                </button>

                                                <!-- Delete Button with SweetAlert2 -->
                                                <button type="button"
                                                    class="btn btn-sm btn-light border text-danger delete-action-btn rounded-2 p-1.5 delete-btn"
                                                    data-id="{{ $product->id }}" title="Delete Product">
                                                    <i class="align-middle" data-feather="trash"
                                                        style="width: 15px; height: 15px;"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                @if ($products->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="bi bi-box2 fs-1 d-block mb-2 text-light-muted"></i>No products found.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 💎 POP-UP MODAL សម្រាប់កែប្រែ PRODUCT -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-white border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark" id="editProductModalLabel"><i
                            class="bi bi-pencil-square text-primary me-2"></i>Edit Product</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="edit-product-form" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="store_id" id="modal_store_id">

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <!-- Product Name -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Product Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="product_name" id="modal_product_name"
                                    class="form-control shadow-none py-2" required>
                            </div>

                            <!-- SKU -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">SKU <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="sku" id="modal_sku"
                                    class="form-control shadow-none py-2" required>
                            </div>

                            <!-- Regular Price -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Regular Price ($)
                                    <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="regular_price" id="modal_regular_price"
                                    class="form-control shadow-none py-2" required>
                            </div>

                            <!-- Discounted Price -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Discounted Price
                                    ($)</label>
                                <input type="number" step="0.01" name="discounted_price" id="modal_discounted_price"
                                    class="form-control shadow-none py-2">
                            </div>

                            <!-- Stock Quantity -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Stock Quantity <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="stock_quantity" id="modal_stock_quantity"
                                    class="form-control shadow-none py-2" required>
                            </div>

                            <!-- Tax Rate -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Tax Rate (%)</label>
                                <input type="number" step="0.01" name="tax_rate" id="modal_tax_rate"
                                    class="form-control shadow-none py-2">
                            </div>

                            <!-- Category Selection -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Category <span
                                        class="text-danger">*</span></label>

                                <select name="category_id" id="modal_category_id" class="form-select shadow-none py-2"
                                    onchange="loadSubcategories(this.value)">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Sub Category Selection -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small text-uppercase">SubCategory</label>
                                <select name="subcategory_id" id="modal_subcategory_id"
                                    class="form-select shadow-none py-2">
                                    <option value="">Select SubCategory</option>
                                </select>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Description</label>
                                <textarea name="description" id="modal_description" class="form-control shadow-none py-2" rows="3"></textarea>
                            </div>

                            <div class="col-12 mt-3">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Attributes</label>
                                <div id="attribute-container">
                                    <!-- ធាតុនេះនឹងត្រូវចាក់បញ្ចូលដោយ JS -->
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                    onclick="addAttributeRow()">+ Add Attribute</button>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top p-3">
                        <button type="button" class="btn btn-secondary px-3 fw-bold rounded-2"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold rounded-2 shadow-sm">Save
                            Changes</button>
                    </div>
                </form>

                <!-- គំរូសម្រាប់ Clone (Hidden) -->
                <div id="attributes-container">
                    <!-- ១. បង្ហាញទិន្នន័យដែលមានស្រាប់ (សម្រាប់ Edit) -->
                    @if (isset($product_info) && $product_info->attributes->count() > 0)
                        @foreach ($product_info->attributes as $index => $attr)
                            <div class="row g-2 mb-2 align-items-center attr-row d-flex">
                                <div class="col-4">
                                    <select name="attributes[{{ $index }}][attribute_id]"
                                        class="form-select attr-select" onchange="loadValues(this)">
                                        <option value="">Select Attribute</option>
                                        @foreach ($availableAttributes as $availableAttr)
                                            <option value="{{ $availableAttr->id }}"
                                                {{ $attr->attribute_id == $availableAttr->id ? 'selected' : '' }}>
                                                {{ $availableAttr->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select name="attributes[{{ $index }}][attribute_value_id]"
                                        class="form-select value-select">
                                        <option value="{{ $attr->attribute_value_id }}" selected>
                                            {{ $attr->attributeValue->value ?? 'Select Value' }}</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <input type="number" name="attributes[{{ $index }}][additional_price]"
                                        class="form-control" value="{{ $attr->additional_price }}">
                                </div>
                                <div class="col-1">
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="this.parentElement.parentElement.remove()">×</button>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <!-- ២. Template ដែលលាក់ទុកសម្រាប់បន្ថែម Row ថ្មី (JavaScript នឹងប្រើប្រាស់វា) -->
                    <div id="attribute-template" style="display:none;">
                        <div class="row g-2 mb-2 align-items-center attr-row d-flex">
                            <div class="col-4">
                                <select name="attributes[0][attribute_id]" class="form-select attr-select"
                                    onchange="loadValues(this)">
                                    <option value="">Select Attribute</option>
                                    @foreach ($availableAttributes as $attr)
                                        <option value="{{ $attr->id }}">{{ $attr->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <select name="attributes[0][attribute_value_id]" class="form-select value-select">
                                    <option value="">Select Value</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <input type="number" name="attributes[0][additional_price]" class="form-control"
                                    placeholder="Price">
                            </div>
                            <div class="col-1">
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="this.parentElement.parentElement.remove()">×</button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <!-- Custom Styles -->
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

    <!-- 🛠️ JAVASCRIPT LOGIC FOR PRODUCT POP-UP EDIT & DELETE -->
    <script>
        // ✅ FIX 1: Better error handling for loadValues
        function loadValues(selectElement, selectedValueId = null) {
            const attributeId = selectElement.value;
            const row = selectElement.closest('.attr-row');
            const valueSelect = row.querySelector('.value-select');

            valueSelect.innerHTML = '<option value="">Loading...</option>';

            if (attributeId) {
                fetch(`/api/attributes/${attributeId}/values`)
                    .then(res => {
                        if (!res.ok) throw new Error('Failed to load values');
                        return res.json();
                    })
                    .then(values => {
                        valueSelect.innerHTML = '<option value="">Select Value</option>';
                        values.forEach(v => {
                            const opt = document.createElement('option');
                            opt.value = v.id;
                            opt.textContent = v.value;
                            if (selectedValueId && v.id == selectedValueId) opt.selected = true;
                            valueSelect.appendChild(opt);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading values:', error);
                        valueSelect.innerHTML = '<option value="">Error loading values</option>';
                    });
            }
        }

        // ✅ FIX 2: Better error handling for loadSubcategories
        function loadSubcategories(categoryId, selectedSubcatId = null) {
            const subcatSelect = document.getElementById('modal_subcategory_id');

            if (!categoryId) {
                subcatSelect.innerHTML = '<option value="">Select SubCategory</option>';
                return;
            }

            subcatSelect.innerHTML = '<option value="">Loading...</option>';

            fetch(`/api/categories/${categoryId}/subcategories`)
                .then(res => {
                    if (!res.ok) throw new Error('Failed to load subcategories');
                    return res.json();
                })
                .then(subcategories => {
                    subcatSelect.innerHTML = '<option value="">Select SubCategory</option>';

                    if (Array.isArray(subcategories) && subcategories.length > 0) {
                        subcategories.forEach(sub => {
                            const opt = document.createElement('option');
                            opt.value = sub.id;
                            opt.textContent = sub.subcategory_name;
                            if (selectedSubcatId && selectedSubcatId == sub.id) opt.selected = true;
                            subcatSelect.appendChild(opt);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading subcategories:', error);
                    subcatSelect.innerHTML = '<option value="">Error loading subcategories</option>';
                });
        }

        // ✅ FIX 3: Improved addAttributeRow function
        function addAttributeRow(data = null) {
            const container = document.getElementById('attribute-container');
            const templateContent = document.getElementById('attribute-template').firstElementChild;

            // បង្កើត Clone នៃ Template
            const newRow = templateContent.cloneNode(true);
            newRow.style.display = 'block';

            // ប្រើ Timestamp ឬ Random ID ដើម្បីកុំឱ្យជាន់គ្នា (ជៀសវាងការប្រើ 0 ជានិច្ច)
            const index = Date.now() + Math.floor(Math.random() * 1000);

            newRow.querySelectorAll('select, input').forEach(el => {
                let oldName = el.getAttribute('name');
                if (oldName) {
                    // ជំនួស [0] ទៅជា index ថ្មី
                    let newName = oldName.replace(/\[\d+\]/, `[${index}]`);
                    el.setAttribute('name', newName);
                }

                // សម្អាតតម្លៃដើម (Reset)
                if (el.tagName === 'INPUT') el.value = '';
            });

            // ប្រសិនបើជាការ Edit (មាន data ចូលមក)
            if (data) {
                const attrSelect = newRow.querySelector('.attr-select');
                const valueSelect = newRow.querySelector('.value-select');
                const priceInput = newRow.querySelector('input[name*="additional_price"]');

                attrSelect.value = data.attribute_id;

                // កំណត់តម្លៃសម្រាប់ Select នីមួយៗ
                if (data.attribute_id) {
                    // ទាញយកតម្លៃ (Values) តាមរយៈ AJAX
                    loadValues(attrSelect, data.attribute_value_id);
                }

                if (priceInput) {
                    priceInput.value = data.additional_price || 0;
                }
            }

            container.appendChild(newRow);
        }

        // ✅ FIX 4: Delete with SweetAlert2
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
            const editForm = document.getElementById('edit-product-form');

            // Handle Edit Button Click
            document.querySelectorAll('.open-edit-modal').forEach(button => {
                button.addEventListener('click', function() {
                    const prodId = this.getAttribute('data-id');
                    const storeId = this.getAttribute('data-store-id');
                    const row = this.closest('tr');

                    // Clear attributes container
                    document.getElementById('attribute-container').innerHTML = '';

                    // Load attributes data
                    const attrData = JSON.parse(row.querySelector('.project-attributes')
                        .getAttribute('data-attributes') || '[]');

                    if (Array.isArray(attrData) && attrData.length > 0) {
                        attrData.forEach(item => addAttributeRow(item));
                    }

                    // Get data from row
                    const prodName = row.querySelector('.project-name-text').textContent.trim();
                    const sku = row.querySelector('.project-sku-text').getAttribute('data-sku');
                    const desc = row.querySelector('.project-desc-text').textContent.trim();
                    const catId = row.querySelector('.project-cat-text').getAttribute(
                        'data-cat-id');
                    const subcatId = row.querySelector('.project-subcat-text').getAttribute(
                        'data-subcat-id');
                    const regPrice = row.querySelector('.project-reg-price').getAttribute(
                        'data-price');
                    const discPrice = row.querySelector('.project-disc-price').getAttribute(
                        'data-disc-price');
                    const stock = row.querySelector('.project-stock-qty').getAttribute(
                        'data-stock');
                    const tax = row.querySelector('.project-tax-rate').getAttribute('data-tax');

                    // Set form data
                    editForm.action = `/vendor/product/update/${prodId}`;
                    document.getElementById('modal_store_id').value = storeId;
                    document.getElementById('modal_product_name').value = prodName;
                    document.getElementById('modal_sku').value = sku;
                    document.getElementById('modal_description').value = desc;
                    document.getElementById('modal_regular_price').value = regPrice;
                    document.getElementById('modal_discounted_price').value = discPrice || '';
                    document.getElementById('modal_stock_quantity').value = stock;
                    document.getElementById('modal_tax_rate').value = tax || 0;

                    // Set category and load subcategories
                    document.getElementById('modal_category_id').value = catId;
                    if (catId) {
                        // ប្រើ Promise ឬ async/await ដើម្បីឱ្យវា Load ទិន្នន័យរួចសិន ទើបកំណត់ value
                        fetch(`/api/categories/${catId}/subcategories`)
                            .then(res => res.json())
                            .then(data => {
                                const select = document.getElementById('modal_subcategory_id');
                                select.innerHTML =
                                    '<option value="">Select SubCategory</option>';
                                data.forEach(sub => {
                                    let opt = document.createElement('option');
                                    opt.value = sub.id;
                                    opt.textContent = sub.name;
                                    select.appendChild(opt);
                                });
                                // ដាក់ value បន្ទាប់ពី options ត្រូវបានបង្កើត
                                select.value = subcatId;
                            });
                    }

                    editModal.show();
                });
            });

            // ✅ Handle Delete Button with SweetAlert2
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const prodId = this.getAttribute('data-id');
                    const row = this.closest('tr');
                    const prodName = row.querySelector('.project-name-text').textContent.trim();

                    Swal.fire({
                        title: 'Delete Product?',
                        text: `Are you sure you want to delete "${prodName}"? This action cannot be undone.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Create a temporary form to submit the DELETE request
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/vendor/product/delete/${prodId}`;

                            // Add CSRF token
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = '{{ csrf_token() }}';

                            // Add Method spoofing
                            const methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'DELETE';

                            form.appendChild(csrfInput);
                            form.appendChild(methodInput);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
