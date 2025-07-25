<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Profile Report</h5>
        <div class="d-flex flex-wrap align-items-center">
            <div class="avatar me-3">
                {{-- <img src="assets/img/avatars/1.png" alt="Avatar" class="rounded-circle" /> --}}
                <img src="{{ Auth::user()->avatar ?? asset('assets/img/avatars/default.png') }}" alt="Avatar"
                    class="rounded-circle" />
            </div>
            <div class="me-3">
                <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                <small class="text-muted">{{ Auth::user()->email }}</small>
            </div>
            <div class="ms-auto">
                <span class="badge bg-label-primary">Active</span>
            </div>
        </div>
    </div>
</div>
