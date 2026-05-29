@extends('layouts.user')

@section('home')
    <div class="pt-5 mt-4 bg-gray-50">
        <div class="container mt-3">

            <div class="row g-4 align-items-stretch">
                <div class="col-12 col-lg-8">
                    <div class="p-5 text-white h-100 d-flex align-items-center position-relative overflow-hidden main-hero-banner"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #f97316 100%); border-radius: 24px; min-height: 380px;">

                        <div class="row w-100 align-items-center g-4 position-relative z-index-2">

                            <div class="col-12 col-md-7 text-start">
                                <h1 class="fw-bold mb-0 line-height-1" style="font-size: 5.5rem; letter-spacing: -2px;">
                                    {{ number_format($homepagesetting->discount_percent, 0) }}%
                                </h1>
                                <h3 class="fw-bold my-2" style="font-size: 1.75rem;">
                                    {{ $homepagesetting->discount_heading }}
                                </h3>
                                <p class="opacity-90 small mb-0" style="max-width: 400px;">
                                    {{ $homepagesetting->discount_subheading }}
                                </p>
                            </div>

                            <div class="col-12 col-md-5 d-flex justify-content-center align-items-center">
                                <img src="{{ asset('storage/' . $homepagesetting->discountedProduct->images->first()->image_path) }}"
                                    alt="" class="img-fluid floating-product-img"
                                    style="max-height: 260px; object-fit: contain;">
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4 d-flex flex-column gap-4">

                    <div class="p-4 text-white flex-grow-1 d-flex align-items-center position-relative overflow-hidden side-promo-card-new"
                        style="background: linear-gradient(135deg, #e879f9 0%, #000000 100%); border-radius: 20px; min-height: 178px;">

                        <div class="row w-100 align-items-center g-0 position-relative z-index-2">
                            <div class="col-6 d-flex justify-content-start">
                                <img src="{{ asset('storage/' . $homepagesetting->featuredProduct1->images->first()->image_path) }}"
                                    alt="Chair" class="img-fluid side-product-img"
                                    style="max-height: 120px; object-fit: contain;">
                            </div>
                            <div class="col-6 text-end pl-2">
                                <h3 class="fw-bold mb-1 text-start ms-2" style="font-size: 1.35rem;">
                                    {{ $homepagesetting->featuredProduct1->product_name ?? 'Comfy' }}
                                </h3>
                                <p class="small opacity-75 mb-0 text-start ms-2">
                                    {{ isset($homepagesetting->featuredProduct1) ? '$' . $homepagesetting->featuredProduct1->regular_price : 'Bean Bag Chair' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 text-white flex-grow-1 d-flex align-items-center position-relative overflow-hidden side-promo-card-new"
                        style="background: linear-gradient(135deg, #06b6d4 0%, #d946ef 100%); border-radius: 20px; min-height: 178px;">

                        <div class="row w-100 align-items-center g-0 position-relative z-index-2">
                            <div class="col-6 text-start pr-2">
                                <h3 class="fw-bold mb-1" style="font-size: 1.35rem;">
                                    {{ $homepagesetting->featuredProduct2->product_name ?? 'VR' }}
                                </h3>
                                <p class="small opacity-75 mb-0">
                                    {{ isset($homepagesetting->featuredProduct2) ? '$' . $homepagesetting->featuredProduct2->regular_price : 'Glasses' }}
                                </p>
                            </div>
                            <div class="col-6 d-flex justify-content-center">
                                <img src="{{ asset('storage/' . $homepagesetting->featuredProduct2->images->first()->image_path) }}"
                                    alt="VR" class="img-fluid side-product-img"
                                    style="max-height: 120px; object-fit: contain;">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="pt-5 mt-4">
                @livewire('home-product-filter-component')
            </div>

        </div>
    </div>
    {{-- @livewire('home-product-filter-component') --}}
@endsection
<style>
    .transition-all {
        transition: all 0.25s ease-in-out;
    }

    /* Card Hover Styling */
    .hover-card {
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.25s ease-in-out;
    }

    .hover-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1) !important;
    }

    .hover-card:hover .product-img-fluid {
        transform: scale(1.05);
    }

    /* Wishlist Heart Button Animation */
    .wishlist-btn {
        transition: transform 0.2s ease, background-color 0.2s ease;
    }

    .wishlist-btn:hover {
        transform: scale(1.15) !important;
        background-color: #f8fafc !important;
    }

    /* Text Title Link Styling */
    .hover-title-link {
        transition: color 0.2s ease-in-out;
    }

    .hover-title-link:hover {
        color: #4f46e5 !important;
    }

    /* Cart Action Button Styling */
    .btn-cart-action {
        border-radius: 10px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }

    .btn-cart-action:hover {
        background-color: #4f46e5 !important;
        border-color: #4f46e5 !important;
        color: white !important;
    }

    .btn-main-shop:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
    }

    .btn-vendor-action {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.3);
        transition: all 0.2s ease;
    }

    .btn-vendor-action:hover {
        background: #ffffff !important;
        color: #4f46e5 !important;
        border-color: #ffffff !important;
        transform: translateY(-2px);
    }

    .hover-link:hover .arrow {
        transform: translateX(5px);
        display: inline-block;
    }

    .side-promo-card {
        transition: all 0.25s ease;
        cursor: pointer;
    }

    .side-promo-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.15);
    }

    .side-promo-card:hover .promo-icon {
        transform: scale(1.1) rotate(5deg);
        opacity: 0.15 !important;
    }

    .promo-icon {
        transition: all 0.3s ease;
    }





    /* Custom Utility Styles */
    .z-index-2 {
        z-index: 2;
    }

    .line-height-1 {
        line-height: 1;
    }

    /* Big Hero Banner Image Styling */
    .hero-image-wrap {
        right: 5%;
        top: 50%;
        transform: translateY(-50%);
        width: 45%;
        display: d-flex;
        justify-content: center;
        align-items: center;
    }

    .floating-product-img {
        max-height: 320px;
        object-fit: contain;
        filter: drop-shadow(0 20px 30px rgba(0, 0, 0, 0.3));
        transform: rotate(-15deg);
        /* បង្វិលស្បែកជើងឱ្យរាងបញ្ឈរទាក់ទាញ */
        animation: floatAnim 4s ease-in-out infinite;
    }

    /* Side Promo Cards Image Styling */
    .side-product-img {
        max-height: 140px;
        object-fit: contain;
        filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.2));
        animation: floatAnimReverse 4.5s ease-in-out infinite;
    }

    /* ចលនាអណ្តែតឡើងចុះថ្នមៗ (Floating Animation Effect) */
    @keyframes floatAnim {

        0%,
        100% {
            transform: translateY(0) rotate(-15deg);
        }

        50% {
            transform: translateY(-10px) rotate(-13deg);
        }
    }

    @keyframes floatAnimReverse {

        0%,
        100% {
            transform: translateY(0) scale(1);
        }

        50% {
            transform: translateY(-6px) scale(1.03);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 991px) {
        .hero-image-wrap {
            position: relative !important;
            right: auto;
            top: auto;
            transform: none !important;
            width: 70%;
            margin: 20px auto 0 auto;
        }

        .main-hero-banner {
            flex-direction: column;
        }

        .main-hero-banner div[style*="max-width: 55%"] {
            max-width: 100% !important;
            text-align: center;
        }

        .main-hero-banner .text-start {
            text-align: center !important;
        }

        .side-promo-card-new div[style*="position: absolute"] {
            position: relative !important;
            left: auto !important;
            right: auto !important;
            width: 40% !important;
        }
    }
</style>
