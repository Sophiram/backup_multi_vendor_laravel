@extends('vendor.layouts.layout')

@section('vendor_page_title')
    Settings - Vendor Panel
@endsection

@section('vendor_layout')
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-dark mb-1">Account Settings</h3>
            <p class="text-muted small">Update your security settings and account preferences.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-xl-6">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark text-uppercase small">
                        <i class="bi bi-key text-primary me-2"></i>Change Password
                    </h5>
                </div>
                <div class="card-body p-4">

                    @if (session('status') === 'password-updated')
                        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                <div>Password has been updated successfully!</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->updatePassword->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 mb-4">
                            <ul class="mb-0 small">
                                @foreach ($errors->updatePassword->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary text-uppercase">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="current_password"
                                    class="form-control shadow-none py-2 @if($errors->updatePassword->has('current_password')) is-invalid @endif" required>
                                <button class="btn btn-outline-secondary toggle-password border" type="button" data-target="current_password">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary text-uppercase">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="password"
                                    class="form-control shadow-none py-2 @if($errors->updatePassword->has('password')) is-invalid @endif" required>
                                <button class="btn btn-outline-secondary toggle-password border" type="button" data-target="password">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-secondary text-uppercase">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control shadow-none py-2" required>
                                <button class="btn btn-outline-secondary toggle-password border" type="button" data-target="password_confirmation">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-bold rounded-2 shadow-sm text-uppercase tracking-wider small">
                                <i class="bi bi-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const inputField = document.getElementById(targetId);
                    const icon = this.querySelector('i');

                    if (inputField.type === 'password') {
                        inputField.type = 'text';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    } else {
                        inputField.type = 'password';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    }
                });
            });
        });
    </script>
@endsection
