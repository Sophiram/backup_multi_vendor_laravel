<x-guest-layout>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="text-center mb-4">
                    <a href="/" class="brand-link">
                        <i class="fa-solid fa-bag-shopping"></i> Quick<span>Cart</span>
                    </a>
                    <p class="text-muted mt-2">Welcome back! Please login to your account.</p>
                </div>

                <div class="card shadow-custom border-0 p-4 p-sm-5">
                    <h3 class="fw-bold mb-4 text-dark">Sign In</h3>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="name@example.com"
                                    required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <label class="form-label">Password</label>
                                <a href="#" class="text-decoration-none text-muted"
                                    style="font-size: 0.8rem;">Forgot?</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="••••••••"
                                    required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fa-solid fa-right-to-bracket me-2"></i>Sign In
                        </button>



                        <div class="text-center mt-3">
                            <a href="{{ url('/') }}" class="text-muted text-decoration-none"
                                style="font-size: 0.9rem;">
                                <i class="fa-solid fa-arrow-left"></i> Back to Home
                            </a>
                        </div>
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
                        <span class="text-muted">Don't have an account? </span>
                        <a href="{{ route('register') }}" class="text-success fw-bold text-decoration-none">Register</a>
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
