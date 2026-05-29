@extends('vendor.layouts.layout')

@section('vendor_page_title')
    Manage Product Attributes - Vendor Panel
@endsection

@section('vendor_layout')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1" style="color: #0f172a;">
                        <i data-feather="list" class="me-2 text-primary" style="vertical-align: middle; width: 22px; height: 22px;"></i>Product Attributes
                    </h3>
                    <p class="text-muted small mb-0">Manage your product variations, sizes, colors, and their respective value options.</p>
                </div>
                <a href="{{ route('vendor.attribute.create') }}" class="btn btn-primary fw-semibold px-3 py-2.5 shadow-sm rounded-3 d-flex align-items-center gap-1" style="font-size: 14px;">
                    <i data-feather="plus-circle" style="width: 16px; height: 16px;"></i> Add New Attribute
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 bg-success-subtle text-success-emphasis">
                    <div class="d-flex align-items-center gap-2">
                        <i data-feather="check-circle" style="width: 18px; height: 18px;"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: #ffffff; overflow: hidden;">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h5 class="card-title mb-0 fw-bold text-dark small text-uppercase tracking-wider" style="font-size: 13px; color: #475569;">All Attributes & Values</h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="table-light border-bottom text-secondary" style="font-size: 11.5px; font-weight: 700; background-color: #f8fafc;">
                                <tr>
                                    <th class="ps-4 py-3" style="width: 80px;">ID</th>
                                    <th class="py-3" style="width: 250px;">Attribute Name</th>
                                    <th class="py-3">Configured Values</th>
                                    <th class="py-3" style="width: 200px;">Created At</th>
                                    <th class="py-3 text-end pe-4" style="width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 13.5px; color: #334155;">
                                @forelse ($attributes as $attribute)
                                    <tr class="border-bottom">
                                        <td class="ps-4 fw-bold text-secondary">#{{ $attribute->id }}</td>

                                        <td class="fw-bold text-dark attribute-name-text" style="color: #1e293b;">{{ $attribute->name }}</td>

                                        <td>
                                            <div class="d-flex flex-wrap gap-2 text-wrap">
                                                @if($attribute->values && $attribute->values->count() > 0)
                                                    @foreach($attribute->values as $val)
                                                        <span class="badge bg-light text-dark border border-slate-200 px-2.5 py-1.5 rounded-2 fw-medium attribute-value-badge" data-value-id="{{ $val->id }}" style="font-size: 12px; color: #475569 !important;">
                                                            {{ $val->value }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted small italic">No values configured</span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="text-muted small">
                                            <div class="d-flex align-items-center gap-1">
                                                <i data-feather="calendar" class="text-secondary" style="width: 14px; height: 14px;"></i>
                                                <span>{{ $attribute->created_at ? $attribute->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                                            </div>
                                        </td>

                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-2 align-items-center">
                                                <button type="button"
                                                    class="btn btn-sm btn-light border edit-action-btn rounded-2 p-1.5 open-edit-modal shadow-none d-flex align-items-center justify-content-center"
                                                    data-id="{{ $attribute->id }}"
                                                    title="Edit Attribute"
                                                    style="width: 32px; height: 32px;">
                                                    <i data-feather="edit-2" class="text-primary" style="width: 15px; height: 15px;"></i>
                                                </button>

                                                <form action="{{ route('vendor.delete.attribute', $attribute->id) }}" method="POST" class="d-inline mb-0 delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-light border delete-action-btn rounded-2 p-1.5 delete-btn shadow-none d-flex align-items-center justify-content-center" title="Delete Attribute" style="width: 32px; height: 32px;">
                                                        <i data-feather="trash-2" class="text-danger" style="width: 15px; height: 15px;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i data-feather="sliders" class="d-block mx-auto mb-3 text-slate-300" style="width: 48px; height: 48px;"></i>
                                            <span class="d-block fw-medium mb-1">No attributes found</span>
                                            <span class="small text-muted">Click "Add New Attribute" to create one.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editAttributeModal" tabindex="-1" aria-labelledby="editAttributeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-white border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="editAttributeModalLabel" style="font-size: 16px;">
                        <i data-feather="edit" class="text-primary" style="width: 18px; height: 18px;"></i>Edit Attribute
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="edit-attribute-form" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label for="modal_attribute_name" class="form-label fw-semibold text-secondary small text-uppercase tracking-wider">Attribute Name <span class="text-danger">*</span></label>
                            <input type="text" name="attribute_name" id="modal_attribute_name" class="form-control px-3 py-2 rounded-3 border-slate-200 shadow-none" required>
                        </div>

                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-semibold text-secondary small text-uppercase tracking-wider mb-0">Attribute Values</label>
                                <button type="button" class="btn btn-outline-primary btn-xs fw-semibold px-2 py-1 rounded-2 d-flex align-items-center gap-1 shadow-none" id="modal-add-value-btn" style="font-size: 11px;">
                                    <i data-feather="plus" style="width: 12px; height: 12px;"></i> Add Value
                                </button>
                            </div>

                            <div id="modal-values-container" class="bg-light p-3 rounded-3 border border-dashed border-slate-300">
                                </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top p-3" style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                        <button type="button" class="btn btn-light border px-3 py-2 rounded-3 fw-medium shadow-none text-secondary" style="font-size: 13.5px;" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm" style="font-size: 13.5px;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .edit-action-btn:hover { background-color: #e0f2fe !important; border-color: #bae6fd !important; }
        .delete-action-btn:hover { background-color: #fee2e2 !important; border-color: #fca5a5 !important; }
        .btn-xs { padding: .25rem .4rem; font-size: .75rem; line-height: 1; border-radius: .2rem; }
        .border-slate-200 { border-color: #e2e8f0 !important; }
        .border-slate-300 { border-color: #cbd5e1 !important; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ==========================================
            // ១. ផ្នែកគ្រប់គ្រងការលុប (SWEETALERT2 POP-UP)
            // ==========================================
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    const form = this.closest('.delete-form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This will permanently delete the attribute and all its sub-values!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        background: '#ffffff',
                        customClass: {
                            confirmButton: 'btn btn-danger px-4 py-2 fw-semibold me-2 rounded-3',
                            cancelButton: 'btn btn-secondary px-4 py-2 fw-semibold rounded-3'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) { form.submit(); }
                    });
                });
            });

            // ==========================================
            // ២. ផ្នែកគ្រប់គ្រងការកែប្រែ (EDIT IN POP-UP MODAL)
            // ==========================================
            const editModal = new bootstrap.Modal(document.getElementById('editAttributeModal'));
            const editForm = document.getElementById('edit-attribute-form');
            const modalAttrNameInput = document.getElementById('modal_attribute_name');
            const modalValuesContainer = document.getElementById('modal-values-container');
            const modalAddValueBtn = document.getElementById('modal-add-value-btn');

            // មុខងារពិនិត្យ និងបង្ហាញ/លាក់ប៊ូតុងលុបជួរ Value ក្នុង Modal
            function updateModalRemoveButtons() {
                const rows = modalValuesContainer.querySelectorAll('.modal-value-row');
                rows.forEach(row => {
                    const btn = row.querySelector('.remove-modal-value-btn');
                    if (rows.length > 1) {
                        btn.classList.remove('d-none');
                    } else {
                        btn.classList.add('d-none');
                    }
                });
            }

            // ចាប់ Event ពេល Vendor ចុចប៊ូតុង Edit លើតារាង
            document.querySelectorAll('.open-edit-modal').forEach(button => {
                button.addEventListener('click', function() {
                    const attrId = this.getAttribute('data-id');
                    const row = this.closest('tr');
                    const attrName = row.querySelector('.attribute-name-text').textContent.trim();

                    // កំណត់ទិសដៅ Action Dynamic
                    editForm.action = `/vendor/attribute/update/${attrId}`;
                    modalAttrNameInput.value = attrName;
                    modalValuesContainer.innerHTML = '';

                    // ទាញយកគ្រាប់តម្លៃ (Badges) ចេញពីតារាងមកបង្កើតជា Input ក្នុង Pop-up
                    const badges = row.querySelectorAll('.attribute-value-badge');
                    if(badges.length > 0) {
                        badges.forEach(badge => {
                            const valText = badge.textContent.trim();
                            appendNewValueRow(valText);
                        });
                    } else {
                        appendNewValueRow('');
                    }

                    editModal.show();
                });
            });

            // មុខងារបង្កើតជួរបញ្ចូល Value ថ្មី
            function appendNewValueRow(value = '') {
                const div = document.createElement('div');
                div.className = 'd-flex gap-2 mb-2 modal-value-row align-items-center';
                div.innerHTML = `
                    <div class="flex-grow-1">
                        <input type="text" name="values[]" class="form-control form-control-sm px-3 py-2 rounded-3 border-slate-200 shadow-none" value="${value}" placeholder="e.g., Red, XL" style="font-size: 13.5px;" required>
                    </div>
                    <button type="button" class="btn btn-link link-danger remove-modal-value-btn p-1.5 text-decoration-none" title="Remove">
                        <i class="remove-icon" style="width: 16px; height: 16px;"></i>
                    </button>
                `;

                modalValuesContainer.appendChild(div);

                // បង្កើត Lucide Trash Icon ថ្មីតាមរយៈការជំនួស i tag ខាងលើ
                const iconPlace = div.querySelector('.remove-icon');
                if (typeof feather !== 'undefined') {
                    const iTag = document.createElement('i');
                    iTag.setAttribute('data-feather', 'trash-2');
                    iTag.setAttribute('style', 'width: 16px; height: 16px; vertical-align: middle;');
                    iconPlace.parentNode.replaceChild(iTag, iconPlace);
                    feather.replace();
                }

                updateModalRemoveButtons();
            }

            // ចុចបន្ថែមប្រឡោះ Value ថ្មីក្នុង Modal
            modalAddValueBtn.addEventListener('click', function() {
                appendNewValueRow('');
                const lastInput = modalValuesContainer.querySelector('.modal-value-row:last-child input');
                if(lastInput) lastInput.focus();
            });

            // ចុចលុបប្រឡោះ Value ក្នុង Modal
            modalValuesContainer.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-modal-value-btn');
                if (removeBtn) {
                    removeBtn.closest('.modal-value-row').remove();
                    updateModalRemoveButtons();
                }
            });
        });
    </script>
@endsection
