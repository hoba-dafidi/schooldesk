<div class="card" style="height: 360px; overflow: hidden;">
    <div class="row row-bordered g-0 h-100">
        <div class="col-md-8 d-flex flex-column">
            <h5 class="card-header m-0 me-2 pb-3">Recent Notifications</h5>

            <ul class="list-group list-group-flush px-3 flex-grow-1 overflow-auto">
                @forelse ($notifications as $notification)
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">
                                {{ ucfirst($notification->type) }}
                                @if (!$notification->read_at)
                                    <span class="badge bg-info ms-2">New</span>
                                @endif
                            </div>
                            {{ $notification->data['message'] ?? 'No details available.' }}
                            @if (isset($notification->data['school_name']))
                                <br><small class="text-muted">School: {{ $notification->data['school_name'] }}</small>
                            @endif
                        </div>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No notifications available</li>
                @endforelse
            </ul>
        </div>

        <div class="col-md-4 border-start">
            <div class="card-body h-100 d-flex flex-column justify-content-between">
                <div class="text-center">
                    <div class="dropdown mb-3">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="notifFilter"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Filter
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="notifFilter">
                            <a class="dropdown-item" href="?filter=all">All</a>
                            <a class="dropdown-item" href="?filter=unread">Unread</a>
                            <a class="dropdown-item" href="?filter=system">System</a>
                            <a class="dropdown-item" href="?filter=support">Support</a>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('user.notifications.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
            </div>
        </div>
    </div>
</div>
