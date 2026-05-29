@extends('admin.layouts.layout')

@section('admin_page_title', 'Manage Reviews - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1">Customer Reviews</h3>
                <p class="text-muted small mb-0">Review and manage customer reviews and ratings.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success rounded-4 border-0 shadow-sm">{{ session('success') }}</div>
        @endif

        @forelse ($reviews as $review)
            @if ($loop->first)
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase font-monospace text-muted"
                                style="font-size: 0.70rem; letter-spacing: 0.05em;">
                                <tr>
                                    <th class="ps-4 py-3">Product</th>
                                    <th class="py-3">Customer</th>
                                    <th class="py-3">Rating</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Date</th>
                                    <th class="text-end pe-4 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
            @endif
            <tr>
                <td class="ps-4">
                    <span class="fw-bold text-dark">{{ $review->product->product_name ?? 'Unknown' }}</span>
                </td>
                <td>
                    <div class="fw-semibold text-dark">{{ $review->user->name ?? 'Guest' }}</div>
                    <small class="text-muted">{{ $review->user->email ?? '' }}</small>
                </td>
                <td>
                    <div class="text-warning small">
                        @for ($i = 0; $i < 5; $i++)
                            <i class="fa-{{ $i < $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                        @endfor
                    </div>
                </td>
                <td>
                    @if ($review->status == 'approved')
                        <span class="badge bg-success-subtle text-success rounded-pill px-3"><i
                                class="fa-solid fa-check-circle me-1"></i> Approved</span>
                    @else
                        <span class="badge bg-warning-subtle text-warning rounded-pill px-3"><i
                                class="fa-solid fa-clock me-1"></i> Pending</span>
                    @endif
                </td>
                <td class="text-muted small">{{ $review->created_at->format('M d, Y') }}</td>
                <td class="text-end pe-4">
                    <div class="d-flex justify-content-end gap-2">
                        <form action="{{ route('admin.reviews.update', $review->id) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="button" class="btn btn-sm btn-light text-success rounded-2"
                                onclick="confirmAction(this.form, 'Approve this review?')">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </form>

                        <form action="{{ route('admin.review.reject', $review->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-light text-danger rounded-2"
                                onclick="confirmAction(this.form, 'Reject and delete this review?')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @if ($loop->last)
                </tbody>
                </table>
    </div>
    <div class="card-footer bg-white py-3 border-0">
        {{ $reviews->links() }}
    </div>
    </div>
    @endif
@empty
    <div class="card p-5 text-center border-0 shadow-sm rounded-4">
        <div class="text-muted mb-3"><i class="fa-solid fa-comments fa-3x"></i></div>
        <h5>No Reviews Found</h5>
        <p class="text-muted">There are no pending or approved reviews at the moment.</p>
    </div>
    @endforelse
    </div>


@endsection
