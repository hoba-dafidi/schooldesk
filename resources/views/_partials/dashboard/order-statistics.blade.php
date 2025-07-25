<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0">Order Statistics</h5>
        <div class="dropdown">
            <button class="btn p-0" type="button" id="orederStatistics" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="bx bx-dots-vertical-rounded"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                <a class="dropdown-item" href="javascript:void(0);">Last 7 Days</a>
                <a class="dropdown-item" href="javascript:void(0);">Last 30 Days</a>
                <a class="dropdown-item" href="javascript:void(0);">This Month</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            <h3 class="mb-0">8,258</h3>
            <span class="text-success ms-2 fw-semibold"><i class="bx bx-up-arrow-alt"></i> +12%</span>
        </div>
        <div id="orderStatisticsChart"></div>
    </div>
</div>
