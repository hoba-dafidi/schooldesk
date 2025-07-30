@extends('layouts/contentNavbarLayout')

@section('title', 'Notifications')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">Notifications</h4>

            @if ($notifications->whereNull('read_at')->count())
                <form method="POST" action="{{ route('user.notifications.markAllAsRead') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        Mark All as Read
                    </button>
                </form>
            @endif
        </div>

        @foreach ($notifications as $notification)
            <div class="card mb-3 notification-item {{ is_null($notification->read_at) ? 'border-primary' : '' }}">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        {{-- <h6 class="mb-1">{{ ucfirst($notification->type) }}</h6> --}}
                        <p class="mb-1">{{ $notification->data['message'] ?? 'No message' }}</p>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>

                    @if (is_null($notification->read_at))
                        <button class="btn btn-sm btn-outline-success mark-as-read" data-id="{{ $notification->id }}">
                            Mark as Read
                        </button>
                    @else
                        <span class="badge bg-secondary">Read</span>
                    @endif
                </div>
            </div>
        @endforeach

        @if ($notifications->isEmpty())
            <div class="alert alert-info">You have no notifications.</div>
        @endif

        {{ $notifications->links() }}
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.mark-as-read').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const notificationElement = this.closest(
                        '.notification-item'); // Adjust selector as needed

                    fetch(`/user/notifications/${id}/read`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                        })
                        .then(response => {
                            if (response.ok) {
                                Toastify({
                                    text: "Notification marked as read",
                                    duration: 3000,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#28a745",
                                }).showToast();

                                // ✅ Fade out the notification visually
                                notificationElement.style.transition = 'opacity 0.5s ease';
                                notificationElement.style.opacity = '0';

                                setTimeout(() => {
                                    notificationElement.remove();
                                }, 500); // Remove after fade-out
                            } else {
                                return response.json().then(err => {
                                    throw err;
                                });
                            }
                        })
                        .catch(error => {
                            Toastify({
                                text: error.message || "An error occurred",
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#dc3545",
                            }).showToast();
                        });
                });
            });
        });
    </script>
@endpush
