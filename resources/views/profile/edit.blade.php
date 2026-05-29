@extends('user.layouts.layout')

@section('user_page_title', 'My Profile')

@section('user_layout')
    <div class="container-fluid px-2 px-md-4">
        <div class="mb-4">
            <h3 class="fw-bold text-dark mb-1" style="color: #0f172a;">
                <i data-lucide="user" class="me-2 text-primary" style="vertical-align: middle;"></i>Profile Settings
            </h3>
            <p class="text-muted small mb-0">Update your personal identification and manage account security.</p>
        </div>


        @if (session('status') === 'profile-updated')
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: 'Your Profile​ has been successfully updated.',
                    showConfirmButton: false,
                    timer: 2000, // បិទដោយស្វ័យប្រវត្តិក្នុង 2 វិនាទី
                    position: 'top-end', // ឱ្យវាលោតនៅជ្រុងខាងលើ
                    toast: true, // ធ្វើឱ្យវាចេញជាទម្រង់ Toast ស្អាត
                    timerProgressBar: true
                });
            </script>
        @endif

        <!-- បង្ហាញ Error ក្នុងករណីរូបភាពធំពេក ឬ Validation ខុស -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('patch')

            <div class="row g-4">
                <div class="col-12 col-lg-4 col-xl-3">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="p-4 text-center">
                            <div class="position-relative d-inline-block mb-3">
                                @if (Auth::user()->image)
                                    <img id="profileImagePreview" src="{{ asset('storage/' . Auth::user()->image) }}"
                                        alt="{{ Auth::user()->name }}"
                                        class="img-fluid rounded-circle p-1 border border-2 border-primary-subtle shadow-sm"
                                        style="width: 110px; height: 110px; object-fit: cover;" />
                                @else
                                    <img id="profileImagePreview"
                                        src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&size=128&background=4f46e5&color=fff&bold=true"
                                        alt="{{ Auth::user()->name }}"
                                        class="img-fluid rounded-circle p-1 border border-2 border-primary-subtle shadow-sm"
                                        style="width: 110px; height: 110px; object-fit: cover;" />
                                @endif
                                <span
                                    class="position-absolute bottom-0 end-0 rounded-circle bg-success border border-2 border-white"
                                    style="width: 14px; height: 14px; margin-right: 8px; margin-bottom: 8px;"></span>
                            </div>

                            <div class="mb-3">
                                <label for="imageUpload" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm"
                                    style="cursor: pointer; transition: all 0.2s ease-in-out;">
                                    <i data-lucide="camera" style="width: 12px; height: 12px;" class="me-1"></i> Change
                                    Photo
                                </label>
                                <input type="file" id="imageUpload" name="image" class="d-none" accept="image/*"
                                    onchange="previewImage(event)">
                            </div>

                            <h5 class="fw-bold text-dark mb-1">{{ Auth::user()->name }}</h5>
                            <span class="badge rounded-pill bg-primary-subtle text-primary text-uppercase"
                                style="font-size: 10px;">
                                {{ Auth::user()->role ?? 'User' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-8 col-xl-9">
                    <div class="card shadow-sm border-0 p-4 rounded-4">
                        <h5 class="fw-bold text-dark mb-3">Account Information</h5>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-secondary small" for="inputName">Full Name</label>
                                <input type="text" class="form-control rounded-3" id="inputName" name="name"
                                    value="{{ Auth::user()->name }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-secondary small" for="inputEmail">Email
                                    Address</label>
                                <input type="text" class="form-control rounded-3" id="inputEmail" name="email"
                                    value="{{ Auth::user()->email }}" required>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-semibold text-secondary small d-block mb-2">Gender</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="genderMale"
                                        value="male" {{ Auth::user()->gender == 'male' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="genderMale">Male</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="genderFemale"
                                        value="female" {{ Auth::user()->gender == 'female' ? 'checked' : '' }}>
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

        <div class="card shadow-sm border-0 p-4 rounded-4 mt-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Security & Privacy</h5>
                    <p class="text-muted small mb-0">Change your password regularly for better security.</p>
                </div>
                <a href="#" class="btn btn-outline-danger btn-sm rounded-3 py-2 px-3 border-dashed">
                    <i data-lucide="lock" style="width: 16px; height: 16px;" class="me-1"></i> Change Password
                </a>
            </div>
        </div>
    </div>

    <script>
        // បើកដំណើរការ Lucide Icons
        lucide.createIcons();

        // មុខងារសម្រាប់បង្ហាញរូបភាពភ្លាមៗពេលជ្រើសរើសរួច (Preview Image)
        function previewImage(event) {
            const imageInput = event.target;
            const imagePreview = document.getElementById('profileImagePreview');

            if (imageInput.files && imageInput.files[0]) {
                // បង្កើត URL បណ្តោះអាសន្នសម្រាប់ File រូបភាពដែលបានរើស
                imagePreview.src = URL.createObjectURL(imageInput.files[0]);

                // រក្សាទម្រង់រូបភាពឱ្យនៅជាកណ្ដាលរង្វង់ស្អាត ទោះបីជាទំហំដើមប៉ុណ្ណាក៏ដោយ
                imagePreview.style.objectFit = "cover";
            }
        }
    </script>
@endsection
