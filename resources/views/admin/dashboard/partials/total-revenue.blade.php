<div class="card h-100">
    <div class="card-body d-flex flex-column justify-content-between">
        <div class="d-flex align-items-start justify-content-between mb-3">
            <div class="avatar flex-shrink-0 bg-label-success rounded-circle p-3">
                <i class="bx bx-dollar-circle text-success fs-4"></i>
            </div>
            <div class="dropdown">
                <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">View Revenue</a></li>
                    <li><a class="dropdown-item" href="#">Download Summary</a></li>
                </ul>
            </div>
        </div>

        <h5 class="card-title mb-1">Total Revenue</h5>
        <p class="mb-3 text-muted">Combined revenue from all active schools & plans.</p>

        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h4 class="mb-0">{{ formatCurrency($totalRevenue) }}</h4>
                <small class="text-muted">This Month</small>
            </div>
            <div class="text-end">
                @if ($revenueGrowth >= 0)
                    <span class="badge bg-label-success"><i class="bx bx-up-arrow-alt"></i> {{ $revenueGrowth }}%</span>
                @else
                    <span class="badge bg-label-danger"><i class="bx bx-down-arrow-alt"></i>
                        {{ abs($revenueGrowth) }}%</span>
                @endif
            </div>
        </div>

        <div class="progress mt-2" style="height: 6px;">
            <div class="progress-bar bg-success" style="width: {{ $revenueGrowth }}%" role="progressbar"></div>
        </div>
        <small class="text-muted mt-1 d-block">Compared to last month</small>
    </div>
</div>
