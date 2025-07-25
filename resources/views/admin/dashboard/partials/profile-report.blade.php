<div class="card h-100">
    <div class="card-body d-flex flex-column justify-content-between">
        <div class="d-flex align-items-start justify-content-between mb-3">
            <div class="avatar flex-shrink-0 bg-label-info rounded-circle p-3">
                <i class="bx bx-user-check text-info fs-4"></i>
            </div>
            <div class="dropdown">
                <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">View All Profiles</a></li>
                    <li><a class="dropdown-item" href="#">Download Report</a></li>
                </ul>
            </div>
        </div>

        <h5 class="card-title mb-1">User Overview</h5>
        <p class="mb-3 text-muted">Summary of new user registrations, logins, and activity this week.</p>

        <div class="d-flex align-items-center mb-2">
            <div class="me-2">
                <h4 class="mb-0">{{ number_format($newUsersThisWeek) }}</h4>
                <small class="text-muted">New Users</small>
            </div>
            <div class="mx-3 border-start"></div>
            <div class="me-2">
                <h4 class="mb-0">{{ number_format($activeUsers) }}</h4>
                <small class="text-muted">Active Users</small>
            </div>
        </div>

        <div class="progress mt-2" style="height: 6px;">
            <div class="progress-bar bg-info" style="width: {{ $activeUsersPercent }}%" role="progressbar"></div>
        </div>
        <small class="text-muted mt-1 d-block">{{ $activeUsersPercent }}% Active Engagement</small>
    </div>
</div>
