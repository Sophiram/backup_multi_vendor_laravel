@extends('user.layouts.layout')

@section('user_page_title')
Payment - User Panel
@endsection

@section('user_layout')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1" style="color: #0f172a;">
                <i data-lucide="credit-card" class="me-2 text-primary" style="vertical-align: middle;"></i>Payment Methods
            </h3>
            <p class="text-muted small mb-0">Securely manage your saved payment methods and monitor your billing status.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 p-4 h-100" style="border-radius: 20px; background: #ffffff;">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Saved Cards & Accounts</h5>
                        <p class="text-secondary small mb-0">Your payment data is encrypted and completely secure.</p>
                    </div>
                </div>

                <div class="p-4 d-flex flex-column align-items-center text-center justify-content-center border border-dashed rounded-4"
                     style="background: #f8fafc; border-color: #cbd5e1 !important; min-height: 220px;">

                    <div class="mb-3 position-relative text-secondary opacity-50" style="width: 80px; height: 50px; background: #e2e8f0; border-radius: 8px; padding: 6px;">
                        <div class="bg-secondary rounded-1 mb-2" style="width: 20px; height: 12px; opacity: 0.3;"></div>
                        <div class="bg-secondary rounded-1 position-absolute bottom-0 end-0 m-2" style="width: 24px; height: 14px; opacity: 0.2; border-radius: 4px !important;"></div>
                    </div>

                    <h6 class="fw-bold text-dark mb-1">No payment methods linked yet</h6>
                    <p class="text-muted small mb-3" style="max-width: 320px;">Add a credit card or payment account to enable seamless one-click checkout.</p>

                    <a href="#" class="btn btn-outline-primary btn-sm px-4 py-2 rounded-3 fw-semibold d-flex align-items-center gap-2">
                        <i data-lucide="plus" style="width: 16px; height: 16px; stroke-width: 2.5;"></i> Add Payment Method
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 p-4 text-white position-relative overflow-hidden h-100"
                 style="border-radius: 20px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); min-height: 200px;">

                <div class="position-absolute opacity-10" style="bottom: -20px; right: -20px; color: #fff;">
                    <i data-lucide="wallet" style="width: 140px; height: 140px; stroke-width: 1;"></i>
                </div>

                <div class="position-relative" style="z-index: 2;">
                    <span class="text-white-50 fw-medium uppercase d-block mb-1" style="font-size: 12px; letter-spacing: 0.5px;">Pending Balance</span>
                    <h1 class="fw-extrabold mb-4 tracking-tight" style="font-size: 38px;">$0.00</h1>

                    <button class="btn btn-white w-100 py-2.5 fw-semibold rounded-3 shadow-sm btn-pay-now"
                            style="background: #ffffff; color: #4f46e5; border: none; transition: all 0.2s;">
                        Make Payment
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling សម្រាប់ប៊ូតុងសងប្រាក់ពណ៌ស */
    .btn-white:hover {
        background: #f8fafc !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15) !important;
    }
    .btn-white:active {
        transform: translateY(0);
    }
</style>

<script>
    // អានឡុក Icons
    lucide.createIcons();
</script>
@endsection
