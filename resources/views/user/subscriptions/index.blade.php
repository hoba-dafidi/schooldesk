@extends('layouts/contentNavbarLayout')

@section('title', 'My Subscriptions')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold mb-4">My Subscriptions</h4>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped" id="userSubscriptionsTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Invoice</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subscriptions as $index => $subscription)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $subscription->plan->name ?? '-' }}</td>
                                <td>
                                    ₦{{ number_format($subscription->plan->price, 2) }}
                                    @if (config('settings.tax_percentage'))
                                        <span class="text-muted d-block small">Tax:
                                            {{ config('settings.tax_percentage') }}%</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $status = strtolower($subscription->status);
                                    @endphp
                                    <span
                                        class="badge bg-{{ match ($status) {
                                            'active' => 'success',
                                            'canceled' => 'danger',
                                            'pending' => 'warning',
                                            default => 'secondary',
                                        } }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td>{{ $subscription->starts_at?->format('M d, Y') ?? '-' }}</td>
                                <td>{{ $subscription->ends_at?->format('M d, Y') ?? '-' }}</td>
                                <td>
                                    @php $invoice = $subscription->invoice; @endphp
                                    @if ($invoice)
                                        <a href="{{ route('user.invoices.show', $invoice->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-receipt"></i> View
                                        </a>
                                        <a href="{{ route('user.invoices.download', $invoice->id) }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="bx bx-download"></i> Download
                                        </a>
                                    @else
                                        <span class="text-muted small">No Invoice</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No subscriptions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
