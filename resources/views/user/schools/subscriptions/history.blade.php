@extends('layouts/contentNavbarLayout')

@section('title', 'Subscription History - ' . $school->name)

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Subscription History for {{ $school->name }}</h4>

        <a href="{{ route('user.schools.index') }}" class="btn btn-outline-secondary mb-3">
            <i class="bx bx-arrow-back"></i> Back to Schools
        </a>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped" id="subscriptionHistoryTable">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Started At</th>
                            <th>Ends At</th>
                            <th>Cancelled At</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subscriptions as $subscription)
                            <tr>
                                <td>{{ $subscription->user->name ?? 'N/A' }}</td>
                                <td>{{ $subscription->plan->name ?? 'N/A' }}</td>
                                <td>
                                    <span
                                        class="badge bg-label-{{ $subscription->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </td>
                                <td>{{ $subscription->starts_at?->format('Y-m-d') ?? '-' }}</td>
                                <td>{{ $subscription->ends_at?->format('Y-m-d') ?? '-' }}</td>
                                <td>{{ $subscription->cancelled_at?->format('Y-m-d') ?? '-' }}</td>
                                <td>{{ $subscription->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No subscriptions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $subscriptions->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#subscriptionHistoryTable').DataTable({
                "paging": false,
                "searching": false,
                "info": false,
                "ordering": true,
            });
        });
    </script>
@endpush
