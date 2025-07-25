@extends('layouts/contentNavbarLayout')

@section('title', 'My Support Tickets')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">My Tickets</h4>
            <a href="{{ route('user.tickets.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> New Ticket
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->subject }}</td>
                            <td><span
                                    class="badge bg-{{ getStatusColor($ticket->status) }}">{{ $ticket->status->label() }}</span>
                            </td>
                            <td><span
                                    class="badge bg-{{ getPriorityColor($ticket->priority) }}">{{ $ticket->priority->label() }}</span>
                            </td>
                            <td>{{ $ticket->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('user.tickets.show', $ticket) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No tickets found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $tickets->links() }}
        </div>
    </div>
@endsection
