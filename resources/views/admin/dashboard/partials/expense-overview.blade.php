<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0">Expense Overview</h5>
        <div class="dropdown">
            <button class="btn p-0" type="button" id="expenseOverview" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <i class="bx bx-dots-vertical-rounded"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="expenseOverview">
                <a class="dropdown-item" href="javascript:void(0);">Last 7 Days</a>
                <a class="dropdown-item" href="javascript:void(0);">Last 30 Days</a>
                <a class="dropdown-item" href="javascript:void(0);">This Month</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div id="expenseOverviewChart"></div>
    </div>
</div>
