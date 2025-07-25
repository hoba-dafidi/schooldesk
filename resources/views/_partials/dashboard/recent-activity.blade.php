@php
    // Icon + Badge map by activity type (description)
    $activityIcons = [
        'login' => ['icon' => 'bx bx-log-in', 'color' => 'primary', 'label' => 'Login'],
        'logout' => ['icon' => 'bx bx-log-out', 'color' => 'secondary', 'label' => 'Logout'],
        'ticket_created' => ['icon' => 'bx bx-message-square-add', 'color' => 'info', 'label' => 'Ticket'],
        'subscription_updated' => ['icon' => 'bx bx-refresh', 'color' => 'warning', 'label' => 'Subscription'],
        'school_updated' => ['icon' => 'bx bx-edit-alt', 'color' => 'success', 'label' => 'Update'],
        // Add more types here as needed
    ];
@endphp

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Recent Activity</h5>
    </div>
    <div class="card-body">
        <ul class="p-0 m-0">
            @forelse ($recentActivities as $activity)
                @php
                    $type = $activity->description;
                    $meta = $activityIcons[$type] ?? [
                        'icon' => 'bx bx-bell',
                        'color' => 'dark',
                        'label' => ucfirst($type),
                    ];
                @endphp

                <li class="d-flex mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-{{ $meta['color'] }}">
                            <i class="{{ $meta['icon'] }} text-white"></i>
                        </span>
                    </div>
                    <div class="d-flex w-100 align-items-center justify-content-between">
                        <div class="me-2">
                            <h6 class="mb-0">{{ $meta['label'] }}</h6>
                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                        </div>
                        <span class="badge bg-label-{{ $meta['color'] }}">{{ $meta['label'] }}</span>
                    </div>
                </li>
            @empty
                <li class="d-flex">
                    <div class="me-2">No recent activity.</div>
                </li>
            @endforelse
        </ul>
    </div>
</div>
