@extends('layouts/contentNavbarLayout')

@section('title', 'Affiliate Dashboard')


@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold mb-4">Affiliate Dashboard</h4>

        {{-- Referral Code / Link --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-2">Your Referral Code</h5>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-label-primary fs-6">{{ $referralCode }}</span>
                    <div class="ms-auto"></div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Referral Link</label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ $referralLink }}" id="referralLink" readonly>
                        <button class="btn btn-outline-primary" type="button" id="copyLinkBtn">Copy</button>
                    </div>
                </div>
            </div>
        </div>

        @include('components.warnings.missing-bank-details')




        {{-- Stat cards --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Total Commission</div>
                        <div class="fs-4 fw-semibold">₦{{ number_format($totalCommission, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Paid</div>
                        <div class="fs-4 fw-semibold text-success">₦{{ number_format($paidCommission, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Pending</div>
                        <div class="fs-4 fw-semibold text-warning">₦{{ number_format($pendingCommission, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Direct Signups</div>
                        <div class="fs-4 fw-semibold">{{ number_format($signupCount) }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if ($pendingCommission >= 30000)
            <div class="text-end mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#payoutModal">
                    <i class="fas fa-wallet me-1"></i> Request Payout
                </button>
            </div>
        @endif


        <div class="card shadow-sm mb-10">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0">Payout Requests</h5>
            </div>
            <div class="card-body table-responsive m-5">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Bank</th>
                            <th>Account</th>
                            <th>Requested At</th>
                            <th>Paid At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payoutRequests as $payout)
                            <tr>
                                <td>₦{{ number_format($payout->amount, 2) }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $payout->status === 'paid'
                                            ? 'success'
                                            : ($payout->status === 'rejected'
                                                ? 'danger'
                                                : ($payout->status === 'approved'
                                                    ? 'info'
                                                    : 'warning text-dark')) }}">
                                        {{ ucfirst($payout->status) }}
                                    </span>
                                </td>
                                <td>{{ $payout->bank_name }}</td>
                                <td>{{ $payout->account_number }} ({{ $payout->account_name }})</td>
                                <td>{{ $payout->created_at->format('M d, Y') }}</td>
                                <td>{{ $payout->paid_at ? $payout->paid_at->format('M d, Y') : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No payout requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $payoutRequests->links() }}
                </div>
            </div>
        </div>


        {{-- Chart + Filters --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Commission Trend</h5>
                <form method="GET" class="d-flex align-items-center gap-2">
                    @if ($status)
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    <select name="range" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        <option value="weekly" {{ $range === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $range === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </form>
            </div>
            <div class="card-body">
                <canvas id="commissionChart" height="120"></canvas>
            </div>
        </div>

        <div class="row">
            {{-- Referred Users --}}
            <div class="col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0">Direct Referred Users</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle w-100" id="referralsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Signup Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($referredUsers as $u)
                                        <tr>
                                            <td>{{ $u->firstname }} {{ $u->lastname }}</td>
                                            <td>{{ $u->email }}</td>
                                            <td>{{ $u->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        {{-- Keep body empty for DataTables; show a friendly alert below --}}
                                    @endforelse
                                </tbody>
                            </table>
                            @if ($referredUsers->isEmpty())
                                <div class="text-center text-muted small py-3">No referrals yet.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Commission History --}}
            <div class="col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Commission History</h5>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('affiliate.dashboard') }}"
                                class="btn btn-outline-secondary {{ !$status ? 'active' : '' }}">All</a>
                            <a href="{{ route('affiliate.dashboard', ['status' => 'pending', 'range' => $range]) }}"
                                class="btn btn-outline-warning {{ $status == 'pending' ? 'active' : '' }}">Pending</a>
                            <a href="{{ route('affiliate.dashboard', ['status' => 'paid', 'range' => $range]) }}"
                                class="btn btn-outline-success {{ $status == 'paid' ? 'active' : '' }}">Paid</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle w-100" id="commissionsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>User</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($commissions as $c)
                                        <tr>
                                            <td>₦{{ number_format($c->amount, 2) }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $c->status === 'paid' ? 'success' : ($c->status === 'pending' ? 'warning text-dark' : 'secondary') }}">
                                                    {{ ucfirst($c->status) }}
                                                </span>
                                            </td>
                                            <td>{{ optional($c->referredUser)->email ?? '—' }}</td>
                                            <td>{{ $c->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        {{-- Keep body empty; show friendly message below --}}
                                    @endforelse
                                </tbody>
                            </table>
                            @if ($commissions->isEmpty())
                                <div class="text-center text-muted small py-3">No commissions yet.</div>
                            @endif
                        </div>
                        <div class="mt-3">
                            {{ $commissions->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Campaign Performance --}}
            <div class="col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0">Campaign Performance</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>Campaign</th>
                                        <th>Code</th>
                                        <th>Clicks</th>
                                        <th>Signups</th>
                                        <th>Commission</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($campaigns as $camp)
                                        <tr>
                                            <td>{{ $camp->name }}</td>
                                            <td><code>{{ $camp->code }}</code></td>
                                            <td>{{ number_format($camp->clicks) }}</td>
                                            <td>{{ number_format($camp->unique_signups) }}</td>
                                            <td>₦{{ number_format($commissionByCampaign[$camp->id] ?? 0, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No campaigns yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="small text-muted mt-2">
                            Tip: Create multiple campaigns to A/B test your messaging.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="payoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('affiliate.payouts.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Request Payout</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Amount to withdraw</label>
                    <input type="number" name="amount" min="1000" class="form-control" required>
                    <small class="text-muted d-block mt-1">Minimum payout: ₦1,000</small>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('page-script')
    <script>
        // Copy referral link
        document.getElementById('copyLinkBtn')?.addEventListener('click', function() {
            const input = document.getElementById('referralLink');
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand('copy');
            this.textContent = 'Copied!';
            setTimeout(() => this.textContent = 'Copy', 1500);
        });

        // Chart.js
        (function() {
            const ctx = document.getElementById('commissionChart').getContext('2d');
            const labels = @json($chartLabels);
            const data = @json($chartData);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Commission (₦)',
                        data: data,
                        borderColor: '#696CFF',
                        backgroundColor: 'rgba(105,108,255,0.15)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3
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
                                callback: (val) => '₦' + Number(val).toLocaleString()
                            }
                        }
                    }
                }
            });
        })();

        // DataTables for non-AJAX tables (no column definitions needed)
        $(function() {
            if ($('#commissionsTable tbody tr').length) {
                $('#commissionsTable').DataTable({
                    paging: true,
                    searching: true,
                    lengthChange: true,
                    pageLength: 10,
                    order: [
                        [3, 'desc']
                    ], // Date column
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search commissions..."
                    }
                });
            }

            if ($('#referralsTable tbody tr').length) {
                $('#referralsTable').DataTable({
                    paging: true,
                    searching: true,
                    lengthChange: true,
                    pageLength: 10,
                    order: [
                        [2, 'desc']
                    ], // Signup date
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search referrals..."
                    }
                });
            }
        });
    </script>
@endsection
