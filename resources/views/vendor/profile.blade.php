@extends('vendor.layouts.layout')

@section('vendor_page_title', 'Vendor Profile')

@section('vendor_layout')
    <div class="container-fluid px-2 px-md-4">
        <div class="mb-4">
            <h3 class="fw-bold text-dark mb-1" style="color: #0f172a;">
                <i data-lucide="user" class="me-2 text-primary" style="vertical-align: middle;"></i>Vendor Profile Settings
            </h3>
            <p class="text-muted small mb-0">Manage your vendor store information and account settings.</p>
        </div>

        {{-- បង្ហាញ Success Message --}}
        @if (session('status') === 'profile-updated' || session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: 'Your profile has been successfully updated.',
                    showConfirmButton: false,
                    timer: 2000,
                    position: 'top-end',
                    toast: true,
                    timerProgressBar: true
                });
            </script>
        @endif

        {{-- បង្ហាញ Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('vendor.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('patch')

            <div class="row g-4">
                <!-- ផ្នែករូបភាព Profile -->
                <div class="col-12 col-lg-4 col-xl-3">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="p-4 text-center">
                            <div class="position-relative d-inline-block mb-3">
                                <img id="profileImagePreview"
                                    src="{{ $vendor->image ? asset('storage/' . $vendor->image) : 'https://ui-avatars.com/api/?name=' . urlencode($vendor->name) . '&size=128&background=4f46e5&color=fff' }}"
                                    alt="{{ $vendor->name }}"
                                    class="img-fluid rounded-circle p-1 border border-2 border-primary-subtle shadow-sm"
                                    style="width: 110px; height: 110px; object-fit: cover;" />
                            </div>

                            <div class="mb-3">
                                <label for="imageUpload" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm"
                                    style="cursor: pointer;">
                                    <i data-lucide="camera" style="width: 12px; height: 12px;" class="me-1"></i> Change
                                    Photo
                                </label>
                                <input type="file" id="imageUpload" name="image" class="d-none" accept="image/*"
                                    onchange="previewImage(event)">
                            </div>

                            <h5 class="fw-bold text-dark mb-1">{{ $vendor->name }}</h5>
                            <span class="badge rounded-pill bg-primary-subtle text-primary text-uppercase"
                                style="font-size: 10px;">Vendor</span>
                        </div>
                    </div>
                </div>

                <!-- ផ្នែកព័ត៌មានលម្អិត -->
                <div class="col-12 col-lg-8 col-xl-9">
                    <div class="card shadow-sm border-0 p-4 rounded-4">
                        <h5 class="fw-bold text-dark mb-3">Vendor Information</h5>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-secondary small">Store/Vendor Name</label>
                                <input type="text" class="form-control rounded-3" name="name"
                                    value="{{ $vendor->name }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-secondary small">Email Address</label>
                                <input type="email" class="form-control rounded-3" name="email"
                                    value="{{ $vendor->email }}" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label fw-semibold text-secondary small d-block mb-2">Gender</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="genderMale"
                                        value="male" {{ old('gender', $vendor->gender) == 'male' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="genderMale">Male</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="genderFemale"
                                        value="female" {{ old('gender', $vendor->gender) == 'female' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="genderFemale">Female</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold w-100 w-md-auto">
                                <i data-lucide="save" style="width: 16px; height: 16px;" class="me-2"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        lucide.createIcons();

        function previewImage(event) {
            const imagePreview = document.getElementById('profileImagePreview');
            if (event.target.files && event.target.files[0]) {
                imagePreview.src = URL.createObjectURL(event.target.files[0]);
            }
        }
    </script>
@endsection
