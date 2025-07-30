@extends('layouts/contentNavbarLayout')

@section('title', 'Affiliate Payout Requests')

@section('content')
    <div class="container-xxl">
        <h4 class="fw-bold mb-4">Affiliate Payout Requests</h4>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Bank</th>
                            <th>Account</th>
                            <th>Status</th>
                            <th>Requested</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $payout)
                            <tr>
                                <td>{{ $payout->user->name }}</td>
                                <td>₦{{ number_format($payout->amount, 2) }}</td>
                                <td>{{ $payout->bank_name }}</td>
                                <td>{{ $payout->account_number }} ({{ $payout->account_name }})</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $payout->status === 'paid' ? 'success' : ($payout->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($payout->status) }}
                                    </span>
                                </td>
                                <td>{{ $payout->created_at->format('M d, Y') }}</td>
                                <td>
                                    <form action="{{ route('admin.payouts.update', $payout) }}" method="POST"
                                        class="d-flex gap-1">
                                        @csrf @method('PATCH')
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="approved"
                                                {{ $payout->status === 'approved' ? 'selected' : '' }}>Approve</option>
                                            <option value="rejected"
                                                {{ $payout->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                                            <option value="paid" {{ $payout->status === 'paid' ? 'selected' : '' }}>Mark
                                                Paid</option>
                                        </select>
                                        <button class="btn btn-primary btn-sm">Update</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
@endsection
