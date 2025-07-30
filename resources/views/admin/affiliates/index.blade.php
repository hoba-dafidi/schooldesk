@extends('layouts/contentNavbarLayout')

@section('title', 'Affiliate Dashboard')

@section('vendor-style')
    @vite('resources/assets/vendor/libs/chart.js/chart.min.css')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/chart.js/chart.min.js')
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold mb-4">Affiliate Dashboard</h4>

        {{-- Stat Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-md-3">
                <x-admin.card icon="dollar-sign" label="Total Commission" value="₦{{ number_format($totalCommission) }}" />
            </div>
            <div class="col-sm-6 col-md-3">
                <x-admin.card icon="check-circle" label="Paid" value="₦{{ number_format($paidCommission) }}"
                    class="text-success" />
            </div>
            <div class="col-sm-6 col-md-3">
                <x-admin.card icon="clock" label="Pending" value="{{ $pendingCommission }}" class="text-warning" />
            </div>
            <div class="col-sm-6 col-md-3">
                <x-admin.card icon="users" label="Top Referrals"
                    value="{{ $referralsBySchool->sum() ?? $topAffiliates->sum('affiliate_commissions_count') }}" />
            </div>
        </div>

        {{-- Chart --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-2 mb-md-0">Commission Overview ({{ ucfirst($period ?? 'weekly') }})</h5>
                <form method="GET" class="ms-md-auto w-100 w-md-auto">
                    <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="weekly" {{ ($period ?? '') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ ($period ?? '') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </form>
            </div>
            <div class="card-body">
                <div style="overflow-x: auto;">
                    <canvas id="commissionChart" height="100" style="min-width: 500px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Top Affiliates --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Top Affiliates</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Referrals</th>
                            <th>Total Commission</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topAffiliates as $user)
                            <tr>
                                <td>
                                    <div class="d-block">
                                        <strong>{{ $user->name }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($user->email, 25) }}</small>
                                    </div>
                                </td>
                                <td>{{ $user->affiliate_commissions_count }}</td>
                                <td>₦{{ number_format($user->affiliate_commissions_sum_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">No affiliates found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Commissions --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-2 mb-md-0">Recent Commissions</h5>
                {{-- Optional Export Button --}}
                {{-- <a href="{{ route('admin.affiliates.export') }}" class="btn btn-sm btn-primary mt-2 mt-md-0">Export</a> --}}
            </div>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>User</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentCommissions as $commission)
                            <tr>
                                <td>₦{{ number_format($commission->amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $commission->status === 'paid' ? 'success' : 'warning' }}">
                                        {{ ucfirst($commission->status) }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $commission->affiliate?->user?->name ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $commission->created_at->format('d M, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No commissions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Chart Script --}}
    @push('script')
        <script>
            const ctx = document.getElementById('commissionChart').getContext('2d');
            const commissionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Commission',
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: 'rgba(0,123,255,0.2)',
                        borderColor: 'rgba(0,123,255,1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection
