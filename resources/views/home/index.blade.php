@extends('layouts.user')

@section('home')
    {{-- 🔗 បញ្ចូល Google Fonts ឡូយៗ និងទាក់ទាញ --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap"
        rel="stylesheet">

    {{-- 🌟 HERO BANNER SECTION (Premium Soft Gradient Theme) --}}
    <div class="hero-wrapper">
        <div class="container">
            <div class="row g-4 align-items-stretch">

                {{-- 🎇 ផ្នែកទី ១: Big Promo Banner (ខាងឆ្វេង - Vibrant Soft Gradient) --}}
                <div class="col-12 col-lg-8">
                    <div class="main-hero-banner position-relative overflow-hidden h-100 d-flex align-items-center">
                        {{-- Soft Lighting Glows ជំនួយពន្លឺ --}}
                        <div class="glow-circle bg-white opacity-20 position-absolute"
                            style="top: -20%; right: -10%; width: 300px; height: 300px; filter: blur(80px); border-radius: 50%;">
                        </div>

                        <div class="row w-100 align-items-center g-4 position-relative z-index-2 m-0 p-0">
                            {{-- អក្សរព័ត៌មានលម្អិត --}}
                            <div class="col-12 col-md-7 text-start p-0 ps-md-4">
                                <span
                                    class="badge hero-promo-badge mb-3 animate__animated animate__flash animate__infinite animate__slower">Limited
                                    Offer</span>
                                {{-- <h1 class="fw-extrabold text-white mb-2 line-height-1 hero-discount-text">
                                    {{ number_format($homepagesetting->discount_percent, 0) }}% <span
                                        class="fs-2 fw-bold text-warning">OFF</span>
                                </h1>
                                <h3 class="fw-bold text-white mb-3 hero-title-text">
                                    {{ $homepagesetting->discount_heading }}
                                </h3>
                                <p class="small mb-4 hero-desc-text text-white-50">
                                    {{ $homepagesetting->discount_subheading }}
                                </p> --}}

                                @if ($homepagesetting)
                                    <h1 class="fw-extrabold text-white mb-2 line-height-1 hero-discount-text">
                                        {{ number_format($homepagesetting->discount_percent ?? 0, 0) }}%
                                        <span class="fs-2 fw-bold text-warning">OFF</span>
                                    </h1>
                                    <h3 class="fw-bold text-white mb-3 hero-title-text">
                                        {{ $homepagesetting->discount_heading ?? 'Welcome to our store' }}
                                    </h3>
                                    <p class="small mb-4 hero-desc-text text-white-50">
                                        {{ $homepagesetting->discount_subheading ?? 'Best quality products for you.' }}
                                    </p>
                                @else
                                    {{-- បង្ហាញអ្វីដែលសាមញ្ញប្រសិនបើគ្មានទិន្នន័យ --}}
                                    <h1 class="fw-extrabold text-white mb-2">Welcome!</h1>
                                @endif

                                @if (isset($homepagesetting->discountedProduct))
                                    <a href="{{ route('product.details', ['productId' => $homepagesetting->discountedProduct->id]) }}"
                                        class="btn btn-hero-action px-4 py-2-5 fw-bold text-dark rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                                        <i class="fa-solid fa-bag-shopping"></i> Shop Deals
                                    </a>
                                @else
                                    <a href="#"
                                        class="btn btn-hero-action px-4 py-2-5 fw-bold text-dark rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                                        <i class="fa-solid fa-bag-shopping"></i> Shop Deals
                                    </a>
                                @endif
                            </div>

                            {{-- រូបភាពផលិតផលអណ្តែត --}}
                            <div class="col-12 col-md-5 d-flex justify-content-center align-items-center p-0">
                                <div class="hero-image-container">
                                    @if (isset($homepagesetting->discountedProduct) && $homepagesetting->discountedProduct->images->first())
                                        <img src="{{ asset('storage/' . $homepagesetting->discountedProduct->images->first()->image_path) }}"
                                            alt="Discounted Product" class="img-fluid floating-product-img">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 🎴 ផ្នែកទី ២: Side Cards (ខាងស្តាំ ២ ជាន់) --}}
                <div class="col-12 col-lg-4 d-flex flex-column gap-4">

                    {{-- 🛋️ Card ខាងលើ (Featured Product 1) --}}
                    <div
                        class="side-promo-card-new top-card position-relative overflow-hidden flex-grow-1 d-flex align-items-center">
                        <div class="row w-100 align-items-center g-0 position-relative z-index-2 m-0">
                            <div class="col-7 p-3 d-flex flex-column justify-content-center text-start">
                                <span class="text-warning small fw-bold text-uppercase mb-1"
                                    style="letter-spacing: 1px;">Featured</span>
                                <h4 class="fw-bold text-white mb-1 side-card-title">
                                    {{ $homepagesetting->featuredProduct1->product_name ?? 'Comfy' }}
                                </h4>
                                <p class="small text-white-50 mb-3 fw-semibold">
                                    {{ isset($homepagesetting->featuredProduct1) ? '$' . number_format($homepagesetting->featuredProduct1->regular_price, 2) : 'Bean Bag Chair' }}
                                </p>

                                {{-- ✨ កែប្រែត្រង់នេះ៖ ប្តូរ Link ឱ្យរត់ទៅកាន់ Product Details របស់ Featured Product 1 --}}
                                @if (isset($homepagesetting->featuredProduct1))
                                    <a href="{{ route('product.details', ['productId' => $homepagesetting->featuredProduct1->id]) }}"
                                        class="small text-white fw-bold text-decoration-none side-card-link d-inline-flex align-items-center gap-1">
                                        Discover <i class="fa-solid fa-arrow-right arrow-icon transition-all"
                                            style="font-size: 0.85rem;"></i>
                                    </a>
                                @else
                                    <a href="#"
                                        class="small text-white fw-bold text-decoration-none side-card-link d-inline-flex align-items-center gap-1">
                                        Discover <i class="fa-solid fa-arrow-right arrow-icon transition-all"
                                            style="font-size: 0.85rem;"></i>
                                    </a>
                                @endif
                            </div>
                            <div class="col-5 d-flex justify-content-center align-items-center p-2">
                                @if (isset($homepagesetting->featuredProduct1) && $homepagesetting->featuredProduct1->images->first())
                                    <img src="{{ asset('storage/' . $homepagesetting->featuredProduct1->images->first()->image_path) }}"
                                        alt="Featured Product 1" class="img-fluid side-product-img">
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 💻 Card ខាងក្រោម (Featured Product 2) --}}
                    <div
                        class="side-promo-card-new bottom-card position-relative overflow-hidden flex-grow-1 d-flex align-items-center">
                        <div class="row w-100 align-items-center g-0 position-relative z-index-2 m-0">
                            <div class="col-7 p-3 d-flex flex-column justify-content-center text-start">
                                <span class="text-cyan small fw-bold text-uppercase mb-1" style="letter-spacing: 1px;">New
                                    Arrival</span>
                                <h4 class="fw-bold text-white mb-1 side-card-title">
                                    {{ $homepagesetting->featuredProduct2->product_name ?? 'VR' }}
                                </h4>
                                <p class="small text-white-50 mb-3 fw-semibold">
                                    {{ isset($homepagesetting->featuredProduct2) ? '$' . number_format($homepagesetting->featuredProduct2->regular_price, 2) : 'Glasses' }}
                                </p>

                                {{-- ✨ កែប្រែត្រង់នេះ៖ ប្តូរ Link ឱ្យរត់ទៅកាន់ Product Details របស់ Featured Product 2 --}}
                                @if (isset($homepagesetting->featuredProduct2))
                                    <a href="{{ route('product.details', ['productId' => $homepagesetting->featuredProduct2->id]) }}"
                                        class="small text-white fw-bold text-decoration-none side-card-link d-inline-flex align-items-center gap-1">
                                        Shop Now <i class="fa-solid fa-arrow-right arrow-icon transition-all"
                                            style="font-size: 0.85rem;"></i>
                                    </a>
                                @else
                                    <a href="#"
                                        class="small text-white fw-bold text-decoration-none side-card-link d-inline-flex align-items-center gap-1">
                                        Shop Now <i class="fa-solid fa-arrow-right arrow-icon transition-all"
                                            style="font-size: 0.85rem;"></i>
                                    </a>
                                @endif
                            </div>
                            <div class="col-5 d-flex justify-content-center align-items-center p-2">
                                @if (isset($homepagesetting->featuredProduct2) && $homepagesetting->featuredProduct2->images->first())
                                    <img src="{{ asset('storage/' . $homepagesetting->featuredProduct2->images->first()->image_path) }}"
                                        alt="Featured Product 2" class="img-fluid side-product-img">
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            {{-- 🛒 ផ្នែក Livewire Product Filter ខាងក្រោម --}}
            <div class="pt-5 mt-2">
                @livewire('home-product-filter-component')
            </div>

        </div>
    </div>
@endsection

<style>
    /* -----------------------------------------
       ✨ MODERN HERO VIBRANT GRADIENT STYLES
    -------------------------------------------- */

    .hero-wrapper {
        font-family: 'Plus Jakarta Sans', sans-serif;
        padding: 10px 0 30px 0 !important;
    }

    .z-index-2 {
        z-index: 2;
    }

    .line-height-1 {
        line-height: 0.85;
    }

    .transition-all {
        transition: all 0.25s ease-in-out;
    }

    /* 🎆 1. MAIN HERO BANNER */
    .main-hero-banner {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 45%, #f97316 100%) !important;
        border-radius: 24px;
        padding: 40px;
        min-height: 380px;
        border: none;
        box-shadow: 0 20px 40px -15px rgba(124, 58, 237, 0.35);
    }

    .hero-promo-badge {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 6px 14px;
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
    }

    .hero-discount-text {
        font-family: 'Outfit', sans-serif;
        font-size: 5.5rem;
        font-weight: 800;
        letter-spacing: -3px;
        color: #ffffff !important;
        text-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .hero-title-text {
        font-size: 1.85rem;
        letter-spacing: -0.5px;
        line-height: 1.2;
        color: #ffffff !important;
    }

    .hero-desc-text {
        max-width: 420px;
        line-height: 1.6;
    }

    .btn-hero-action {
        background: #ffffff !important;
        border: none !important;
        padding: 12px 28px !important;
        font-size: 0.95rem;
        border-radius: 12px !important;
        transition: all 0.25s ease;
    }

    .btn-hero-action:hover {
        background: #f8fafc !important;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(255, 255, 255, 0.2) !important;
    }

    /* 🎴 2. SIDE PROMO CARDS */
    .side-promo-card-new {
        border-radius: 20px;
        padding: 24px;
        min-height: 178px;
        border: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .side-promo-card-new.top-card {
        background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%) !important;
        box-shadow: 0 15px 30px -10px rgba(99, 102, 241, 0.3);
    }

    .side-promo-card-new.bottom-card {
        background: linear-gradient(135deg, #0ea5e9 0%, #4f46e5 100%) !important;
        box-shadow: 0 15px 30px -10px rgba(14, 165, 233, 0.3);
    }

    .side-promo-card-new:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 35px -5px rgba(0, 0, 0, 0.2);
    }

    .side-card-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.35rem;
        letter-spacing: -0.5px;
        color: #ffffff !important;
    }

    .text-cyan {
        color: #22d3ee !important;
    }

    .side-card-link:hover .arrow-icon {
        transform: translateX(4px);
    }

    /* 📷 3. PRODUCT IMAGES DESIGN WITH SHADOW */
    .floating-product-img {
        max-height: 270px;
        object-fit: contain;
        filter: drop-shadow(0 20px 30px rgba(0, 0, 0, 0.25));
        transform: rotate(-10deg);
        animation: floatA 4s ease-in-out infinite;
    }

    .side-product-img {
        max-height: 110px;
        object-fit: contain;
        filter: drop-shadow(0 15px 20px rgba(0, 0, 0, 0.2));
        animation: floatB 4.5s ease-in-out infinite;
        transition: transform 0.4s ease;
    }

    .side-promo-card-new:hover .side-product-img {
        transform: scale(1.05) rotate(3deg);
    }

    /* 🔄 FLOATING ANIMATION EFFECTS */
    @keyframes floatA {

        0%,
        100% {
            transform: translateY(0) rotate(-10deg);
        }

        50% {
            transform: translateY(-10px) rotate(-8deg);
        }
    }

    @keyframes floatB {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-6px);
        }
    }

    /* 📱 4. RESPONSIVE MEDIA QUERIES */
    @media (max-width: 991.98px) {
        .hero-wrapper {
            padding: 5px 0 20px 0 !important;
        }

        .main-hero-banner {
            padding: 30px;
        }

        .hero-discount-text {
            font-size: 4rem;
        }

        .floating-product-img {
            max-height: 200px;
        }
    }

    @media (max-width: 767.98px) {

        .main-hero-banner .text-start,
        .main-hero-banner {
            text-align: center !important;
        }

        .hero-desc-text {
            margin-left: auto;
            margin-right: auto;
        }

        .floating-product-img {
            max-height: 180px;
            margin-top: 10px;
        }

        .side-product-img {
            max-height: 90px;
        }
    }
</style>
