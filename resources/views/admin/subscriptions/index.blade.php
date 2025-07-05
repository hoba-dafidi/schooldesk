@extends('layouts/contentNavbarLayout')

@section('title', 'Subscriptions')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Manage Subscriptions</h4>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filter Form --}}
        <form method="GET" class="row g-3 mb-3 align-items-center">
            <div class="col-md-3">
                <input type="text" name="user" class="form-control" placeholder="Search by email"
                    value="{{ request('user') }}">
            </div>
            <div class="col-md-3">
                <select name="plan" class="form-select">
                    <option value="">-- Filter by Plan --</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">-- Status --</option>
                    @foreach (['active', 'trialing', 'cancelled', 'expired'] as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Filter</button>

                <a href="{{ route('admin.subscriptions.export', array_merge(request()->all(), ['format' => 'csv'])) }}"
                    class="btn btn-outline-secondary flex-fill" target="_blank" title="Export CSV">
                    CSV
                </a>

                <a href="{{ route('admin.subscriptions.export', array_merge(request()->all(), ['format' => 'pdf'])) }}"
                    class="btn btn-outline-secondary flex-fill" target="_blank" title="Export PDF">
                    PDF
                </a>
            </div>
        </form>


        {{-- Table --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Subscriptions</h5>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Plan</th>
                            <th>Gateway</th>
                            <th>Status</th>
                            <th>Starts</th>
                            <th>Ends</th>
                            <th>Renews</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subscriptions as $subscription)
                            <tr>
                                <td>
                                    <strong>{{ $subscription->user->name }}</strong><br>
                                    <small>{{ $subscription->user->email }}</small>
                                </td>
                                <td>
                                    {{ $subscription->plan->name }}<br>
                                    <small>Qty: {{ $subscription->quantity }}</small>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-label-info">{{ $subscription->paymentGateway->name ?? 'Manual' }}</span>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-label-{{ match ($subscription->status) {
                                            'active' => 'success',
                                            'trialing' => 'warning',
                                            'cancelled', 'expired' => 'danger',
                                            default => 'secondary',
                                        } }}">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </td>
                                <td>{{ $subscription->starts_at?->format('d M, Y') ?? '-' }}</td>
                                <td>{{ $subscription->ends_at?->format('d M, Y') ?? '-' }}</td>
                                <td>{{ $subscription->renews_at?->format('d M, Y') ?? '-' }}</td>
                                <td class="text-nowrap">
                                    {{-- View Details --}}
                                    <button class="btn btn-sm btn-outline-info" aria-label="View subscription details"
                                        data-bs-toggle="modal" data-bs-target="#subscriptionModal{{ $subscription->id }}">
                                        <i class="bx bx-show"></i>
                                    </button>

                                    {{-- Cancel Subscription --}}
                                    @if ($subscription->status === 'active' || $subscription->status === 'trialing')
                                        <form action="{{ route('admin.subscriptions.cancel', $subscription) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Cancel this subscription?')">
                                                <i class="bx bx-x"></i>
                                            </button>
                                        </form>
                                    @elseif ($subscription->status === 'cancelled')
                                        {{-- Resume Subscription --}}
                                        <form action="{{ route('admin.subscriptions.resume', $subscription) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success"
                                                onclick="return confirm('Resume this subscription?')">
                                                <i class="bx bx-refresh"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Resend Invoice (example) --}}
                                    <form action="{{ route('admin.subscriptions.resendInvoice', $subscription) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary"
                                            onclick="return confirm('Resend invoice to user?')" title="Resend Invoice">
                                            <i class="bx bx-envelope"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Subscription Detail Modal --}}
                            <div class="modal fade" id="subscriptionModal{{ $subscription->id }}" tabindex="-1"
                                aria-labelledby="subscriptionModalLabel{{ $subscription->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content shadow-lg border-0 rounded-3">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="subscriptionModalLabel{{ $subscription->id }}">
                                                Subscription Details
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>User:</strong> {{ $subscription->user->name }}
                                                ({{ $subscription->user->email }})
                                            </p>
                                            <p><strong>Plan:</strong> {{ $subscription->plan->name }}</p>
                                            <p><strong>Status:</strong> {{ ucfirst($subscription->status) }}</p>
                                            <p><strong>Gateway:</strong>
                                                {{ $subscription->paymentGateway->name ?? 'Manual' }}</p>
                                            <p><strong>Qty:</strong> {{ $subscription->quantity }}</p>
                                            <p><strong>Starts At:</strong>
                                                {{ $subscription->starts_at?->toDayDateTimeString() }}</p>
                                            <p><strong>Ends At:</strong>
                                                {{ $subscription->ends_at?->toDayDateTimeString() }}</p>
                                            <p><strong>Renews At:</strong>
                                                {{ $subscription->renews_at?->toDayDateTimeString() }}</p>
                                            <p><strong>External Ref:</strong>
                                                {{ $subscription->external_reference ?? '-' }}</p>
                                            @if ($subscription->meta)
                                                <p><strong>Meta:</strong>
                                                    <pre>{{ json_encode($subscription->meta, JSON_PRETTY_PRINT) }}</pre>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No subscriptions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $subscriptions->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
