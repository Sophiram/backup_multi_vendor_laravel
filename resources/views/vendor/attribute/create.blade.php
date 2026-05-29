@extends('vendor.layouts.layout')

@section('vendor_page_title')
    Create New Attribute - Vendor Panel
@endsection

@section('vendor_layout')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">

            <div class="mb-4">
                <h3 class="fw-bold text-dark mb-1" style="color: #0f172a;">
                    <i data-feather="sliders" class="me-2 text-primary"
                        style="vertical-align: middle; width: 22px; height: 22px;"></i>Attribute Management
                </h3>
                <p class="text-muted small mb-0">Create a main attribute category along with its specific value options.</p>
            </div>

            @if ($errors->any())
                <div
                    class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 bg-warning-subtle text-warning-emphasis">
                    <div class="fw-bold mb-1 d-flex align-items-center gap-2">
                        <i data-feather="alert-triangle" style="width: 18px; height: 18px;"></i>
                        <span>Please fix the errors:</span>
                    </div>
                    <ul class="mb-0 small ps-3 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('success'))
                <div
                    class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 bg-success-subtle text-success-emphasis">
                    <div class="d-flex align-items-center gap-2">
                        <i data-feather="check-circle" style="width: 18px; height: 18px;"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('vendor.attribute.store') }}" method="POST">
                @csrf

                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: #ffffff;">
                    <div class="card-header bg-white border-bottom py-3 px-4"
                        style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 16px;">Create New Attribute</h5>
                    </div>

                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label for="attribute_name"
                                class="form-label fw-semibold text-secondary small text-uppercase tracking-wider">Attribute
                                Name <span class="text-danger">*</span></label>
                            <input type="text" name="attribute_name" id="attribute_name"
                                class="form-control px-3 py-2.5 rounded-3 border-slate-200 shadow-none fs-5"
                                placeholder="e.g., Color, Size, Storage, Material" value="{{ old('attribute_name') }}"
                                required>
                            <div class="form-text text-muted small mt-1">The primary name classification of the product
                                variant.</div>
                        </div>

                        <hr class="text-slate-200 opacity-50 my-4">

                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <label
                                        class="form-label fw-semibold text-secondary small text-uppercase tracking-wider mb-0">Attribute
                                        Values</label>
                                    <div class="text-muted small" style="font-size: 12px;">Add specific options belonging to
                                        this attribute.</div>
                                </div>
                                <button type="button"
                                    class="btn btn-outline-primary btn-sm fw-semibold px-3 rounded-3 shadow-none d-flex align-items-center gap-1"
                                    id="add-value-btn">
                                    <i data-feather="plus" style="width: 16px; height: 16px;"></i> Add Value
                                </button>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Select Attribute</label>
                                <select name="attribute_id" class="form-select">
                                    @foreach (\App\Models\Attribute::all() as $attr)
                                        <option value="{{ $attr->id }}">{{ $attr->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Additional Price</label>
                                <input type="number" name="additional_price" class="form-control" placeholder="0.00">
                            </div>
                            

                            <div id="values-container" class="bg-light p-3 rounded-3 border border-dashed border-slate-300">
                                <div class="d-flex gap-2 mb-2 value-row align-items-center">
                                    <div class="flex-grow-1">
                                        <input type="text" name="values[]"
                                            class="form-control px-3 py-2 rounded-3 border-slate-200 shadow-none"
                                            placeholder="e.g., Red, Small, 256GB" style="font-size: 14px;" required>
                                    </div>
                                    <button type="button"
                                        class="btn btn-link link-danger remove-value-btn p-2 d-none text-decoration-none"
                                        title="Remove">
                                        <i data-feather="trash-2" style="width: 18px; height: 18px;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top p-4 text-end"
                        style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                        <button type="submit"
                            class="btn btn-primary px-4 py-2.5 rounded-3 fw-semibold shadow-sm d-flex align-items-center gap-2 ms-auto"
                            style="font-size: 14px;">
                            <i data-feather="upload-cloud" style="width: 18px; height: 18px;"></i> Save Attribute
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('values-container');
            const addBtn = document.getElementById('add-value-btn');

            // មុខងារពិនិត្យ និងបង្ហាញ/លាក់ ប៊ូតុងលុប (បើមានតែ ១ ប្រឡោះ មិនឱ្យលុបទេ)
            function updateRemoveButtons() {
                const rows = container.querySelectorAll('.value-row');
                rows.forEach(row => {
                    const btn = row.querySelector('.remove-value-btn');
                    if (rows.length > 1) {
                        btn.classList.remove('d-none');
                    } else {
                        btn.classList.add('d-none');
                    }
                });
            }

            // ១. ចុចបង្កើតប្រឡោះ Value ថ្មី
            addBtn.addEventListener('click', function() {
                const firstRow = container.querySelector('.value-row');
                const clone = firstRow.cloneNode(true);

                // សំអាតតម្លៃចាស់ចេញពី Input ដែលលូនថ្មី
                const input = clone.querySelector('input');
                input.value = '';

                // លុបកម្មវិធីចាស់របស់ Feather Icons ចេញមុននឹងលូនថ្មី ដើម្បីកុំឱ្យស្ទួន SVG
                const svg = clone.querySelector('svg');
                if (svg) {
                    const iTag = document.createElement('i');
                    iTag.setAttribute('data-feather', 'trash-2');
                    iTag.setAttribute('style', 'width: 18px; height: 18px;');
                    iTag.className = 'remove-value-btn-icon';
                    svg.parentNode.replaceChild(iTag, svg);
                }

                container.appendChild(clone);

                // បំពេញ Feather Icons ឡើងវិញសម្រាប់ធាតុដែលទើបនឹងថែម
                if (typeof feather !== 'undefined') {
                    feather.replace();
                } else if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }

                updateRemoveButtons();
                input.focus();
            });

            // ២. ចុចលុបប្រឡោះ Value វិញ
            container.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-value-btn');
                if (removeBtn) {
                    const row = removeBtn.closest('.value-row');
                    row.remove();
                    updateRemoveButtons();
                }
            });
        });
    </script>
@endsection
