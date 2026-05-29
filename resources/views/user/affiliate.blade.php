@extends('user.layouts.layout')

@section('user_page_title')
    Affiliate - User Panel
@endsection

@section('user_layout')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1" style="color: #0f172a;">
                <i data-lucide="users" class="me-2 text-primary" style="vertical-align: middle;"></i>Affiliate Program
            </h3>
            <p class="text-muted small mb-0">Invite your friends and earn commissions on every successful sale.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card p-4 shadow-sm border-0" style="border-radius: 20px; background: #ffffff;">
                <h5 class="fw-bold text-dark mb-2">Welcome to your Affiliate Dashboard</h5>
                <p class="text-secondary mb-4" style="font-size: 14.5px;">
                    Share your unique referral link with friends, family, or on your social media channels to start earning competitive commissions.
                </p>

                <div class="p-3 mb-3 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3"
                     style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 14px;">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <div class="p-2 rounded-3 bg-primary-subtle text-primary d-none d-sm-block">
                            <i data-lucide="link" style="width: 20px; height: 20px;"></i>
                        </div>
                        <span id="referralLink" class="fw-semibold text-slate-700 text-truncate" style="font-size: 15px; color: #334155;">
                            {{ url('/ref/' . Auth::user()->id) }}
                        </span>
                    </div>

                    <button id="btnCopy" onclick="copyLink()" class="btn btn-primary px-4 py-2 d-flex align-items-center justify-content-center gap-2 shadow-sm fw-medium rounded-3" style="min-width: 130px;">
                        <i data-lucide="copy" style="width: 16px; height: 16px;"></i>
                        <span id="btnText">Copy Link</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card p-4 border-0 shadow-sm h-100" style="border-radius: 16px; background: #ffffff;">
                        <div class="p-3 rounded-3 bg-info-subtle text-info mb-3 d-inline-block" style="width: fit-content;">
                            <i data-lucide="send" style="width: 24px; height: 24px;"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-2">1. Share Your Link</h6>
                        <p class="text-muted small mb-0">Copy your link and post it on your blog, social media platforms, or send directly via message.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 border-0 shadow-sm h-100" style="border-radius: 16px; background: #ffffff;">
                        <div class="p-3 rounded-3 bg-warning-subtle text-warning mb-3 d-inline-block" style="width: fit-content;">
                            <i data-lucide="user-plus" style="width: 24px; height: 24px;"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-2">2. Friends Register</h6>
                        <p class="text-muted small mb-0">When someone signs up or completes a purchase using your unique link, they get tracked instantly.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 border-0 shadow-sm h-100" style="border-radius: 16px; background: #ffffff;">
                        <div class="p-3 rounded-3 bg-success-subtle text-success mb-3 d-inline-block" style="width: fit-content;">
                            <i data-lucide="banknote" style="width: 24px; height: 24px;"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-2">3. Earn Commission</h6>
                        <p class="text-muted small mb-0">Receive a percentage payout of the generated income straight into your wallet system.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Re-init lucide icons
    lucide.createIcons();

    function copyLink() {
        // ទាញយកអត្ថបទពី Link Span
        const linkText = document.getElementById('referralLink').innerText;
        const btnText = document.getElementById('btnText');
        const btnCopy = document.getElementById('btnCopy');

        // មុខងារចម្លង (Copy to Clipboard)
        navigator.clipboard.writeText(linkText).then(() => {
            // កែប្រែ UI ជាបណ្តោះអាសន្នពេល Copy រួចរាល់
            btnText.innerText = 'Copied!';
            btnCopy.classList.remove('btn-primary');
            btnCopy.classList.add('btn-success');

            // កំណត់ឱ្យត្រឡប់មកស្ថានភាពដើមវិញក្រោយ ២ វិនាទី
            setTimeout(() => {
                btnText.innerText = 'Copy Link';
                btnCopy.classList.remove('btn-success');
                btnCopy.classList.add('btn-primary');
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }
</script>
@endsection
