@extends('layouts/contentNavbarLayout')

@section('title', 'Subscription Audit Logs')

@section('content')
    <div class="container-xxl">
        <h4 class="fw-bold mb-4">Subscription Change Logs</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Date</th>
                        <th>School</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Old Plan</th>
                        <th>New Plan</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($audits as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $log->school->name }}</td>
                            <td>{{ $log->user->name ?? '-' }}</td>
                            <td><span
                                    class="badge bg-{{ $log->action === 'upgrade' ? 'success' : ($log->action === 'downgrade' ? 'warning' : 'secondary') }}">{{ ucfirst($log->action) }}</span>
                            </td>
                            <td>{{ $log->oldPlan->name ?? '-' }}</td>
                            <td>{{ $log->newPlan->name }}</td>
                            <td>{{ $log->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $audits->links() }}
        </div>
    </div>
@endsection
