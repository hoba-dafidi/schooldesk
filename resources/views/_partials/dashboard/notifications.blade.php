<div class="card shadow-sm border-0" style="height: 360px; overflow: hidden;">
    <div class="row row-bordered g-0 h-100">
        <!-- Notifications -->
        <div class="col-md-8 d-flex flex-column">
            <div class="card-header bg-primary text-white py-2 px-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class='bx bx-bell'></i> Recent Notifications
                </h6>
            </div>

            <ul class="list-group list-group-flush flex-grow-1 overflow-auto">
                @forelse ($notifications as $notification)
                    @php
                        $type = $notification->data['type'] ?? 'info';

                        $icon = match ($type) {
                            'support' => 'bx bx-headphone',
                            'system' => 'bx bx-cog',
                            'alert' => 'bx bx-error',
                            default => 'bx bx-bell',
                        };

                        $badgeClass = match ($type) {
                            'support' => 'bg-warning',
                            'system' => 'bg-secondary',
                            'alert' => 'bg-danger',
                            default => 'bg-info',
                        };
                    @endphp

                    <li class="list-group-item px-3 py-2 border-0 d-flex justify-content-between align-items-start"
                        style="transition: background 0.3s;">
                        <div class="me-auto">
                            <div class="d-flex align-items-center mb-1">
                                <i class="{{ $icon }} fs-5 me-2 text-primary"></i>
                                <span class="badge {{ $badgeClass }} text-white rounded-pill small">
                                    {{ ucfirst($type) }}
                                </span>
                                @unless ($notification->read_at)
                                    <span class="badge bg-success text-white ms-2">New</span>
                                @endunless
                            </div>
                            <div class="small text-dark">
                                {{ $notification->data['message'] ?? 'No details available.' }}
                                @if (isset($notification->data['school_name']))
                                    <br>
                                    <span class="text-muted">
                                        <i class="bx bx-building-house"></i>
                                        {{ $notification->data['school_name'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <small class="text-muted mt-1 ms-2">{{ $notification->created_at->diffForHumans() }}</small>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-4">
                        <i class='bx bx-bell-off fs-4'></i> No notifications available
                    </li>
                @endforelse
            </ul>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4 border-start bg-light">
            <div class="card-body h-100 d-flex flex-column justify-content-between">
                <div class="text-center">
                    <div class="dropdown mb-3">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="notifFilter"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-filter-alt"></i> Filter
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifFilter">
                            <li><a class="dropdown-item" href="?filter=all">All</a></li>
                            <li><a class="dropdown-item" href="?filter=unread">Unread</a></li>
                            <li><a class="dropdown-item" href="?filter=system">System</a></li>
                            <li><a class="dropdown-item" href="?filter=support">Support</a></li>
                        </ul>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('user.notifications.index') }}" class="btn btn-sm btn-primary">
                        <i class="bx bx-list-ul"></i> View All
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
