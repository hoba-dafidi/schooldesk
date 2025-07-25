<div class="card h-100">
    <div class="card-body d-flex flex-column justify-content-between">
        <div class="d-flex align-items-start justify-content-between mb-3">
            <div class="avatar flex-shrink-0 bg-label-info rounded-circle p-3">
                <i class="bx bx-bar-chart-alt-2 text-info fs-4"></i>
            </div>
            <div class="dropdown">
                <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">View All Orders</a></li>
                    <li><a class="dropdown-item" href="#">Export CSV</a></li>
                </ul>
            </div>
        </div>

        <h5 class="card-title mb-1">Subscription Statistics</h5>
        <p class="mb-3 text-muted">Track active, new, and expired school subscriptions.</p>

        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="mb-0">{{ $activeSubscriptions }}</h4>
                <small class="text-muted">Active</small>
            </div>
            <div>
                <h4 class="mb-0 text-success">+{{ $newSubscriptions }}</h4>
                <small class="text-muted">New This Week</small>
            </div>
            <div>
                <h4 class="mb-0 text-danger">-{{ $expiredSubscriptions }}</h4>
                <small class="text-muted">Expired</small>
            </div>
        </div>

        <div class="mt-3">
            <div class="progress mb-2" style="height: 6px;">
                <div class="progress-bar bg-info" style="width: {{ $subscriptionGrowth }}%" role="progressbar"></div>
            </div>
            <small class="text-muted">+{{ $subscriptionGrowth }}% growth since last week</small>
        </div>
    </div>
</div>
