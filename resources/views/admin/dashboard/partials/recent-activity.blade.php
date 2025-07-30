<div class="card shadow-sm h-100">
    <div class="card-header bg-primary text-white d-flex align-items-center">
        <i class="bx bx-time-five fs-4 me-2"></i>
        <h5 class="mb-0">Recent Activity</h5>
    </div>

    <div class="card-body px-3 py-3 overflow-auto" style="max-height: 350px;">
        @forelse ($activities as $dateLabel => $group)
            <h6 class="text-muted text-uppercase small mt-3 mb-2">{{ $dateLabel }}</h6>

            <ul class="timeline position-relative ps-2 border-start border-2 border-primary mb-4">
                @foreach ($group as $activity)
                    @if (is_object($activity))
                        @php
                            $colors = [
                                'ticket' => 'primary',
                                'payment' => 'success',
                                'alert' => 'danger',
                                'user' => 'warning',
                                'system' => 'info',
                            ];
                            $type = $activity->type ?? 'system';
                            $color = $colors[$type] ?? 'secondary';
                            $icon = $activity->icon ?? 'bx-bell';
                        @endphp

                        <li class="mb-4 position-relative">
                            <div
                                class="position-absolute top-0 start-0 translate-middle bg-white border border-{{ $color }} rounded-circle p-2 shadow-sm">
                                <i class="bx {{ $icon }} text-{{ $color }} fs-5"></i>
                            </div>

                            <div class="ms-5">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-1 fw-semibold text-dark">
                                        {{ $activity->title ?? 'Activity' }}
                                    </h6>
                                    <small
                                        class="text-muted">{{ $activity->created_at?->diffForHumans() ?? '' }}</small>
                                </div>
                                <p class="mb-0 text-muted small">{{ $activity->description ?? '' }}</p>
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
        @empty
            <p class="text-muted">No recent activity.</p>
        @endforelse
    </div>
</div>
