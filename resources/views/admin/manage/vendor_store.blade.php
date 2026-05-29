@extends('admin.layouts.layout')

@section('admin_page_title')
Manage Store - Admin Panel
@endsection

@section('admin_layout')
<div class="container-fluid px-4 py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-slate-800 mb-1">Store Profile & Settings</h3>
            <p class="text-muted small mb-0">Update your storefront information, business address, and operational hours.</p>
        </div>
        <div>
            <button type="submit" form="storeSettingsForm" class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-bold">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom border-light p-0">
            <ul class="nav nav-tabs border-0 px-4" id="storeTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-3 fw-bold text-secondary" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-pane" type="button" role="tab" aria-selected="true">
                        <i class="fa-solid fa-store me-2"></i>General Info
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 fw-bold text-secondary" id="localization-tab" data-bs-toggle="tab" data-bs-target="#localization-pane" type="button" role="tab" aria-selected="false">
                        <i class="fa-solid fa-location-dot me-2"></i>Location & Contact
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 fw-bold text-secondary" id="hours-tab" data-bs-toggle="tab" data-bs-target="#hours-pane" type="button" role="tab" aria-selected="false">
                        <i class="fa-solid fa-clock me-2"></i>Business Hours
                    </button>
                </li>
            </ul>
        </div>

        <form id="storeSettingsForm" action="#" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card-body p-4">
                <div class="tab-content" id="storeTabContent">

                    <div class="tab-pane fade show active" id="general-pane" role="tabpanel" aria-labelledby="general-tab" tabindex="0">
                        <div class="row g-4">
                            <div class="col-12 col-md-4 text-center border-end border-light">
                                <label class="form-label small fw-bold text-secondary d-block mb-3">Store Logo</label>
                                <div class="mb-3">
                                    <div class="position-relative d-inline-block">
                                        <img src="https://via.placeholder.com/150" alt="Store Logo" class="rounded-circle border p-1" style="width: 130px; height: 130px; object-fit: cover;">
                                        <label for="logo_upload" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center cursor-pointer shadow" style="width: 32px; height: 32px; id='logo_upload_label';">
                                            <i class="fa-solid fa-camera" style="font-size: 0.85rem;"></i>
                                        </label>
                                    </div>
                                    <input type="file" id="logo_upload" name="store_logo" class="d-none" accept="image/*">
                                </div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Allowed JPG, PNG. Max size 2MB</small>
                            </div>

                            <div class="col-12 col-md-8">
                                <div class="mb-3">
                                    <label for="store_name" class="form-label small fw-bold text-secondary">Store Name</label>
                                    <input type="text" class="form-control rounded-3 py-2" id="store_name" name="store_name" value="My Premium Boutique" required>
                                </div>
                                <div class="mb-3">
                                    <label for="store_tagline" class="form-label small fw-bold text-secondary">Store Tagline / Slogan</label>
                                    <input type="text" class="form-control rounded-3 py-2" id="store_tagline" name="store_tagline" value="Your one-stop fashion destination.">
                                </div>
                                <div class="mb-3">
                                    <label for="store_description" class="form-label small fw-bold text-secondary">Store Description</label>
                                    <textarea class="form-control rounded-3" id="store_description" name="store_description" rows="3" placeholder="Brief metadata description about your shop...">This is the official online store platform containing highly curated premium products.</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="localization-pane" role="tabpanel" aria-labelledby="localization-tab" tabindex="0">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="store_email" class="form-label small fw-bold text-secondary">Business Email Address</label>
                                <input type="email" class="form-control rounded-3 py-2" id="store_email" name="store_email" value="contact@boutique.com" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="store_phone" class="form-label small fw-bold text-secondary">Business Phone Number</label>
                                <input type="text" class="form-control rounded-3 py-2" id="store_phone" name="store_phone" value="+855 12 345 678" required>
                            </div>
                            <div class="col-12">
                                <label for="store_address" class="form-label small fw-bold text-secondary">Physical Street Address</label>
                                <input type="text" class="form-control rounded-3 py-2" id="store_address" name="store_address" value="St. 123, Toul Tom Poung, Phnom Penh, Cambodia">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="store_country" class="form-label small fw-bold text-secondary">Country</label>
                                <input type="text" class="form-control rounded-3 py-2" id="store_country" name="store_country" value="Cambodia" readonly>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="store_currency" class="form-label small fw-bold text-secondary">Default Currency</label>
                                <select class="form-select rounded-3 py-2" id="store_currency" name="store_currency">
                                    <option value="USD" selected>USD ($)</option>
                                    <option value="KHR">KHR (៛)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="hours-pane" role="tabpanel" aria-labelledby="hours-tab" tabindex="0">
                        <p class="text-muted small mb-4">Define your weekly store operations and checkout scheduling availability.</p>

                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-4 col-sm-3 col-md-2">
                                <span class="fw-bold text-dark small">Monday - Friday</span>
                            </div>
                            <div class="col-4 col-sm-4 col-md-3">
                                <input type="time" class="form-control form-control-sm rounded-2" name="weekday_open" value="08:00">
                            </div>
                            <div class="col-auto text-muted small">to</div>
                            <div class="col-4 col-sm-4 col-md-3">
                                <input type="time" class="form-control form-control-sm rounded-2" name="weekday_close" value="21:00">
                            </div>
                        </div>

                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-4 col-sm-3 col-md-2">
                                <span class="fw-bold text-dark small">Saturday - Sunday</span>
                            </div>
                            <div class="col-4 col-sm-4 col-md-3">
                                <input type="time" class="form-control form-control-sm rounded-2" name="weekend_open" value="09:00">
                            </div>
                            <div class="col-auto text-muted small">to</div>
                            <div class="col-4 col-sm-4 col-md-3">
                                <input type="time" class="form-control form-control-sm rounded-2" name="weekend_close" value="18:00">
                            </div>
                        </div>

                        <div class="row g-3 align-items-center">
                            <div class="col-12 mt-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="acceptOrdersSwitch" name="accept_orders" checked>
                                    <label class="form-check-label small fw-bold text-secondary" for="acceptOrdersSwitch">Accept automated orders outside business hours</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .nav-tabs .nav-link {
        border: none;
        color: #64748b;
        background: transparent;
        position: relative;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd !important;
        background: transparent;
    }
    .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background-color: #0d6efd;
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>
@endsection
