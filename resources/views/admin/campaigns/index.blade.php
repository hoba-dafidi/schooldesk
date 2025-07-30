@extends('layouts/contentNavbarLayout')

@section('title', 'Admin: Affiliate Campaigns')

@section('content')
    <div class="container-xxl">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">Affiliate Campaigns</h4>
            <div>
                <a href="{{ route('admin.campaigns.export', 'excel') }}" class="btn btn-outline-success btn-sm">Export
                    Excel</a>
                <a href="{{ route('admin.campaigns.export', 'pdf') }}" class="btn btn-outline-danger btn-sm">Export PDF</a>
            </div>
        </div>

        <div class="container-xxl">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold">Affiliate Campaigns</h4>
                <div>
                    <a href="{{ route('admin.campaigns.export', 'excel') }}" class="btn btn-outline-success btn-sm">Export
                        Excel</a>
                    <a href="{{ route('admin.campaigns.export', 'pdf') }}" class="btn btn-outline-danger btn-sm">Export
                        PDF</a>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 text-center">
                        <div class="card-body">
                            <h6 class="text-muted small">Total Commission</h6>
                            <h4 class="fw-bold">₦{{ number_format($totalCommission, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 text-center">
                        <div class="card-body">
                            <h6 class="text-muted small">Paid Commission</h6>
                            <h4 class="fw-bold text-success">₦{{ number_format($paidCommission, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 text-center">
                        <div class="card-body">
                            <h6 class="text-muted small">Pending Commission</h6>
                            <h4 class="fw-bold text-warning">₦{{ number_format($pendingCommission, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 text-center">
                        <div class="card-body">
                            <h6 class="text-muted small">Active Campaigns</h6>
                            <h4 class="fw-bold text-primary">{{ $activeCampaigns }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="card shadow-sm mb-3">
                <div class="card-body row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Affiliate</label>
                        <select id="affiliateFilter" class="form-select">
                            <option value="">All</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->affiliate->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" id="startDate" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" id="endDate" class="form-control">
                    </div>
                    <div class="col-md-3 align-self-end">
                        <button class="btn btn-primary w-100" id="filterBtn">Filter</button>
                    </div>
                </div>
            </div>

            {{-- Campaigns Table --}}
            <div class="card shadow-sm">
                <div class="card-body table-responsive">
                    <table class="table table-striped table-hover align-middle" id="campaignsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Affiliate</th>
                                <th>Code</th>
                                <th>Share Link</th>
                                <th>Clicks</th>
                                <th>Signups</th>
                                <th>Commission Rate</th>
                                <th>Active</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>


        {{-- Summary Cards --}}
        <div class="row mb-4">
            {{-- ... existing summary cards ... --}}
        </div>

        {{-- Chart --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Commission Trends</h5>
                <form method="GET">
                    <select name="period" onchange="this.form.submit()" class="form-select form-select-sm w-auto">
                        <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </form>
            </div>
            <div class="card-body">
                <canvas id="commissionChart" height="90"></canvas>
            </div>
        </div>


        <div class="card shadow-sm mb-3">
            <div class="card-body row g-3">
                <div class="col-md-3">
                    <label class="form-label">Affiliate</label>
                    <select id="affiliateFilter" class="form-select">
                        <option value="">All</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->affiliate->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" id="startDate" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" id="endDate" class="form-control">
                </div>
                <div class="col-md-3 align-self-end">
                    <button class="btn btn-primary w-100" id="filterBtn">Filter</button>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-striped table-hover align-middle" id="campaignsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Affiliate</th>
                            <th>Code</th>
                            <th>Share Link</th>
                            <th>Clicks</th>
                            <th>Signups</th>
                            <th>Commission Rate</th>
                            <th>Active</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let table;

        function loadTable() {
            table = $('#campaignsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.campaigns.data') }}",
                    data: function(d) {
                        d.affiliate_id = $('#affiliateFilter').val();
                        d.start_date = $('#startDate').val();
                        d.end_date = $('#endDate').val();
                    }
                },
                columns: [{
                        data: 'name'
                    },
                    {
                        data: 'affiliate'
                    },
                    {
                        data: 'code'
                    },
                    {
                        data: 'link'
                    },
                    {
                        data: 'clicks'
                    },
                    {
                        data: 'unique_signups'
                    },
                    {
                        data: 'custom_commission_rate',
                        render: data => data ? data + '%' : '—'
                    },
                    {
                        data: 'active'
                    },
                    {
                        data: 'created_at',
                        render: data => new Date(data).toLocaleDateString()
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        }

        $(function() {
            loadTable();

            $('#filterBtn').on('click', function() {
                table.ajax.reload();
            });
        });

        const ctx = document.getElementById('commissionChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Commissions (₦)',
                    data: @json($chartData),
                    borderColor: '#696CFF',
                    backgroundColor: 'rgba(105, 108, 255, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '₦' + value.toLocaleString()
                        }
                    }
                }
            }
        });
    </script>
@endpush
