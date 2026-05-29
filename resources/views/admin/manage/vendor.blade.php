@extends('admin.layouts.layout')

@section('admin_page_title', 'Manage Vendors')

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <h3 class="fw-bold mb-4">All Vendors</h3>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Store Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $vendor->store_name }}</td>
                                <td>{{ $vendor->store_email }}</td>
                                <td>
                                    <span
                                        class="badge {{ $vendor->status == 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ ucfirst($vendor->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.manage.vendors.edit', $vendor->id) }}" class="btn btn-sm btn-light">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">No vendors found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
