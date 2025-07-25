{{-- <div class="col-xxl-8 mb-6 order-0">

    <div class="card">
        <div class="d-flex align-items-start row">
            <div class="col-sm-7">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">Welcome back, {{ auth()->user()->name }}!
                    </h5>
                    <p class="mb-6">You are logged in as <strong>{{ ucfirst(auth()->user()->role) }}. Manage the
                            platform, view insights, and stay on top of system-wide activity.
                    </p>

                    <a href="javascript:;" class="btn btn-sm btn-outline-primary">View Badges</a>
                </div>
            </div>
            <div class="col-sm-5 text-center text-sm-left">
                <div class="card-body pb-0 px-0 px-md-6">
                    <img src="{{ asset('assets/img/illustrations/man-with-laptop.png') }}" height="175"
                        class="scaleX-n1-rtl" alt="View Badge User">
                </div>
            </div>
        </div>
    </div>
</div> --}}

<div class="card shadow-sm mb-4">
    <div class="card-body d-flex align-items-center">
        <div class="avatar avatar-xl bg-primary text-white rounded-circle me-3">
            <i class="bx bx-bar-chart-alt-2 fs-3"></i>
        </div>
        <div>
            <h5 class="mb-1">Welcome back, {{ auth()->user()->name }}</h5>
            <p class="mb-0 text-muted">
                You are logged in as
                <strong>{{ ucfirst(Auth::user()->getRoleNames()->first() ?? 'No role') }}</strong>
            </p>
        </div>
    </div>
</div>
