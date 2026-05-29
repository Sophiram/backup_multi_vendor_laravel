<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('user_page_title')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --sidebar-width: 260px;
        }

        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar {
            background: var(--primary-gradient) !important;
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            min-height: 100vh;
            color: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 24px rgba(79, 70, 229, 0.15);
            z-index: 1040;
            /* ឱ្យវាអណ្តែតខ្ពស់ជាងគេ */
        }

        .sidebar-brand {
            color: #fff !important;
            font-weight: 700;
            padding: 2rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 1.3rem;
            text-decoration: none;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-link {
            color: rgba(255, 255, 255, 0.75) !important;
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            text-decoration: none;
            margin: 4px 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff !important;
            transform: translateX(4px);
        }

        .sidebar-item.active .sidebar-link {
            background: #ffffff !important;
            color: #4f46e5 !important;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        /* Navbar Styling */
        .navbar {
            background: #ffffff !important;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .navbar-brand-text {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
        }

        .nav-btn-toggle {
            background: #f1f5f9;
            border: none;
            padding: 8px;
            border-radius: 8px;
            color: #64748b;
            transition: all 0.2s;
        }

        .nav-btn-toggle:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .dropdown-menu {
            border-radius: 12px;
            padding: 8px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .dropdown-item {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
        }

        .main-wrapper {
            min-width: 0;
            width: 100%;
        }

        /* 📱 រៀបចំការ Responsive សម្រាប់អេក្រង់ទូរស័ព្ទ និង Tablet */
        @media (max-width: 991.98px) {
            #sidebar {
                position: fixed;
                left: calc(-1 * var(--sidebar-width));
                /* លាក់ខ្លួនទៅខាងឆ្វេងជាស្រេច */
                height: 100vh;
            }

            #sidebar.show-mobile {
                left: 0;
                /* បង្ហាញខ្លួនមកវិញពេលចុច Toggle */
            }

            /* បង្កើតកម្រាលខ្មៅបិទបាំងពីក្រោយពេល Sidebar បើកលើ Mobile (ជម្រើសបន្ថែមបើចង់បាន) */
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.4);
                z-index: 1030;
                display: none;
            }

            .sidebar-overlay.active {
                display: block;
            }
        }

        /* 💻 សម្រាប់អេក្រង់កុំព្យូទ័រធំៗ (Desktop) */
        @media (min-width: 992px) {
            #sidebar.collapsed {
                margin-left: calc(-1 * var(--sidebar-width));
                /* បិទបើកធម្មតាតាមការចុច */
            }
        }
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="wrapper d-flex">
        <nav id="sidebar">
            <a class="sidebar-brand" href="{{ url('/') }}">
                <i data-lucide="layout-grid" style="width: 24px; height: 24px;"></i>
                <span>User Panel</span>
            </a>
            <ul class="nav flex-column mt-4">
                <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('dashboard') }}">
                        <i data-lucide="layout-dashboard" style="width: 18px; height: 18px;"></i> Dashboard
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('user.history') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('user.history') }}">
                        <i data-lucide="shopping-bag" style="width: 18px; height: 18px;"></i> Order History
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('user.payment') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('user.payment') }}">
                        <i data-lucide="credit-card" style="width: 18px; height: 18px;"></i> Payment
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('user.affiliate') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('user.affiliate') }}">
                        <i data-lucide="users" style="width: 18px; height: 18px;"></i> Affiliate
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('profile.edit') }}">
                        <i data-lucide="user-circle" style="width: 18px; height: 18px;"></i> My Profile
                    </a>
                </li>
            </ul>
        </nav>

        <div class="main-wrapper d-flex flex-column flex-grow-1">
            <nav class="navbar navbar-expand align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 gap-sm-3">
                    <button class="nav-btn-toggle" id="sidebarToggle">
                        <i data-lucide="menu" style="width: 20px; height: 20px;"></i>
                    </button>
                    <span class="navbar-brand-text d-none d-sm-inline-block">Welcome back,
                        {{ Auth::user()->name }}</span>
                </div>

                <div class="navbar-nav align-items-center gap-2">
                    <a class="btn btn-light btn-sm d-flex align-items-center gap-1 border-0 px-2 px-sm-3 py-2 rounded-3 text-secondary fw-medium"
                        href="{{ url('/') }}">
                        <i data-lucide="home" style="width: 16px; height: 16px;"></i> <span
                            class="d-none d-md-inline">Home</span>
                    </a>

                    <div class="dropdown">
                        <a class="btn btn-light btn-sm d-flex align-items-center gap-2 border-0 px-2 px-sm-3 py-2 rounded-3 text-dark fw-semibold dropdown-toggle"
                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                            <!-- ជំនួសការបង្ហាញអក្សរ SO ដោយរូបភាព -->
                            @if (Auth::user()->image)
                                <img src="{{ asset('storage/' . Auth::user()->image) }}"
                                    alt="{{ Auth::user()->name }}" class="rounded-circle"
                                    style="width: 24px; height: 24px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                                    style="width: 24px; height: 24px; font-size: 11px;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                            @endif

                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 mt-2">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 text-secondary"
                                    href="{{ route('profile.edit') }}">
                                    <i data-lucide="settings" style="width: 16px;"></i> Profile Settings
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider opacity-50">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="dropdown-item text-danger d-flex align-items-center gap-2"
                                        style="background: none; width: 100%;">
                                        <i data-lucide="log-out" style="width: 16px;"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="content p-3 p-md-4 flex-grow-1">
                @yield('user_layout')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // អានឡុក Icons
        lucide.createIcons();

        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        // មុខងារបិទបើក Sidebar ឆ្លាតវៃ (គិតគូរទាំង Mobile និង Desktop)
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            if (window.innerWidth < 992) {
                sidebar.classList.toggle('show-mobile');
                overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        });

        // ចុចលើផ្ទៃងងឹតដើម្បីបិទ Sidebar វិញ (សម្រាប់តែលើ Mobile)
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show-mobile');
            overlay.classList.remove('active');
        });
    </script>
</body>

</html>
