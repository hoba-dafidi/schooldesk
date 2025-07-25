<div class="card" style="height: 460px;">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bx bx-bell me-2"></i>Recent Notifications
        </h5>
        @if ($unreadCount = auth()->user()->unreadNotifications->count())
            <span class="badge bg-danger">{{ $unreadCount }} unread</span>
        @endif
    </div>

    {{-- Scrollable Notification List --}}
    <div class="overflow-auto" style="max-height: 360px;">
        <ul class="list-group list-group-flush">
            @forelse($notifications as $note)
                @php
                    $isUnread = is_null($note->read_at);
                    $icon = $note->data['icon'] ?? 'bx-info-circle';
                    $level = $note->data['level'] ?? 'primary';
                    $message = $note->data['message'] ?? 'No message';
                    $title = is_array($message) ? $message['title'] ?? 'No message' : $message;
                @endphp

                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-start">
                        <i class="bx {{ $icon }} text-{{ $level }} fs-4 me-2 mt-1"></i>
                        <div>
                            <div class="fw-semibold">
                                {!! e($title) !!}
                                @if ($isUnread)
                                    <span class="badge bg-warning text-dark ms-2">New</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $note->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </li>
            @empty
                <li class="list-group-item text-muted">No recent notifications.</li>
            @endforelse
        </ul>
    </div>
</div>
