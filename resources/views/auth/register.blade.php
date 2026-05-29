<x-guest-layout>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="text-center mb-4">
                    <a href="/" class="brand-link">
                        <i class="fa-solid fa-bag-shopping"></i> Quick<span>Cart</span>
                    </a>
                    <p class="text-muted mt-2">Join us today! Create your shopping account.</p>
                </div>

                <div class="card shadow-custom border-0 p-4 p-sm-5">
                    <h3 class="fw-bold mb-4 text-dark">Create Account</h3>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-regular fa-user"></i></span>
                                <input type="text" name="name" class="form-control" placeholder="John Doe"
                                    required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="name@example.com"
                                    required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="••••••••"
                                    required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-shield-halved"></i></span>
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="••••••••" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Register As</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user-tag"></i></span>
                                <select name="role" class="form-control" required>
                                    <option value="user">Customer</option>
                                    <option value="vendor">Vendor</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fa-solid fa-user-plus me-2"></i>Register
                        </button>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </form>

                    <div class="text-center mt-4">
                        <span class="text-muted">Already registered? </span>
                        <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none">Log in</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .brand-link {
            font-size: 2.2rem;
            font-weight: 800;
            color: #4f46e5;
            text-decoration: none;
        }

        .brand-link span {
            color: #10b981;
        }

        .shadow-custom {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            border-radius: 24px;
        }

        .input-group-text {
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: #6b7280;
        }

        .form-control {
            border: 1px solid #e5e7eb;
            border-left: none;
            border-radius: 0 12px 12px 0;
            padding: 12px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
            border-color: #e5e7eb;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f46e5, #3b82f6);
            border: none;
            border-radius: 12px;
            font-weight: 600;
        }
    </style>
</x-guest-layout>
