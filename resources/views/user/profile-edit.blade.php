@extends('layouts.contentNavbarLayout')

@section('title', 'Edit Profile')

@section('content')
    <div class="container">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-dark"><i class="bx bx-edit me-2"></i>Edit Profile</h4>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">First Name</label>
                            <input type="text" name="firstname" class="form-control"
                                value="{{ old('firstname', $user->firstname) }}" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middlename" class="form-control"
                                value="{{ old('middlename', $user->middlename) }}">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="lastname" class="form-control"
                                value="{{ old('lastname', $user->lastname) }}" required>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $user->email) }}" disabled>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone', $user->phone) }}">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Address Line 1</label>
                            <input type="text" name="address_line1" class="form-control"
                                value="{{ old('address_line1', $user->address_line1) }}">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" name="address_line2" class="form-control"
                                value="{{ old('address_line2', $user->address_line2) }}">
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control"
                                value="{{ old('city', $user->city) }}">
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control"
                                value="{{ old('state', $user->state) }}">
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label">Postal Code</label>
                            <input type="text" name="postal_code" class="form-control"
                                value="{{ old('postal_code', $user->postal_code) }}">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control"
                                value="{{ old('country', $user->country) }}">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Timezone</label>
                            <select name="timezone" class="form-select">
                                @foreach (timezone_identifiers_list() as $tz)
                                    <option value="{{ $tz }}" {{ $tz == $user->timezone ? 'selected' : '' }}>
                                        {{ $tz }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label">Facebook</label>
                            <input type="text" name="facebook" class="form-control"
                                value="{{ old('facebook', $user->facebook) }}">
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label">Twitter</label>
                            <input type="text" name="twitter" class="form-control"
                                value="{{ old('twitter', $user->twitter) }}">
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label">LinkedIn</label>
                            <input type="text" name="linkedin" class="form-control"
                                value="{{ old('linkedin', $user->linkedin) }}">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Avatar</label><br>
                            @if ($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle mb-2"
                                    width="70" alt="Avatar">
                            @endif
                            <input type="file" name="avatar" class="form-control">
                        </div>

                        <div class="col-12">
                            <hr>
                            <h5 class="text-primary">Update Password</h5>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                        <a href="{{ route('user.profile') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
