<div class="card shadow-sm h-100">
    <div class="card-body">
        <div class="d-flex align-items-start mb-3">
            <div class="avatar flex-shrink-0 me-3">
                <img src="{{ asset('assets/img/icons/unicons/support.png') }}" alt="Tickets" class="rounded">
            </div>
            <div class="d-flex flex-column">
                <h5 class="card-title mb-0">Ticket Overview</h5>
                <small class="text-muted">Support performance this week</small>
            </div>
        </div>

        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="bx bx-envelope me-1 text-primary"></i> Open Tickets</span>
                <span class="badge bg-label-primary">{{ $openTickets }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="bx bx-time-five me-1 text-warning"></i> In Progress</span>
                <span class="badge bg-label-warning">{{ $inProgressTickets }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="bx bx-check-circle me-1 text-success"></i> Resolved</span>
                <span class="badge bg-label-success">{{ $resolvedTickets }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="bx bx-error me-1 text-danger"></i> Overdue</span>
                <span class="badge bg-label-danger">{{ $overdueTickets }}</span>
            </li>
        </ul>
    </div>
</div>
