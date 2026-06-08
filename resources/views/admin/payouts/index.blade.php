@extends('admin.layouts.layout')

@section('admin_page_title', 'Manage Payout Requests - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4 py-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">Payout Requests</h4>
                <p class="text-muted small mb-0">Review, audit, and approve vendor withdrawal requests securely.</p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block fw-semibold text-uppercase tracking-wider"
                                style="font-size: 0.75rem;">Pending Requests</span>
                            <h4 class="fw-bold text-dark mb-0 mt-1">{{ $requests->where('status', 'pending')->count() }}
                                Case(s)</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-success bg-opacity-10 text-success rounded-4">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block fw-semibold text-uppercase tracking-wider"
                                style="font-size: 0.75rem;">Total Volume (This Page)</span>
                            <h4 class="fw-bold text-dark mb-0 mt-1">${{ number_format($requests->sum('amount'), 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4">
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block fw-semibold text-uppercase tracking-wider"
                                style="font-size: 0.75rem;">Processed Status</span>
                            <h4 class="fw-bold text-dark mb-0 mt-1">Active Gateway</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                    <thead class="table-light text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        <tr>
                            <th class="ps-4 py-3 text-muted fw-bold">Vendor Identity</th>
                            <th class="py-3 text-muted fw-bold">Withdrawal Amount</th>
                            <th class="py-3 text-muted fw-bold">Payout Status</th>
                            <th class="py-3 text-muted fw-bold">Request Date</th>
                            <th class="pe-4 py-3 text-end text-muted fw-bold" style="width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $request)
                            <tr class="transition-all">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold text-uppercase fs-6 shadow-sm border border-primary border-opacity-10"
                                            style="width: 40px; height: 40px; min-width: 40px;">
                                            {{ strtoupper(substr($request->vendor?->user?->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.875rem;">
                                                {{ $request->vendor?->user?->name ?? 'Unknown User' }}</h6>
                                            <span class="text-muted d-block mt-0.5" style="font-size: 0.75rem;">ID:
                                                #{{ $request->vendor?->user?->id ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3">
                                    <span class="fw-bold text-dark fs-6">${{ number_format($request->amount, 2) }}</span>
                                </td>

                                <td class="py-3">
                                    @if ($request->status == 'pending')
                                        <span
                                            class="badge rounded-pill px-2.5 py-1.5 font-monospace text-uppercase fw-semibold bg-warning-subtle text-warning border border-warning-subtle"
                                            style="font-size: 0.7rem;">
                                            • Pending
                                        </span>
                                    @elseif($request->status == 'approved')
                                        <span
                                            class="badge rounded-pill px-2.5 py-1.5 font-monospace text-uppercase fw-semibold bg-success-subtle text-success border border-success-subtle"
                                            style="font-size: 0.7rem;">
                                            • Approved
                                        </span>
                                    @else
                                        <span
                                            class="badge rounded-pill px-2.5 py-1.5 font-monospace text-uppercase fw-semibold bg-danger-subtle text-danger border border-danger-subtle"
                                            style="font-size: 0.7rem;">
                                            • Rejected
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3">
                                    <div class="text-dark fw-semibold" style="font-size: 0.82rem;">
                                        {{ $request->created_at?->format('M d, Y') ?? 'N/A' }}</div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        {{ $request->created_at?->format('h:i A') ?? '' }}</div>
                                </td>

                                <td class="pe-4 text-end py-3">
                                    @if ($request->status == 'pending')
                                        <div class="d-flex justify-content-end gap-2">
                                            <form action="{{ route('admin.payouts.approve', $request->id) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-success btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm border-0"
                                                    style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                                                    <i class="bi bi-check-circle-fill"></i> Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.payouts.reject', $request->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to reject this payout request?');">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-outline-danger btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center gap-1"
                                                    style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                                                    <i class="bi bi-x-circle"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="badge bg-light text-secondary border rounded-3 px-3 py-2 fw-medium">
                                            <i class="bi bi-file-earmark-lock2-fill me-1"></i> Processed
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="mb-2">
                                        <i class="bi bi-inbox-fill fs-2 text-muted opacity-40"></i>
                                    </div>
                                    <h6 class="fw-bold text-secondary mb-1">No Payout Requests Found</h6>
                                    <p class="small text-muted mb-0">When system vendors request withdrawals, they will list
                                        inside this pipeline.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($requests, 'links') && $requests->hasPages())
                <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
                    <span class="small text-muted">Showing page records dynamically.</span>
                    <div>
                        {{ $requests->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
