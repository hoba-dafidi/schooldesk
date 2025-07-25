@extends('layouts/contentNavbarLayout')

@section('title', 'Notifications')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bx bx-bell me-2"></i>Notifications</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendNotificationModal">
                <i class="bx bx-send me-1"></i> Send Notification
            </button>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form id="bulk-action-form" method="POST" action="{{ route('admin.notifications.bulk') }}">
                    @csrf
                    <div class="d-flex mb-3 gap-2">
                        <button class="btn btn-outline-success" type="submit" name="action" value="read">Mark as
                            Read</button>
                        <button class="btn btn-outline-danger" type="submit" name="action" value="delete">Delete</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped" id="notificationTable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>Type</th>
                                    <th>Message</th>
                                    <th>Recipient</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($notifications as $notification)
                                    <tr>
                                        <td><input type="checkbox" name="ids[]" value="{{ $notification->id }}"></td>
                                        <td>
                                            <i
                                                class="bx {{ $notification->data['icon'] ?? 'bx-info-circle' }} text-{{ $notification->data['level'] ?? 'primary' }}"></i>
                                        </td>
                                        <td>
                                            @php
                                                $msg = $notification->data['message'] ?? 'No message';
                                            @endphp

                                            @if (is_array($msg))
                                                <ul class="list-unstyled mb-0">
                                                    @foreach ($msg as $key => $value)
                                                        <li><span class="fw-bold">{{ ucfirst($key) }}:</span>
                                                            {{ is_array($value) ? json_encode($value) : $value }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                {{ $msg }}
                                            @endif
                                        </td>

                                        <td>{{ $notification->notifiable instanceof \App\Models\User ? $notification->notifiable->name : '-' }}
                                        </td>
                                        <td>
                                            @if ($notification->read_at)
                                                <span class="badge bg-success">Read</span>
                                            @else
                                                <span class="badge bg-warning">Unread</span>
                                            @endif
                                        </td>
                                        <td>{{ $notification->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Send Notification Modal -->
    <div class="modal fade" id="sendNotificationModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-3">
                <div class="modal-header bg-primary text-white align-items-center">
                    <h5 class="modal-title">Send Notification</h5>
                    <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.notifications.send') }}">
                    @csrf
                    <div class="modal-body row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" name="message" required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Urgency Level</label>
                            <select class="form-select" name="level" required>
                                <option value="info">Info</option>
                                <option value="warning">Warning</option>
                                <option value="danger">Danger</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Recipient Type</label>
                            <select class="form-select" name="recipient_type" id="recipientTypeSelect" required>
                                <option value="role">Role</option>
                                <option value="user">User</option>
                            </select>
                        </div>

                        <div class="col-md-12" id="roleSelect">
                            <label class="form-label">Select Role</label>
                            <select class="form-select" name="role">
                                <option value="admin">Admin</option>
                                <option value="support">Support</option>
                                <option value="user">User</option>
                                <option value="developer">Developer</option>
                                <option value="sales">Sales</option>
                            </select>
                        </div>

                        <div class="col-md-12 d-none" id="userSelect">
                            <label class="form-label">Select User</label>
                            <select class="form-select" name="user_id">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-0">
                        <button type="button" class="btn btn-outline-secondary m-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary m-2">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('recipientTypeSelect').addEventListener('change', function() {
                if (this.value === 'role') {
                    document.getElementById('roleSelect').classList.remove('d-none');
                    document.getElementById('userSelect').classList.add('d-none');
                } else {
                    document.getElementById('roleSelect').classList.add('d-none');
                    document.getElementById('userSelect').classList.remove('d-none');
                }
            });

            document.getElementById('selectAll').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('input[name="ids[]"]');
                checkboxes.forEach(cb => cb.checked = this.checked);
            });
        </script>
    @endpush
@endsection
