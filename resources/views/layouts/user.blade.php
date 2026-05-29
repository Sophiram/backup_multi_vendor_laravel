<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>QuickCart - Premium Multi-Vendor Marketplace</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
        }

        /* -----------------------------------------
           ✨ MAIN HEADER STYLES
        -------------------------------------------- */
        .main-header {
            background: #ffffff;
            border-bottom: 1px solid #f3f4f6;
            padding: 12px 0;
        }

        .brand-logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: #4f46e5;
            text-decoration: none;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
        }

        .brand-logo span {
            color: #10b981;
        }

        .header-search-form {
            max-width: 550px;
            width: 100%;
        }

        /* 🔍 SEARCH BAR RESTRUCTURE */
        .search-input-container {
            display: flex;
            box-shadow: none !important;
            align-items: center;
            background-color: #f3f4f6;
            border-radius: 12px;
            padding: 4px 14px;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            width: 100%;
        }

        .search-input-container:focus-within {
            background-color: #ffffff;
            border-color: #4f46e5 !important;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15) !important;
        }

        .search-input-container input {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            background: transparent !important;
            width: 100%;
            padding: 8px 0 !important;
            font-size: 0.95rem;
            color: #1f2937;
        }

        .search-submit-btn {
            color: #4f46e5;
            background: transparent;
            border: none;
            outline: none;
            padding-left: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .account-btn {
            background: #4f46e5;
            color: #ffffff !important;
            padding: 11px 25px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
            transition: all 0.2s;
        }

        .account-btn:hover {
            background: #4338ca;
            transform: translateY(-1px);
        }

        /* -----------------------------------------
           🎨 PREMIUM NAVBAR WITH ROUNDED HOVER/ACTIVE
        -------------------------------------------- */
        .navigation-bar {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%) !important;
            padding: 8px 0 !important;
            /* បន្ថែម padding លើក្រោមដើម្បីកុំឱ្យប៊ូតុងមូលបុកគែម */
            border-bottom: none;
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.15);
            border-radius: 16px !important;
            margin-top: 16px;
        }

        .nav-menu-container {
            display: flex;
            align-items: center;
            gap: 10px;
            /* បង្កើតគម្លាតរវាងប៊ូតុងនីមួយៗ */
        }

        .menu-item-link {
            font-size: 0.92rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9) !important;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 12px !important;
            /* ✨ ធ្វើឱ្យប៊ូតុងមានរាងមូលពេល Hover និង Active */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
        }

        .menu-item-link:hover,
        .menu-item-link[aria-expanded="true"] {
            color: #ffffff !important;
            background: linear-gradient(135deg, #06b6d4 0%, #10b981 100%) !important;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.35);
        }

        .menu-item-link.active {
            background: #00cbb4 !important;
            color: white !important;
            box-shadow: 0 4px 15px rgba(0, 203, 180, 0.35);
        }

        .menu-item-link:hover i.text-danger,
        .menu-item-link.active i.text-danger {
            color: #ffffff !important;
        }

        .custom-nav-dropdown .dropdown-toggle::after {
            display: none !important;
        }

        .custom-nav-dropdown .drop-icon {
            font-size: 0.75rem;
            transition: transform 0.25s ease;
            color: rgba(255, 255, 255, 0.8) !important;
        }

        .custom-nav-dropdown.show .drop-icon,
        .custom-nav-dropdown .menu-item-link[aria-expanded="true"] .drop-icon {
            transform: rotate(180deg);
            color: #ffffff !important;
        }

        .premium-dropdown-menu {
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            border-radius: 20px !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15) !important;
            padding: 12px !important;
            min-width: 290px;
            margin-top: 8px !important;
            /* លៃតម្រូវគម្លាតធ្លាក់ចុះក្រោមបន្តិច */
            background: #ffffff;
            z-index: 1060;
        }

        .dropdown-header-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #9ca3af;
            font-weight: 700;
            padding: 8px 16px 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            margin-bottom: 8px;
        }

        .premium-dropdown-item {
            padding: 10px 14px !important;
            border-radius: 12px !important;
            color: #374151 !important;
            font-weight: 600;
            font-size: 0.88rem;
            transition: all 0.2s ease !important;
        }

        .category-icon-wrapper {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background-color: #f3f4f6;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            font-size: 0.85rem;
        }

        .premium-dropdown-item .arrow-icon {
            font-size: 0.75rem;
            color: #d1d5db;
            opacity: 0;
            transform: translateX(-5px);
            transition: all 0.2s ease;
        }

        .premium-dropdown-item:hover {
            background-color: #f0fdf4 !important;
            color: #16a34a !important;
        }

        .premium-dropdown-item:hover .category-icon-wrapper {
            background-color: #dcfce7;
            color: #16a34a;
            transform: scale(1.05);
        }

        .premium-dropdown-item:hover .arrow-icon {
            opacity: 1;
            transform: translateX(0);
            color: #16a34a;
        }

        .dropdown-item:hover {
            background-color: #f4f5fa !important;
            color: #4f46e5 !important;
        }

        .dropdown-item:hover i {
            color: #4f46e5 !important;
        }

        #accountDropdown:hover {
            background-color: #4338ca !important;
        }

        /* -----------------------------------------
           🏢 PREMIUM MODERN FOOTER STYLES
        -------------------------------------------- */
        .premium-footer {
            background: #0b1329;
            /* ពណ៌ Dark បែបទំនើប */
            font-family: 'Plus Jakarta Sans', sans-serif;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .footer-brand {
            color: #ffffff;
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .footer-brand span {
            color: #10b981;
        }

        .footer-heading {
            color: #f8fafc;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            position: relative;
            padding-bottom: 12px;
        }

        /* បន្ថែមបន្ទាត់តូចពីក្រោម Heading */
        .footer-heading::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 35px;
            height: 3px;
            background: linear-gradient(90deg, #4f46e5, #10b981);
            border-radius: 2px;
        }

        .footer-desc {
            color: #94a3b8;
            font-size: 0.9rem;
            line-height: 1.75;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* បន្ថែម Effect ពេល Hover លើ Link */
        .footer-links a::before {
            content: '\f105';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 0.75rem;
            opacity: 0;
            transform: translateX(-5px);
            transition: all 0.25s ease;
            color: #10b981;
        }

        .footer-links a:hover {
            color: #ffffff;
            transform: translateX(4px);
        }

        .footer-links a:hover::before {
            opacity: 1;
            transform: translateX(0);
        }

        .footer-contact-list li {
            color: #94a3b8;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .contact-icon-box {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #4f46e5;
            transition: all 0.3s;
        }

        .footer-contact-list li:hover .contact-icon-box {
            background: #4f46e5;
            color: #fff;
        }

        .social-icon-link {
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.04);
            color: #94a3b8;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .social-icon-link:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #ffffff;
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .bg-slate {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
            border-radius: 12px !important;
        }

        .newsletter-form .form-control:focus {
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.25);
            background-color: rgba(255, 255, 255, 0.08) !important;
            border-color: #4f46e5 !important;
        }

        .btn-subscribe {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px !important;
            padding: 0 20px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-subscribe:hover {
            opacity: 0.9;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .footer-divider {
            border-color: rgba(255, 255, 255, 0.08);
        }

        .footer-copyright {
            color: #64748b;
            font-size: 0.88rem;
        }

        /* 🏦 RESTRUCTURED PAYMENT BADGES */
        .payment-container {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .pay-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            /* background: rgba(255, 255, 255, 0.04); */
            /* padding: 6px 14px; */
            border-radius: 10px;
            /* border: 1px solid rgba(255, 255, 255, 0.05); */
            height: 30px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .pay-badge.badge-bakong {
            background-color: #9E1B22;
        }

        .pay-badge.badge-bakong img {
            padding: 4px;
            filter: brightness(0) invert(1);
        }

        .pay-badge img {
            /* max-height: 22px; */
            width: 100%;
            /* width: auto; */
            height: 100%;
            object-fit: contain;
        }

        /* .pay-badge.badge-bakong img {
            filter: brightness(0) invert(1);
        } */

        .pay-badge:hover {
            /* background: #ffffff; */
            border-color: #006eff;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .pay-badge:hover img {
            filter: none !important;
            /* ឱ្យរូបភាពធនាគារចេញពណ៌ដើមពេល Hover */
        }

        .hover-search-item:hover {
            background-color: #f3f4f6 !important;
        }

        .hover-search-item span {
            transition: color 0.2s ease;
        }

        .hover-search-item:hover span.text-dark {
            color: #4f46e5 !important;
        }




        /* -----------------------------------------
           📱 RESPONSIVE MEDIA QUERIES
        -------------------------------------------- */
        @media (max-width: 991.98px) {
            .brand-logo {
                font-size: 1.3rem;
            }

            .menu-item-link {
                padding: 12px 16px;
                width: 100%;
                /* border-radius: 0px !important; លើទូរស័ព្ទឱ្យវាពេញធម្មតាវិញ */
            }

            .nav-menu-container {
                gap: 8px;
            }

            .premium-dropdown-menu {
                position: static !important;
                box-shadow: none !important;
                border: none !important;
                background: rgba(255, 255, 255, 0.05) !important;
                padding: 0 !important;
                margin-top: 0px !important;
            }

            .premium-dropdown-item {
                color: rgba(255, 255, 255, 0.8) !important;
            }

            .premium-dropdown-item:hover {
                background-color: rgba(255, 255, 255, 0.1) !important;
                color: #fff !important;
            }

            .dropdown-header-title {
                color: rgba(255, 255, 255, 0.5);
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .category-icon-wrapper {
                background-color: rgba(255, 255, 255, 0.1);
                color: #fff;
            }

            .navigation-bar {
                border-radius: 16px !important;
                margin-top: 10px;
                padding: 0 !important;
            }
        }

        @media (max-width: 575.98px) {
            .account-text {
                display: none;
            }

            #accountDropdown {
                padding: 12px 12px !important;
            }

            .brand-logo {
                font-size: 1.2rem;
            }
        }
    </style>
    @livewireStyles
</head>

<body>

    {{-- 🛒 MAIN HEADER --}}
    <header class="main-header sticky-top shadow-sm">
        <div class="container d-flex align-items-center justify-content-between gap-2 gap-md-3">

            <div class="d-flex align-items-center gap-1 gap-sm-2">
                <button class="navbar-toggler d-block d-lg-none text-dark border-0 p-0 me-2" type="button"
                    data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar"
                    aria-expanded="false" aria-label="Toggle navigation" style="font-size: 1.25rem;">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <a href="/" class="brand-logo flex-shrink-0">
                    <i class="fa-solid fa-bag-shopping me-1 me-sm-2"></i>Quick<span>Cart</span>
                </a>
            </div>

            <div class="header-search-form d-none d-md-block flex-grow-1 mx-2 mx-lg-4">
                @livewire('product-search-component')
            </div>

            <div class="d-flex align-items-center gap-2 gap-md-3 flex-shrink-0">
                @livewire('wishlist-icon-component')
                @livewire('cart-component')



                <div class="dropdown">
                    <button class="btn text-white d-flex align-items-center gap-1 gap-md-2 px-2 px-md-3 py-2 shadow-sm"
                        type="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                        style="background-color: #4f46e5; border-radius: 12px; font-weight: 600; border: none; transition: all 0.2s;">
                        <i class="fa-regular fa-user" style="font-size: 1.1rem;"></i>
                        @auth
                            <span class="account-text">{{ Str::limit(Auth::user()->name, 10) }}</span>
                        @else
                            <span class="account-text">Account</span>
                        @endauth
                        <i class="fa-solid fa-chevron-down ms-1" style="font-size: 0.75rem;"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2 p-2"
                        aria-labelledby="accountDropdown" style="border-radius: 16px; min-width: 200px;">
                        @guest
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-secondary"
                                    href="{{ route('login') }}" style="border-radius: 10px; font-weight: 500;">
                                    <i class="fa-solid fa-right-to-bracket text-muted"></i> Sign In
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-secondary"
                                    href="{{ route('register') }}" style="border-radius: 10px; font-weight: 500;">
                                    <i class="fa-solid fa-user-plus text-muted"></i> Create Account
                                </a>
                            </li>
                        @endguest

                        @auth
                            <li>
                                <h6 class="dropdown-header text-dark fw-bold pb-2 border-bottom mb-2"
                                    style="font-size: 0.85rem;">
                                    Hi, {{ Auth::user()->name }}
                                </h6>
                            </li>

                            {{-- បង្ហាញ Dashboard ទៅតាម Role របស់ user --}}
                            @if (Auth::user()->role == 'admin')
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-secondary"
                                        href="/admin/dashboard">
                                        <i class="fa-solid fa-user-shield text-muted"></i> Admin Dashboard
                                    </a>
                                </li>
                            @elseif (Auth::user()->role == 'vendor')
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-secondary"
                                        href="/vendor/dashboard">
                                        <i class="fa-solid fa-gauge text-muted"></i> Vendor Panel
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-secondary"
                                        href="{{ route('dashboard') }}">
                                        <i class="fa-solid fa-house-user text-muted"></i> User Dashboard
                                    </a>
                                </li>
                            @endif

                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-secondary"
                                    href="/profile">
                                    <i class="fa-regular fa-id-card text-muted"></i> My Profile
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider my-2" style="border-color: #f1f5f9;">
                            </li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger fw-semibold"
                                        href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        <i class="fa-solid fa-power-off"></i> Sign Out
                                    </a>
                                </form>
                            </li>
                        @endauth

                    </ul>
                </div>
            </div>
        </div>

        <div class="container d-block d-md-none mt-2 pt-1">
            @livewire('product-search-component')
        </div>
    </header>


    {{-- 🗺️ PREMIUM DOUBLE-GRADIENT NAVIGATION BAR --}}
    <div class="container">
        <nav class="navigation-bar navbar navbar-expand-lg navbar-dark p-0" id="mainNavbar">
            <div class="w-100 px-3"> <!-- បន្ថែមដកឃ្លាខាងក្នុងបន្តិច -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <div
                        class="nav-menu-container flex-column flex-lg-row w-100 justify-content-lg-center align-items-stretch py-2 py-lg-0">

                        {{-- 1. Trending Link --}}
                        <a href="/"
                            class="menu-item-link {{ request()->is('/') || request()->is('trending*') ? 'active' : '' }}">
                            <i class="fa-solid fa-fire me-2 text-danger"></i>Trending
                        </a>

                        {{-- 2. Categories Dropdown --}}
                        <div class="dropdown custom-nav-dropdown">
                            <a href="#"
                                class="menu-item-link dropdown-toggle d-flex align-items-center justify-content-between gap-2 w-100 {{ request()->is('category*') ? 'active' : '' }}"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span>Categories</span>
                                <i class="fa-solid fa-angle-down drop-icon"></i>
                            </a>

                            <div class="dropdown-menu premium-dropdown-menu animate__animated animate__fadeIn">
                                <div class="dropdown-header-title">Browse Categories</div>

                                @foreach ($navbarCategories as $category)
                                    <a href="{{ route('productby.category', $category->category_name) }}"
                                        class="dropdown-item premium-dropdown-item {{ request()->segment(2) == $category->category_name ? 'active-subcategory' : '' }}">
                                        <div class="d-flex align-items-center justify-content-between w-100">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="category-icon-wrapper">
                                                    <i class="fa-solid fa-layer-group"></i>
                                                </div>
                                                <span class="category-name">{{ $category->category_name }}</span>
                                            </div>
                                            <i class="fa-solid fa-chevron-right arrow-icon"></i>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- 3. Static Links --}}
                        <a href="/discounts"
                            class="menu-item-link {{ request()->is('discounts*') ? 'active' : '' }}">Discounts</a>
                        <a href="/gift-collections"
                            class="menu-item-link {{ request()->is('gift-collections*') ? 'active' : '' }}">Gift
                            Collections</a>
                        <a href="/stores"
                            class="menu-item-link {{ request()->is('stores*') ? 'active' : '' }}">Stores</a>

                    </div>
                </div>
            </div>
        </nav>
    </div>


    {{-- 💻 MAIN CONTENT SLOT --}}
    <main class="container-fluid p-0 m-0 min-vh-50 py-4">
        <div class="w-100 class-wrapper">
            {{ $slot ?? '' }}
            @yield('home')
        </div>
    </main>


    {{-- 🏢 PREMIUM MODERN FOOTER --}}
    <footer class="premium-footer text-light pt-5 pb-4">
        <div class="container">
            <div class="row g-4">

                {{-- Column 1: Brand & Desc --}}
                <div class="col-12 col-md-6 col-lg-4 mb-3 mb-lg-0">
                    <h5 class="footer-brand mb-3"><i
                            class="fa-solid fa-bag-shopping me-2 text-primary"></i>Quick<span>Cart</span></h5>
                    <p class="footer-desc mb-4">
                        Connecting buyers and sellers instantly. Enjoy a seamless, secure shopping experience with high
                        marketplace standards and verified local vendors.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="#" class="social-icon-link" aria-label="Facebook"><i
                                class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="social-icon-link" aria-label="Instagram"><i
                                class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="social-icon-link" aria-label="Telegram"><i
                                class="fa-brands fa-telegram"></i></a>
                        <a href="#" class="social-icon-link" aria-label="YouTube"><i
                                class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                {{-- Column 2: Our Company --}}
                <div class="col-6 col-md-6 col-lg-2 ps-lg-4">
                    <h5 class="footer-heading mb-4">Our Company</h5>
                    <ul class="footer-links list-unstyled m-0 p-0">
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Delivery Info</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                    </ul>
                </div>

                {{-- Column 3: Store Contact --}}
                <div class="col-6 col-md-6 col-lg-3">
                    <h5 class="footer-heading mb-4">Store Contact</h5>
                    <ul class="footer-contact-list list-unstyled m-0 p-0">
                        <li class="d-flex gap-3 mb-3 align-items-start">
                            <div class="contact-icon-box flex-shrink-0">
                                <i class="fa-solid fa-map-pin"></i>
                            </div>
                            <span>99 Main St. Teuk Thla, Khan Sen Sok, Phnom Penh, Cambodia</span>
                        </li>
                        <li class="d-flex gap-3 mb-3 align-items-center">
                            <div class="contact-icon-box flex-shrink-0">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <span>+00 123-456-789</span>
                        </li>
                    </ul>
                </div>

                {{-- Column 4: Newsletter --}}
                <div class="col-12 col-md-6 col-lg-3">
                    <h5 class="footer-heading mb-4">Our Newsletter</h5>
                    <p class="footer-desc mb-3" style="font-size: 0.85rem;">Subscribe to receive instant updates on
                        seasonal promotions.</p>
                    <div class="newsletter-form">
                        <div class="input-group gap-2">
                            <input type="email" class="form-control bg-slate border-0 px-3"
                                placeholder="Email address...">
                            <button class="btn btn-subscribe" type="button">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4 footer-divider">

            {{-- Bottom Footer: Copyright & Payments --}}
            <div
                class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 text-center text-md-start pt-2">
                <p class="mb-0 footer-copyright">
                    &copy; {{ date('Y') }} <span class="text-light fw-semibold">QuickCart Marketplace</span>. All
                    Rights Reserved.
                </p>

                <div class="payment-container justify-content-center">
                    <span class="pay-badge badge-aba" title="ABA Pay">
                        <img src="{{ asset('home_asset/img/aba-pay-web.png') }}" alt="ABA Pay">
                    </span>
                    <span class="pay-badge badge-bakong" title="Bakong">
                        <img src="{{ asset('home_asset/img/bakong.svg') }}" alt="Bakong">

                    </span>
                    <span class="pay-badge badge-visa" title="Visa / MasterCard">
                        <img src="{{ asset('home_asset/img/credit-debit-card.png') }}" alt="Visa">

                    </span>
                    <span class="pay-badge badge-aceleda" title="ACLEDA">
                        <img src="{{ asset('home_asset/img/aceleda.png') }}" alt="ACLEDA">
                    </span>
                </div>
            </div>
        </div>
    </footer>
    @if (session('vendor_registered'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Registered Successfully!',
                text: '{{ session('vendor_registered') }}',
                confirmButtonText: 'OK',
                allowOutsideClick: false
            });
        </script>
    @endif

    @livewire('global-cart-manager')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @livewireScripts
</body>

</html>
