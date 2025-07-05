@extends('layouts/contentNavbarLayout')

@section('title', 'Tickets')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">Support Tickets</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                <i class="fa fa-plus"></i> New Ticket
            </button>
        </div>

        <!-- Filters -->
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach (\App\Enums\TicketStatus::cases() as $status)
                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="priority" class="form-select">
                    <option value="">All Priorities</option>
                    @foreach (\App\Enums\TicketPriority::cases() as $priority)
                        <option value="{{ $priority->value }}"
                            {{ request('priority') === $priority->value ? 'selected' : '' }}>
                            {{ $priority->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="Search...">
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-primary w-100">Filter</button>
            </div>
        </form>

        <!-- Tickets Table -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Created</th>
                        <th>Assigned To</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->subject }}</td>
                            <td>
                                <i class="fa fa-circle text-{{ getStatusColor($ticket->status) }}"></i>
                                <span
                                    class="badge bg-{{ getStatusColor($ticket->status) }}">{{ $ticket->status->label() }}</span>
                            </td>
                            <td>
                                <i class="fa fa-flag text-{{ getPriorityColor($ticket->priority) }}"></i>
                                <span
                                    class="badge bg-{{ getPriorityColor($ticket->priority) }}">{{ $ticket->priority->label() }}</span>
                            </td>
                            <td>{{ humanDate($ticket->created_at) }}</td>
                            <td>{{ $ticket->assignedAgent?->name ?? '-' }}</td>
                            <td class="d-flex gap-1">
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewTicketModal"
                                    data-ticket-id="{{ $ticket->id }}" data-subject="{{ $ticket->subject }}"
                                    data-description="{{ $ticket->description }}">
                                    View
                                </button>

                                {{-- <a href="{{ route('admin.tickets.edit', $ticket) }}"
                                    class="btn btn-sm btn-warning">Edit</a> --}}
                                <button type="button" class="btn btn-sm btn-warning editTicketBtn"
                                    data-id="{{ $ticket->id }}" data-subject="{{ $ticket->subject }}"
                                    data-message="{{ $ticket->message }}" data-status="{{ $ticket->status->value }}"
                                    data-priority="{{ $ticket->priority }}" data-bs-toggle="modal"
                                    data-bs-target="#editTicketModal">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>

                                <button class="btn btn-sm btn-success open-reply-modal" data-id="{{ $ticket->id }}"
                                    data-ticket-id="{{ $ticket->id }}" data-subject="{{ $ticket->subject }}"
                                    data-action="{{ route('admin.tickets.reply', $ticket->id) }}">
                                    Reply
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No tickets found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $tickets->links() }}
        </div>

    </div>

    @include('admin.tickets.modals.create')
    @include('admin.tickets.modals.reply')
    @include('admin.tickets.modals.view')
    @include('admin.tickets.modals.edit')

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const replyForm = document.getElementById('replyForm');

            document.querySelectorAll('.open-reply-modal').forEach(button => {
                button.addEventListener('click', function() {
                    const modal = new bootstrap.Modal(document.getElementById('replyTicketModal'));

                    const form = document.getElementById('replyForm');
                    form.action = this.dataset.action;

                    document.querySelector('#replyTicketSubject').textContent = this.dataset
                        .subject;
                    document.querySelector('#replyTicketId').textContent =
                        `#${this.dataset.ticketId}`;

                    // reset form
                    form.querySelector('textarea[name="message"]').value = '';
                    document.getElementById('message-error').classList.add('d-none');

                    // load message thread
                    const thread = document.getElementById('messageThread');
                    thread.innerHTML = '<p class="text-muted text-center">Loading messages...</p>';

                    fetch(`/admin/tickets/${this.dataset.ticketId}/messages`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.length === 0) {
                                thread.innerHTML =
                                    '<p class="text-muted text-center">No messages yet.</p>';
                            } else {
                                thread.innerHTML = data.map(msg => {
                                    const isAdmin = msg.user.is_admin;
                                    const avatar = msg.user.avatar ||
                                        '/default-avatar.png';
                                    const msgClass = isAdmin ? 'bg-primary text-white' :
                                        'bg-light';

                                    const attachments = msg.attachments.map(file => `
                <div>
                    <a href="${file.url}" target="_blank">${file.name}</a>
                </div>
            `).join('');

                                    return `
                <div class="d-flex gap-2 mb-3">
                    <img src="${avatar}" class="rounded-circle" width="40" height="40">
                    <div class="flex-grow-1">
                        <div class="small text-muted fw-bold mb-1">
                            ${msg.user.name} ${isAdmin ? '<span class="badge bg-warning text-dark">Admin</span>' : ''}
                            <span class="float-end">${msg.time}</span>
                        </div>
                        <div class="border rounded p-2 ${msgClass}">
                            ${msg.content}
                            ${attachments}
                        </div>
                    </div>
                </div>
            `;
                                }).join('');
                            }
                        });


                    modal.show();
                });
            });
        });

        document.querySelectorAll('[data-bs-target="#viewTicketModal"]').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('ticketSubject').innerText = this.dataset.subject;
                document.getElementById('ticketDescription').innerText = this.dataset.description;

                const status = this.dataset.status;
                const priority = this.dataset.priority;

                const statusEl = document.getElementById('ticketStatus');
                statusEl.innerText = status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ');
                statusEl.className = `badge bg-${getStatusColor(status)}`;

                const priorityEl = document.getElementById('ticketPriority');
                priorityEl.innerText = priority.charAt(0).toUpperCase() + priority.slice(1);
                priorityEl.className = `badge bg-${getPriorityColor(priority)}`;
            });
        });

        function getStatusColor(status) {
            return {
                open: 'warning',
                in_progress: 'primary',
                awaiting_reply: 'info',
                closed: 'success'
            } [status] || 'secondary';
        }

        function getPriorityColor(priority) {
            return {
                low: 'secondary',
                medium: 'info',
                high: 'warning',
                urgent: 'danger'
            } [priority] || 'secondary';
        }

        function submitReply(e) {
            e.preventDefault();
            const form = e.target;
            const url = form.action;
            const data = new FormData(form);

            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Sending...';

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: data
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        form.querySelector('textarea[name="message"]').value = '';
                        document.getElementById('message-error').classList.add('d-none');
                        // Reload message thread
                        document.querySelector(`.open-reply-modal[data-ticket-id="${response.ticket_id}"]`).click();
                    } else if (response.errors) {
                        document.getElementById('message-error').textContent = response.errors.message?.[0];
                        document.getElementById('message-error').classList.remove('d-none');
                    }
                })
                .catch(() => alert('Something went wrong.'))
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Send Reply';
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('editTicketModal');
            const form = document.getElementById('editTicketForm');

            document.querySelectorAll('.editTicketBtn').forEach(button => {
                button.addEventListener('click', function() {
                    const ticketId = this.dataset.id;

                    // Populate form fields
                    form.action = `/tickets/${ticketId}`;
                    form.querySelector('#subject').value = this.dataset.subject;
                    form.querySelector('#message').value = this.dataset.message;
                    form.querySelector('#status').value = this.dataset.status;
                    form.querySelector('#priority').value = this.dataset.priority;
                });
            });

            // AJAX Submit
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                const url = form.action;

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Something went wrong');
                        return response.json();
                    })
                    .then(data => {
                        // Close modal, show success, reload or update row
                        const modalInstance = bootstrap.Modal.getInstance(modal);
                        modalInstance.hide();

                        // Optional: update table row without reload
                        location.reload();
                    })
                    .catch(error => {
                        alert(error.message);
                    });
            });
        });
    </script>
@endsection
