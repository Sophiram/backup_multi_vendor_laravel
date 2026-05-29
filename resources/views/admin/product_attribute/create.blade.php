@extends('admin.layouts.layout')

@section('admin_page_title', 'Create Product Attribute - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1">Create Product Attribute</h3>
                <p class="text-muted small mb-0">Define global attributes such as sizes, colors, or material types.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h5 class="fw-bold text-dark mb-0">Attribute Specifications</h5>
                    </div>

                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-3 shadow-sm">
                                <ul class="mb-0 small ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.attribute.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Attribute Name</label>
                                <input type="text" name="name" class="form-control form-control-lg rounded-3"
                                    placeholder="e.g., Size" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Default Values</label>
                                <div id="values-container">
                                    <div class="d-flex gap-2 mb-2">
                                        <input type="text" name="values[]" class="form-control form-control-lg rounded-3"
                                            placeholder="e.g., XL" required>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-value-btn">
                                    <i data-lucide="plus" style="width: 14px;"></i> Add Value
                                </button>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg px-4 rounded-3">Save Default
                                Attribute</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize Lucide Icons
            lucide.createIcons();

            // Script to add more value fields
            document.getElementById('add-value-btn').addEventListener('click', function() {
                const container = document.getElementById('values-container');
                const div = document.createElement('div');
                div.className = 'd-flex gap-2 mb-2';
                div.innerHTML = `
                    <input type="text" name="values[]" class="form-control form-control-lg rounded-3" placeholder="e.g., XL" required>
                    <button type="button" class="btn btn-danger rounded-3 remove-btn">
                        <i data-lucide="trash-2" style="width: 16px;"></i>
                    </button>
                `;
                container.appendChild(div);

                // Re-initialize icons for the new button
                lucide.createIcons();

                // Remove logic
                div.querySelector('.remove-btn').addEventListener('click', function() {
                    div.remove();
                });
            });
        });
    </script>
@endsection
