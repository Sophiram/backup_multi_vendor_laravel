<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
    <meta name="author" content="AdminKit">
    <meta name="keywords"
        content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="{{ asset('img/icons/icon-48x48.png') }}" />

    <title>@yield('vendor_page_title', 'Vendor Panel')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('admin_asset/css/app.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @livewireStyles

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .sidebar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
        }

        .avatar {
            border: 2px solid #e2e8f0;
            padding: 2px;
            background: #fff;
        }

        /* 🎯 បន្ថែមសម្រាប់កែសម្រួលពណ៌អក្សរ និង Icon ក្នុង Sidebar ឱ្យភ្លឺច្បាស់ងាយស្រួលមើល */
        .sidebar-nav .sidebar-header {
            color: #ced4da !important;
            /* ធ្វើឱ្យអក្សរក្បាលរង (Main, Store, Settings...) ភ្លឺជាងមុន */
            font-weight: 600;
        }

        .sidebar-nav .sidebar-link {
            color: #e9ecef !important;
            /* ប្តូរពណ៌អក្សរធម្មតានៃ Menu ឱ្យភ្លឺច្បាស់ងាយមើល */
            background: transparent;
            transition: all 0.2s ease;
        }

        .sidebar-nav .sidebar-link i,
        .sidebar-nav .sidebar-link svg {
            color: #adb5bd !important;
            /* ធ្វើឱ្យ Icon ភ្លឺច្បាស់ដូចគ្នា */
        }

        /* ពេលយក Mouse ទៅដាក់ពីលើ (Hover) */
        .sidebar-nav .sidebar-link:hover {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.05) !important;
            /* បន្ថែមផ្ទៃ Background ស្រាលពេល Hover */
        }

        .sidebar-nav .sidebar-link:hover i,
        .sidebar-nav .sidebar-link:hover svg {
            color: #ffffff !important;
            /* Icon ភ្លឺពណ៌សពេល Hover */
        }

        /* សម្រាប់ Menu ណាដែលកំពុងដំណើរការ (Active) */
        .sidebar-item.active .sidebar-link {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.1) !important;
            /* បញ្ជាក់ពីការ Active ឱ្យច្បាស់ */
            border-left: 3px solid #3b82f6;
            /* បន្ថែមបន្ទាត់ពណ៌ខៀវឆ្នូតខាងឆ្វេងដើម្បីឱ្យកាន់តែលេចធ្លោ */
        }

        .sidebar-item.active .sidebar-link i,
        .sidebar-item.active .sidebar-link svg {
            color: #3b82f6 !important;
            /* Icon ប្តូរជាពណ៌ខៀវពេល Active */
        }

        /* --------------------------------------------------------
           📱 កែប្រែតែលើអេក្រង់តូចប៉ុណ្ណោះ (Mobile Styles)
           💻 អេក្រង់ធំរក្សាទុកសភាពដើមរបស់ AdminKit ១០០%
        ----------------------------------------------------------- */
        @media (max-width: 991.98px) {
            .sidebar {
                position: fixed !important;
                top: 0;
                bottom: 0;
                left: 0;
                z-index: 1060;
                margin-left: -260px !important;
                /* លាក់ខ្លួនទៅខាងឆ្វេង */
                transition: margin-left 0.3s ease-in-out;
            }

            /* បង្ហាញ Sidebar នៅពេលចុច Toggle */
            .sidebar.show-mobile {
                margin-left: 0 !important;
            }

            /* ផ្ទៃងងឹតខាងក្រោយ (Overlay Backdrop) */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.4);
                z-index: 1050;
                /* នៅពីក្រោម Sidebar តែនៅលើ Content */
            }

            .sidebar-overlay.show {
                display: block;
            }

            /* ការពារកុំឱ្យ Content រួញខូចទម្រង់លើ Mobile */
            .main {
                width: 100% !important;
                min-width: 100% !important;
            }
        }
    </style>
</head>

<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="wrapper">
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <a class="sidebar-brand text-decoration-none" href="{{ route('vendor') }}">
                    <span class="align-middle">Vendor Dashboard</span>
                </a>

                <ul class="sidebar-nav">
                    <li class="sidebar-header">Main</li>

                    <li class="sidebar-item {{ request()->routeIs('vendor') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('vendor') }}">
                            <i class="align-middle" data-feather="grid"></i>
                            <span class="align-middle">Dashboard</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ request()->routeIs('vendor.sales.report') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('vendor.sales.report') }}">
                            <i class="align-middle" data-feather="bar-chart-2"></i>
                            <span class="align-middle">Sales Reports</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ request()->routeIs('vendor.orders.history') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('vendor.orders.history') }}">
                            <i class="align-middle" data-feather="shopping-cart"></i>
                            <span class="align-middle">Order History</span>
                        </a>
                    </li>

                    <li class="sidebar-header">Store</li>
                    <li class="sidebar-item {{ request()->routeIs('vendor.store.create') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('vendor.store.create') }}">
                            <i class="align-middle" data-feather="plus-square"></i>
                            <span class="align-middle">Create Store</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('vendor.store.manage') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('vendor.store.manage') }}">
                            <i class="align-middle" data-feather="sliders"></i>
                            <span class="align-middle">Manage Stores</span>
                        </a>
                    </li>

                    <li class="sidebar-header">Product</li>
                    <li class="sidebar-item {{ request()->routeIs('vendor.product.create') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('vendor.product.create') }}">
                            <i class="align-middle" data-feather="package"></i>
                            <span class="align-middle">Create Product</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('vendor.product.manage') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('vendor.product.manage') }}">
                            <i class="align-middle" data-feather="list"></i>
                            <span class="align-middle">Manage Products</span>
                        </a>
                    </li>

                    <li class="sidebar-header">Attribute</li>
                    <li class="sidebar-item {{ request()->routeIs('vendor.attribute.create') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('vendor.attribute.create') }}">
                            <i class="align-middle" data-feather="folder-plus"></i>
                            <span class="align-middle">Create Attribute</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('vendor.attribute.manage') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('vendor.attribute.manage') }}">
                            <i class="align-middle" data-feather="layers"></i>
                            <span class="align-middle">Manage Attributes</span>
                        </a>
                    </li>

                    <li class="sidebar-header">Settings</li>
                    <li class="sidebar-item {{ request()->routeIs('vendor.settings') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('vendor.settings') }}">
                            <i class="align-middle" data-feather="settings"></i>
                            <span class="align-middle">Settings</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->routeIs('vendor.profile') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('vendor.profile') }}">
                            <i class="align-middle" data-feather="user"></i>
                            <span class="align-middle">Profile</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="main">
            <nav class="navbar navbar-expand navbar-light navbar-bg shadow-sm" style="padding: 0.875rem 1.25rem;">
                <a class="sidebar-toggle js-sidebar-toggle text-decoration-none" href="#"
                    id="sidebarToggleBtn">
                    <i class="hamburger align-self-center"></i>
                </a>

                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align">
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-1 text-secondary"
                                href="{{ url('/') }}" target="_blank">
                                <i data-feather="globe" class="align-middle" style="width: 18px; height: 18px;"></i>
                                <span class="small fw-medium">View Store</span>
                            </a>
                        </li>

                        <li class="nav-item dropdown ms-2">
                            <a class="nav-icon dropdown-toggle" href="#" id="alertsDropdown"
                                data-bs-toggle="dropdown">
                                <div class="position-relative">
                                    <i class="align-middle text-secondary" data-feather="bell"></i>
                                    <span class="indicator bg-primary">0</span>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0"
                                aria-labelledby="alertsDropdown">
                                <div class="dropdown-menu-header p-3 border-bottom fw-bold small">0 New Notifications
                                </div>
                                <div class="list-group">
                                    <div class="text-center p-4 text-muted small">No new notifications</div>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown ms-2">
                            <a class="nav-link dropdown-toggle text-decoration-none" href="#"
                                data-bs-toggle="dropdown">
                                <!-- ពិនិត្យមើលរូបភាពពី storage ប្រសិនបើគ្មានទេ ប្រើ UI Avatars -->
                                <img src="{{ auth()->user()->image ? asset('storage/' . auth()->user()->image) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=4f46e5&color=fff&bold=true' }}"
                                    class="avatar img-fluid rounded-circle me-1" alt="{{ auth()->user()->name }}"
                                    style="width: 32px; height: 32px; object-fit: cover;" />

                                <span
                                    class="text-dark small fw-semibold d-none d-sm-inline-block">{{ auth()->user()->name }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="dropdown-header small text-muted">Manage Account</div>
                                <a class="dropdown-item d-flex align-items-center gap-2"
                                    href="{{ route('vendor.profile') }}">
                                    <i class="align-middle text-secondary" data-feather="user"
                                        style="width: 16px; height: 16px;"></i> Profile
                                </a>
                                <a class="dropdown-item d-flex align-items-center gap-2"
                                    href="{{ route('vendor.settings') }}">
                                    <i class="align-middle text-secondary" data-feather="settings"
                                        style="width: 16px; height: 16px;"></i> Settings
                                </a>
                                <div class="dropdown-divider"></div>

                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit"
                                        class="dropdown-item text-danger d-flex align-items-center gap-2">
                                        <i class="align-middle" data-feather="log-out"
                                            style="width: 16px; height: 16px;"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="content py-4">
                <div class="container-fluid px-4">
                    @yield('vendor_layout')
                </div>
            </main>

            <footer class="footer border-top bg-white py-3">
                <div class="container-fluid px-4">
                    <div class="row text-muted">
                        <div class="col-6 text-start">
                            <p class="mb-0 small">
                                <strong>QuickCart Marketplace</strong> &copy; {{ date('Y') }}
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item"><a class="text-muted small text-decoration-none"
                                        href="#">Support</a></li>
                                <li class="list-inline-item"><a class="text-muted small text-decoration-none"
                                        href="#">Privacy</a></li>
                                <li class="list-inline-item"><a class="text-muted small text-decoration-none"
                                        href="#">Terms</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('admin_asset/js/app.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const sidebar = document.querySelector('#sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function(e) {
                    // ពិនិត្យមើលទំហំអេក្រង់
                    if (window.innerWidth < 992) {
                        // 📱 សម្រាប់អេក្រង់តូច (Mobile)៖ បិទ/បើក Sidebar តាមរយៈ Class របស់យើង
                        e.preventDefault();
                        e.stopPropagation();

                        sidebar.classList.remove('collapsed');
                        sidebar.classList.toggle('show-mobile');

                        if (sidebar.classList.contains('show-mobile')) {
                            overlay.classList.add('show');
                        } else {
                            overlay.classList.remove('show');
                        }
                    } else {
                        // 💻 សម្រាប់អេក្រង់ធំ (Desktop)៖ ទុកឱ្យកូដដើមរបស់ AdminKit នៅក្នុង app.js ធ្វើការដោយស្វ័យប្រវត្ត (យើងមិនបាច់អន្តរាគមន៍ទេ)
                    }
                });
            }

            // ចុចលើ Overlay ផ្ទៃខ្មៅដើម្បីបិទ Sidebar លើ Mobile
            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('show-mobile');
                    overlay.classList.remove('show');
                });
            }

            // បោសសម្អាត Class ពេលអ្នកប្រើពង្រីកអេក្រង់ត្រឡប់ទៅ Desktop វិញ
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    if (sidebar) sidebar.classList.remove('show-mobile');
                    if (overlay) overlay.classList.remove('show');
                }
            });
        });
    </script>
    @livewireScripts
</body>

</html>
