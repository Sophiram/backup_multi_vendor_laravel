@extends('vendor.layouts.layout')

@section('vendor_page_title', 'Payout & Earnings')

@section('vendor_layout')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;600;700&family=Kantumruuy+Pro:wght@400;600;700&display=swap"
        rel="stylesheet">

    <style>
        .commission-wrapper {
            font-family: 'Plus Jakarta Sans', 'Kantumruuy Pro', sans-serif;
        }

        .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        .form-control-custom {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px 14px;
        }

        .form-control-custom:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .btn-premium {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border-radius: 10px;
            padding: 10px 20px;
            border: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-premium:hover {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
        }

        .icon-box-inline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .alert-custom {
            border-radius: 12px;
            border: none;
            font-size: 0.875rem;
        }
    </style>

    <div class="container-fluid px-2 px-md-4 commission-wrapper py-3">

        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-4"
                    style="background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-uppercase tracking-wider small mb-0" style="opacity: 0.85; font-size: 0.75rem;">
                            Available Balance</h6>
                        <i data-lucide="wallet" style="width: 24px; height: 24px; opacity: 0.9;"></i>
                    </div>
                    <h2 class="fw-bold font-outfit mb-1">${{ number_format($availableBalance, 2) }}</h2>
                    <p class="small mb-0" style="opacity: 0.75; font-size: 0.8rem;">Ready for withdrawal request</p>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white border">
                    <div class="d-flex justify-content-between align-items-center mb-3 text-muted">
                        <h6 class="text-uppercase tracking-wider small mb-0" style="font-size: 0.75rem;">Pending Payouts
                        </h6>
                        <i data-lucide="clock" class="text-warning" style="width: 24px; height: 24px;"></i>
                    </div>
                    <h2 class="fw-bold font-outfit text-dark mb-1">${{ number_format($totalPending, 2) }}</h2>
                    <p class="small text-muted mb-0" style="font-size: 0.8rem;">Currently requested and awaiting review</p>
                </div>
            </div>

            <div class="col-12 class-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white border">
                    <div class="d-flex justify-content-between align-items-center mb-3 text-muted">
                        <h6 class="text-uppercase tracking-wider small mb-0" style="font-size: 0.75rem;">Total Net Earnings
                        </h6>
                        <i data-lucide="banknote" class="text-success" style="width: 24px; height: 24px;"></i>
                    </div>
                    <h2 class="fw-bold font-outfit text-dark mb-1">${{ number_format($totalEarnings, 2) }}</h2>
                    <p class="small text-muted mb-0" style="font-size: 0.8rem;">Lifetime net earnings after commission</p>
                </div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success alert-custom shadow-sm d-flex align-items-center alert-dismissible fade show"
                        role="alert">
                        <i data-lucide="check-circle" class="me-2 text-success" style="width: 20px; height: 20px;"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-custom shadow-sm d-flex align-items-center alert-dismissible fade show"
                        role="alert">
                        <i data-lucide="alert-triangle" class="me-2 text-danger" style="width: 20px; height: 20px;"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-custom shadow-sm alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center mb-1">
                            <i data-lucide="alert-circle" class="me-2 text-danger" style="width: 20px; height: 20px;"></i>
                            <strong class="small">Please fix the following errors:</strong>
                        </div>
                        <ul class="mb-0 ps-4 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <h6 class="fw-bold text-dark mb-4 text-uppercase tracking-wide icon-box-inline"
                        style="font-size: 0.9rem;">
                        <i data-lucide="send" class="text-primary" style="width: 18px; height: 18px;"></i>
                        Request Payout
                    </h6>

                    <form action="{{ route('vendor.payout.request') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Withdraw Amount ($)</label>
                            <input type="number" name="amount" step="0.01" min="5"
                                class="form-control form-control-custom font-outfit" placeholder="0.00"
                                value="{{ old('amount') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Your Bank Details (Snapshot)</label>
                            <textarea class="form-control form-control-custom bg-light font-outfit text-secondary small" rows="3" readonly
                                style="resize: none;">{{ $user->vendor->bank_account_info ?? 'No bank details configured.' }}</textarea>
                            <span class="text-muted d-block mt-1" style="font-size: 11px;">
                                To change this, update your Profile Settings.
                            </span>
                        </div>

                        <button type="submit" class="btn btn-premium w-100 py-2 mt-2">
                            <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                            Submit Request
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <h6 class="fw-bold text-dark mb-4 text-uppercase tracking-wide icon-box-inline"
                        style="font-size: 0.9rem;">
                        <i data-lucide="history" class="text-dark" style="width: 18px; height: 18px;"></i>
                        Payout Transaction History
                    </h6>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                            <thead class="table-light text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                <tr>
                                    <th class="ps-4 py-3 text-muted fw-bold">ID</th>
                                    <th class="py-3 text-muted fw-bold">Amount</th>
                                    <th class="py-3 text-muted fw-bold">Bank Info Snapshot</th>
                                    <th class="pe-4 py-3 text-muted fw-bold">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payouts as $payout)
                                    <tr>
                                        <td class="ps-4 fw-bold">#{{ $payout->id }}</td>
                                        <td class="font-outfit fw-bold text-dark">${{ number_format($payout->amount, 2) }}
                                        </td>
                                        <td>
                                            <span class="text-muted d-inline-block text-truncate"
                                                style="max-width: 200px; font-size: 0.8rem;">
                                                {{ $payout->bank_details_snapshot ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="pe-4">
                                            @if ($payout->status == 'Pending')
                                                <span
                                                    class="badge bg-warning text-dark px-2.5 py-1.5 rounded-2">Pending</span>
                                            @elseif($payout->status == 'Approved' || $payout->status == 'Completed')
                                                <span class="badge bg-success px-2.5 py-1.5 rounded-2">Approved</span>
                                            @else
                                                <span
                                                    class="badge bg-danger px-2.5 py-1.5 rounded-2">{{ $payout->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted small">
                                            <i data-lucide="inbox" class="d-block mx-auto mb-2 text-muted"
                                                style="width: 32px; height: 32px; opacity: 0.5;"></i>
                                            No payout records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($payouts->hasPages())
                        <div class="d-flex justify-content-end mt-4">
                            {{ $payouts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
@endsection
