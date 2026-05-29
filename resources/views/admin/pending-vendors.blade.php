@extends('admin.layouts.layout')

@section('admin_page_title')
    Pending Vendors - Admin Panel
@endsection

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1">Pending Vendor Approvals</h3>
                <p class="text-muted small mb-0">Review and verify new vendor registration requests.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h5 class="fw-bold text-dark mb-0">Vendors Awaiting Approval</h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="table-light text-uppercase font-monospace" style="font-size: 0.75rem;">
                            <tr>
                                <th class="ps-4 py-3 text-muted">Vendor Name</th>
                                <th class="py-3 text-muted">Email Address</th>
                                <th class="pe-4 py-3 text-end text-muted">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingVendors as $vendor)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ $vendor->name }}</td>
                                    <td class="text-secondary">{{ $vendor->email }}</td>
                                    <td class="pe-4 text-end">
                                        <button type="button" class="btn btn-sm btn-primary rounded-2 px-3"
                                            data-bs-toggle="modal" data-bs-target="#approveModal{{ $vendor->id }}">
                                            <i class="fa-solid fa-check-circle me-1"></i> Approve
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="approveModal{{ $vendor->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 rounded-4 shadow">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold">Confirm Approval</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body py-4">
                                                Are you sure you want to approve <strong>{{ $vendor->name }}</strong> as a
                                                vendor? This action will grant them access to the platform.
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light rounded-3"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.approve', $vendor->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success rounded-3 px-4">Yes,
                                                        Approve Now</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Verified!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
@endsection
