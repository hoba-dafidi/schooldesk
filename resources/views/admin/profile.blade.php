@extends('layouts/contentNavbarLayout')

@section('title', 'My Profile')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">



        <h4 class="fw-bold py-3 mb-4"><i class="bx bx-user me-2"></i> My Profile</h4>

        <div class="row">
            <!-- Profile Summary -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="rounded-circle mb-3" width="120"
                            height="120">
                        <h5 class="card-title mb-1">{{ Auth::user()->name }}</h5>
                        <p class="text-muted mb-2">{{ Auth::user()->email }}</p>
                        <span
                            class="badge bg-primary text-uppercase">{{ Auth::user()->roles->pluck('name')->first() ?? 'No Role' }}</span>
                        <p class="mt-3">{{ Auth::user()->bio ?? 'No bio added.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Editable Profile -->
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Profile</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="firstname"
                                        value="{{ old('firstname', Auth::user()->firstname) }}" class="form-control"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="lastname"
                                        value="{{ old('lastname', Auth::user()->lastname) }}" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone) }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Profile Picture</label>
                                    <input type="file" name="avatar" class="form-control">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Bio</label>
                                    <textarea name="bio" rows="3" class="form-control">{{ old('bio', Auth::user()->bio) }}</textarea>
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.profile.password') }}">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-danger">Update Password</button>
                                </div>
                            </div>
                        </form>

                        @if (session('password_status'))
                            <div class="alert alert-success mt-3">{{ session('password_status') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('toast_success'))
                Toastify({
                    text: "{{ session('toast_success') }}",
                    duration: 4000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#28a745",
                    close: true
                }).showToast();
            @endif

            @if (session('toast_error'))
                Toastify({
                    text: "{{ session('toast_error') }}",
                    duration: 4000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                    close: true
                }).showToast();
            @endif
        });
    </script>
@endpush
