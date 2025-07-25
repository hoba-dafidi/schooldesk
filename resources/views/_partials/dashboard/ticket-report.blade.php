<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h5 class="card-title mb-1">Active Tickets</h5>
                <small class="text-muted">Open or pending support issues</small>
            </div>
            <a href="{{ route('user.tickets.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>

        <div class="d-flex flex-wrap align-items-center">
            <div class="avatar me-3">
                <span class="avatar-initial rounded bg-label-warning">
                    <i class="bx bx-support bx-sm"></i>
                </span>
            </div>
            <div class="me-3">
                <h6 class="mb-0">{{ $activeTickets }} Ticket{{ $activeTickets !== 1 ? 's' : '' }} Open</h6>
                @if ($previousWeekTickets > 0)
                    @php
                        $percentageChange = round(
                            (($activeTickets - $previousWeekTickets) / $previousWeekTickets) * 100,
                            1,
                        );
                        $trendColor = $percentageChange >= 0 ? 'text-success' : 'text-danger';
                        $trendIcon = $percentageChange >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt';
                    @endphp
                    <small class="{{ $trendColor }}">
                        <i class="bx {{ $trendIcon }}"></i>
                        {{ $percentageChange }}% from last week
                    </small>
                @else
                    <small class="text-muted">No data for last week</small>
                @endif
            </div>
            <div class="ms-auto">
                <span class="badge bg-label-warning">Active</span>
            </div>
        </div>
    </div>
</div>
