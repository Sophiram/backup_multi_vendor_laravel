<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('admin_page_title')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @livewireStyles
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .wrapper {
            display: flex;
            overflow-x: hidden;
            transition: all 0.3s;
        }

        /* Sidebar Premium Style */
        .sidebar {
            width: 240px;
            background: #0f172a;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
            color: #94a3b8;
        }

        .sidebar-brand {
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 1.2rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .sidebar-header {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 1.2rem 1.5rem 0.5rem;
            color: #475569;
            font-weight: 700;
        }

        .sidebar-link {
            color: #94a3b8;
            padding: 0.6rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: 0.3s;
            font-size: 0.85rem;
            /* កែសម្រួលអក្សរតូច */
        }

        .sidebar-link i {
            width: 18px;
            height: 18px;
        }

        .sidebar-link:hover,
        .sidebar-item.active .sidebar-link {
            background: #1e293b;
            color: #3b82f6;
            border-left: 3px solid #3b82f6;
        }

        /* Navbar */
        .navbar {
            background: #fff !important;
            border-bottom: 1px solid #e2e8f0;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }

        .main {
            margin-left: 240px;
            transition: all 0.3s ease;
            width: calc(100% - 240px);
        }

        .wrapper.toggled .sidebar {
            margin-left: -240px;
        }

        .wrapper.toggled .main {
            margin-left: 0;
            width: 100%;
        }

        .content {
            padding: 2rem;
        }

        #sidebarToggleBtn {
            background: transparent;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            display: none;
        }

        @media (max-width: 991px) {
            .sidebar {
                margin-left: -240px;
            }

            .sidebar.active {
                margin-left: 0 !important;
            }

            .sidebar.active~.sidebar-overlay {
                display: block !important;
            }

            .main {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <nav class="sidebar" id="sidebar">
            <a class="sidebar-brand" href="{{ route('admin') }}">
                <i data-lucide="layers"></i> Admin Panel
            </a>
            <ul class="list-unstyled">
                <li class="sidebar-header">Main</li>
                <li class="sidebar-item {{ request()->routeIs('admin') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('admin') }}"><i data-lucide="layout-dashboard"></i>
                        Dashboard</a>
                </li>

                <li class="sidebar-header">Category</li>
                <li class="sidebar-item {{ request()->routeIs('category.create') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('category.create') }}"><i data-lucide="plus-circle"></i>
                        Create Category</a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('category.manage') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('category.manage') }}"><i data-lucide="layers"></i> Manage
                        Category</a>
                </li>

                <li class="sidebar-header">Sub Category</li>
                <li class="sidebar-item {{ request()->routeIs('subcategory.create') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('subcategory.create') }}"><i data-lucide="plus-circle"></i>
                        Create</a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('subcategory.manage') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('subcategory.manage') }}"><i data-lucide="list"></i>
                        Manage</a>
                </li>

                <li class="sidebar-header">Attribute</li>
                <li class="sidebar-item {{ request()->routeIs('productattribute.create') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('productattribute.create') }}"><i data-lucide="tag"></i>
                        Create</a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('productattribute.manage') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('productattribute.manage') }}"><i data-lucide="tags"></i>
                        Manage</a>
                </li>

                <li class="sidebar-header">Discount</li>
                <li class="sidebar-item {{ request()->routeIs('admin.discount.create') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('admin.discount.create') }}"><i data-lucide="percent"></i>
                        Create</a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('admin.discount.manage') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('admin.discount.manage') }}"><i data-lucide="gift"></i>
                        Manage</a>
                </li>

                <li class="sidebar-header">Product</li>
                <li class="sidebar-item {{ request()->routeIs('product.manage') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('product.manage') }}"><i data-lucide="shopping-bag"></i>
                        Manage Product</a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('admin.reviews.manage') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('admin.reviews.manage') }}"><i data-lucide="star"></i>
                        Manage Review</a>
                </li>

                <li class="sidebar-header">Manage</li>
                <li class="sidebar-item {{ request()->routeIs('admin.manage.store') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('admin.manage.store') }}"><i data-lucide="store"></i>
                        Store</a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('admin.manage.users') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('admin.manage.users') }}"><i data-lucide="users"></i>
                        User</a>
                </li>

                <li class="sidebar-header">History</li>
                <li class="sidebar-item {{ request()->routeIs('admin.cart.history') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('admin.cart.history') }}"><i
                            data-lucide="shopping-cart"></i> Cart History</a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('admin.order.history') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('admin.order.history') }}"><i data-lucide="file-text"></i>
                        Order History</a>
                </li>

                <li class="sidebar-header">Payment</li>
                <li class="sidebar-item {{ request()->routeIs('admin.payment.add') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('admin.payment.add') }}"><i data-lucide="credit-card"></i>
                        Add Payment</a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('admin.payment.manage') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('admin.payment.manage') }}"><i data-lucide="list"></i>
                        Manage Payments</a>
                </li>


                <li class="sidebar-header">Settings</li>
                <li class="sidebar-item {{ request()->routeIs('admin.pending') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('admin.pending') }}"><i data-lucide="user-check"></i>
                        Pending Vendors</a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('admin.settings') }}"><i data-lucide="settings"></i>
                        Settings</a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="main">
            <nav class="navbar px-4 d-flex justify-content-between align-items-center">
                <button class="hamburger" type="button" id="sidebarToggleBtn">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <a href="{{ url('/') }}" target="_blank" class="text-decoration-none text-dark"><i
                            data-lucide="globe"></i> Visit Site</a>
                    <div class="dropdown">
                        <a href="#" data-bs-toggle="dropdown"><img src="https://ui-avatars.com/api/?name=Admin"
                                class="avatar"></a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit"
                                        class="dropdown-item text-danger">Logout</button></form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="content">
                @yield('admin_layout')

                @if (session('success'))
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: "{{ session('success') }}",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    </script>
                @endif
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        lucide.createIcons();
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const wrapper = document.querySelector('.wrapper');
            const overlay = document.getElementById('sidebarOverlay');

            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('active');
                wrapper.classList.toggle('toggled');
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                wrapper.classList.remove('toggled');
            });
        });
    </script>
    @livewireScripts
</body>

</html>
