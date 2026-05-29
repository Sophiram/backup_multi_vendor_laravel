@extends('admin.layouts.layout')

@section('admin_page_title')
    Manage User - Admin Panel
@endsection

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <!-- ផ្នែកអត្ថបទ -->
            <div class="flex-grow-1">
                <h3 class="fw-bold text-slate-800 mb-1">User Management</h3>
                <p class="text-muted small mb-0">Monitor user accounts, permissions, roles, and account status activities.
                </p>
            </div>

            <!-- ផ្នែកប៊ូតុង -->
            <div class="flex-shrink-0">
                <button type="button" class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-bold w-100 w-sm-auto"
                    data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fa-solid fa-user-plus me-1"></i> Add New User
                </button>
            </div>

            <!-- Modal Structure -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold" id="addUserModalLabel">Add New User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.users.store') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Full Name</label>
                                    <input type="text" name="name" class="form-control" required
                                        placeholder="Enter full name">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Email Address</label>
                                    <input type="email" name="email" class="form-control" required
                                        placeholder="example@mail.com">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Role</label>
                                    <select name="role" class="form-select" required>
                                        <option value="customer">Customer</option>
                                        <option value="admin">Admin</option>
                                        <option value="editor">Editor</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Password</label>
                                    <input type="password" name="password" class="form-control" required
                                        placeholder="••••••••">
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <!-- Total Users -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3" style="background-color: #f8fafc;">
                    <small class="text-muted fw-bold text-uppercase d-block mb-1"
                        style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Users</small>
                    <h4 class="fw-extrabold text-dark mb-0">{{ number_format($stats['total']) }}</h4>
                </div>
            </div>
            <!-- Active Now -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3" style="background-color: #f8fafc;">
                    <small class="text-muted fw-bold text-uppercase d-block mb-1"
                        style="font-size: 0.72rem; letter-spacing: 0.5px;">Active Now</small>
                    <h4 class="fw-extrabold text-success mb-0">{{ number_format($stats['active']) }}</h4>
                </div>
            </div>
            <!-- Administrators -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3" style="background-color: #f8fafc;">
                    <small class="text-muted fw-bold text-uppercase d-block mb-1"
                        style="font-size: 0.72rem; letter-spacing: 0.5px;">Administrators</small>
                    <h4 class="fw-extrabold text-primary mb-0">{{ number_format($stats['admins']) }}</h4>
                </div>
            </div>
            <!-- Suspended -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3" style="background-color: #f8fafc;">
                    <small class="text-muted fw-bold text-uppercase d-block mb-1"
                        style="font-size: 0.72rem; letter-spacing: 0.5px;">Suspended</small>
                    <h4 class="fw-extrabold text-danger mb-0">{{ number_format($stats['suspended']) }}</h4>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <form id="filterForm" action="{{ route('admin.manage.users') }}" method="GET">
                    <div class="row g-2 align-items-center justify-content-between">
                        <!-- ផ្នែកស្វែងរក (Search) -->
                        <div class="col-12 col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input type="text" name="search" class="form-control bg-light border-start-0"
                                    placeholder="Search name, email..." value="{{ request('search') }}">
                            </div>
                        </div>

                        <!-- ផ្នែកតម្រង (Filters) -->
                        <div class="col-12 col-md-auto d-flex gap-2">
                            <!-- Filter Role -->
                            <select name="role" class="form-select form-select-sm bg-light text-muted">
                                <option value="">Filter Role</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="editor" {{ request('role') == 'editor' ? 'selected' : '' }}>Editor</option>
                                <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer
                                </option>
                            </select>

                            <!-- Filter Status -->
                            <select name="status" class="form-select form-select-sm bg-light text-muted">
                                <option value="">Filter Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>
                                    Suspended
                                </option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-sm" style="font-size: 0.9rem;">
                    <thead class="table-light text-uppercase font-monospace" style="font-size: 0.75rem;">
                        <tr>
                            <th class="ps-4 py-3 text-muted">User Profile</th>
                            <th class="py-3 text-muted">Role</th>
                            <th class="py-3 text-muted">Phone</th>
                            <th class="py-3 text-muted">Joined Date</th>
                            <th class="py-3 text-muted">Status</th>
                            <th class="pe-4 py-3 text-end text-muted">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center"
                                            style="width: 38px; height: 38px;">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="d-block fw-bold text-dark">{{ $user->name }}</span>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $user->role == 'admin' ? 'bg-primary-subtle text-primary' : 'bg-light text-secondary' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="text-secondary fw-medium">{{ $user->phone ?? 'N/A' }}</td>
                                <td class="text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span
                                        class="badge {{ $user->status == 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST"
                                        class="toggle-status-form">
                                        @csrf @method('PATCH')
                                        <button type="button" class="btn btn-sm btn-light toggle-status-btn"
                                            title="{{ $user->status == 'active' ? 'Suspend User' : 'Activate User' }}">
                                            <i
                                                class="fa-solid {{ $user->status == 'active' ? 'fa-ban text-danger' : 'fa-check text-success' }}"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div
                class="card-footer bg-white py-3 border-top border-light d-flex flex-wrap align-items-center justify-content-between gap-3">
                <small class="text-muted">
                    Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }}
                    entries
                </small>
                <div>
                    {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
    <script>
        // SweetAlert for Toggle Status
        document.querySelectorAll('.toggle-status-btn').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('.toggle-status-form');
                const isSuspending = this.querySelector('i').classList.contains('fa-ban');

                Swal.fire({
                    title: isSuspending ? 'Suspend this user?' : 'Activate this user?',
                    text: isSuspending ?
                        "The user will be restricted from accessing the platform." :
                        "The user will regain access to the platform.",
                    icon: isSuspending ? 'warning' : 'question',
                    showCancelButton: true,
                    confirmButtonColor: isSuspending ? '#d33' : '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: isSuspending ? 'Yes, Suspend' : 'Yes, Activate'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });



        document.querySelectorAll('#filterForm select, #filterForm input[name="search"]').forEach(element => {
            element.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            // សម្រាប់ Input search ឱ្យវា Submit នៅពេលចុច Enter
            if (element.tagName === 'INPUT') {
                element.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        document.getElementById('filterForm').submit();
                    }
                });
            }
        });
    </script>
@endsection
