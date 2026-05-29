@extends('admin.layouts.layout')

@section('admin_page_title', 'Manage Reviews - Admin Panel')

@section('admin_layout')
    <div class="container-fluid px-4 py-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1">Customer Reviews</h3>
                <p class="text-muted small mb-0">Review and manage customer reviews and ratings.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($reviews->count() > 0)
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase font-monospace" style="font-size: 0.75rem;">
                            <tr>
                                <th class="ps-4">Product</th>
                                <th>Customer</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Date</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reviews as $review)
                                <tr>
                                    <td class="ps-4 fw-bold">{{ $review->product->product_name }}</td>
                                    <td>{{ $review->user->name }}</td>
                                    <td>
                                        <div class="text-warning small">
                                            @for ($i = 0; $i < $review->rating; $i++)
                                                <i class="fa-solid fa-star"></i>
                                            @endfor
                                        </div>
                                    </td>
                                    <td class="text-truncate" style="max-width: 200px;">{{ $review->review }}</td>
                                    <td class="text-muted">{{ $review->created_at->format('M d, Y') }}</td>
                                    <td class="text-end pe-4">
                                        <form action="{{ route('admin.reviews.update', $review->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('PUT')
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-success rounded-2 me-1">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.review.reject', $review->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-2"
                                                onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white py-3">{{ $reviews->links() }}</div>
            </div>
        @else
            <div class="card p-5 text-center">No reviews submitted yet.</div>
        @endif
    </div>
@endsection
