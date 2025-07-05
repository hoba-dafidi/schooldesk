@extends('layouts.contentNavbarLayout')

@section('title', 'My Profile')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card card-border-shadow-primary mb-4" style="border-left: 6px solid var(--bs-primary);">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 text-dark"><i class="bx bx-user me-2"></i>Profile Overview</h4>
                    <a href="{{ route('user.profile.edit') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                        data-bs-placement="left" title="Edit your profile">
                        <i class="bx bx-edit"></i> Edit
                    </a>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('assets/img/avatars/default.png') }}"
                            alt="Avatar" class="rounded-circle me-3"
                            style="width: 80px; height: 80px; object-fit: cover;">
                        <div>
                            <h4 class="mb-0">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
                                <span class="badge bg-info ms-2" data-bs-toggle="tooltip"
                                    title="User Role">{{ Auth::user()->getRoleNames()->first() ?? 'No Role' }}</span>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card card-border-shadow-success mb-4" style="border-left: 6px solid var(--bs-success);">
                <div class="card-header bg-white">
                    <h5 class="text-primary mb-0"><i class="bx bx-envelope me-2"></i>Contact Details</h5>
                </div>
                <div class="card-body row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <div class="form-control-plaintext">{{ Auth::user()->email }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <div class="form-control-plaintext">{{ Auth::user()->phone ?? 'Not set' }}</div>
                    </div>
                </div>
            </div>

            <div class="card card card-border-shadow-info mb-4" style="border-left: 6px solid var(--bs-info);">
                <div class="card-header bg-white">
                    <h5 class="text-primary mb-0"><i class="bx bx-map me-2"></i>Location</h5>
                </div>
                <div class="card-body row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address Line 1</label>
                        <div class="form-control-plaintext">{{ Auth::user()->address_line1 ?? 'Not set' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address Line 2</label>
                        <div class="form-control-plaintext">{{ Auth::user()->address_line2 ?? 'Not set' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">City</label>
                        <div class="form-control-plaintext">{{ Auth::user()->city ?? 'Not set' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">State</label>
                        <div class="form-control-plaintext">{{ Auth::user()->state ?? 'Not set' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Postal Code</label>
                        <div class="form-control-plaintext">{{ Auth::user()->postal_code ?? 'Not set' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Country</label>
                        <div class="form-control-plaintext">{{ Auth::user()->country ?? 'Not set' }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Timezone</label>
                        <div class="form-control-plaintext">{{ Auth::user()->timezone ?? 'Not set' }}</div>
                    </div>
                </div>
            </div>

            <div class="card card card-border-shadow-warning mb-4" style="border-left: 6px solid var(--bs-warning);">
                <div class="card-header bg-white">
                    <h5 class="text-primary mb-0"><i class="bx bx-link me-2"></i>Social Profiles</h5>
                </div>
                <div class="card-body row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Facebook</label>
                        <div class="form-control-plaintext">
                            @if (Auth::user()->facebook)
                                <a href="{{ Auth::user()->facebook }}" target="_blank"
                                    class="text-decoration-underline">{{ Auth::user()->facebook }}</a>
                                <span class="badge bg-primary ms-2" data-bs-toggle="tooltip"
                                    title="Facebook profile active"><i class="bx bxl-facebook"></i></span>
                            @else
                                Not set
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Twitter</label>
                        <div class="form-control-plaintext">
                            @if (Auth::user()->twitter)
                                <a href="{{ Auth::user()->twitter }}" target="_blank"
                                    class="text-decoration-underline">{{ Auth::user()->twitter }}</a>
                                <span class="badge bg-info ms-2" data-bs-toggle="tooltip" title="Twitter profile active"><i
                                        class="bx bxl-twitter"></i></span>
                            @else
                                Not set
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">LinkedIn</label>
                        <div class="form-control-plaintext">
                            @if (Auth::user()->linkedin)
                                <a href="{{ Auth::user()->linkedin }}" target="_blank"
                                    class="text-decoration-underline">{{ Auth::user()->linkedin }}</a>
                                <span class="badge bg-secondary ms-2" data-bs-toggle="tooltip"
                                    title="LinkedIn profile active"><i class="bx bxl-linkedin"></i></span>
                            @else
                                Not set
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card card-border-shadow-danger mb-4" style="border-left: 6px solid var(--bs-danger);">
                <div class="card-header bg-white">
                    <h5 class="text-primary mb-0"><i class="bx bx-time me-2"></i>Activity</h5>
                </div>
                <div class="card-body row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Last Login</label>
                        <div class="form-control-plaintext">
                            {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('d M, Y H:i') : 'N/A' }}
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white text-end">
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Logout of your account">
                        <i class="bx bx-power-off me-1"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
