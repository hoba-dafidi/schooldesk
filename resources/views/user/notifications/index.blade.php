@extends('layouts/contentNavbarLayout')

@section('title', 'Notifications')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">Notifications</h4>

            @if ($notifications->whereNull('read_at')->count())
                <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        Mark All as Read
                    </button>
                </form>
            @endif
        </div>

        @foreach ($notifications as $notification)
            <div class="card mb-3 {{ is_null($notification->read_at) ? 'border-primary' : '' }}">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">{{ ucfirst($notification->type) }}</h6>
                        <p class="mb-1">{{ $notification->data['message'] ?? 'No message' }}</p>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>

                    @if (is_null($notification->read_at))
                        <form method="POST" action="{{ route('notifications.markAsRead', $notification->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-success">
                                Mark as Read
                            </button>
                        </form>
                    @else
                        <span class="badge bg-secondary">Read</span>
                    @endif
                </div>
            </div>
        @endforeach

        @if ($notifications->isEmpty())
            <div class="alert alert-info">You have no notifications.</div>
        @endif
    </div>
@endsection

@push('scripts')
    @if (session('success'))
        <script>
            Toastify({
                text: "{{ session('success') }}",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#00b894",
            }).showToast();
        </script>
    @endif
@endpush
