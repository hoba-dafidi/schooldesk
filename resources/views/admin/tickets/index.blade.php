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
                                <button class="btn btn-sm btn-info viewTicketBtn" data-bs-toggle="modal"
                                    data-bs-target="#viewTicketModal" data-ticket-id="{{ $ticket->id }}"
                                    data-subject="{{ $ticket->subject }}" data-description="{{ $ticket->description }}">
                                    View
                                </button>

                                {{-- <a href="{{ route('admin.tickets.edit', $ticket) }}"
                                    class="btn btn-sm btn-warning">Edit</a> --}}
                                {{-- <button type="button" class="btn btn-warning btn-sm editTicketBtn"
                                    data-id="{{ $ticket->id }}" data-bs-toggle="modal" data-bs-target="#editTicketModal">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button> --}}



                                <button class="btn btn-sm btn-secondary assignTicketBtn"
                                    data-ticket-id="{{ $ticket->id }}">
                                    <i class="fas fa-user-check me-1"></i> Assign
                                </button>

                                <button class="btn btn-sm btn-warning"
                                    onclick="openReplyModal('{{ $ticket->id }}', '{{ $ticket->subject }}')">
                                    <i class="fas fa-reply me-1"></i> Reply
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
    @include('admin.tickets.modals.assign')


@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // REPLY
            const replydropzone = document.getElementById('replyDropzoneArea');
            const replyinput = document.getElementById('reply_attachment_input');
            const replyfileList = document.getElementById('reply_file_list');
            const replyprogressWrapper = document.getElementById('reply_progress_wrapper');
            const replyprogressBar = replyprogressWrapper.querySelector('.progress-bar');
            const replyForm = document.getElementById('replyForm');
            let droppedReplyFiles = [];

            // Drag & Drop handling
            replydropzone.addEventListener('click', () => replyinput.click());
            replydropzone.addEventListener('dragover', e => {
                e.preventDefault();
                replydropzone.classList.add('border-primary');
            });
            replydropzone.addEventListener('dragleave', () => replydropzone.classList.remove('border-primary'));
            replydropzone.addEventListener('drop', e => {
                e.preventDefault();
                replydropzone.classList.remove('border-primary');
                handleFiles(e.dataTransfer.files);
            });
            replyinput.addEventListener('change', () => handleFiles(replyinput.files));

            function handleFiles(files) {
                for (const file of files) {
                    const validTypes = ['application/pdf', 'image/jpeg', 'image/png'];
                    if (!validTypes.includes(file.type)) {
                        alert(`${file.name} is not a supported file type.`);
                        continue;
                    }

                    droppedReplyFiles.push(file);

                    const item = document.createElement('div');
                    item.className = 'mb-1';

                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = e => {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.maxWidth = '100px';
                            img.className = 'me-2 mb-1 rounded border';
                            item.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    }

                    const fileInfo = document.createElement('span');
                    fileInfo.textContent = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                    item.appendChild(fileInfo);

                    replyfileList.appendChild(item);
                }
            }

            // Submit Reply via AJAX
            replyForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(replyForm);
                for (const file of droppedReplyFiles) {
                    formData.append('attachments[]', file);
                }

                replyprogressWrapper.classList.remove('d-none');
                replyprogressBar.style.width = '0%';
                replyprogressBar.textContent = 'Uploading...';

                const ticketId = document.getElementById('reply_ticket_id').value;

                const xhr = new XMLHttpRequest();
                xhr.open('POST', `/admin/tickets/${ticketId}/reply`, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        replyprogressBar.style.width = `${percent}%`;
                        replyprogressBar.textContent = `Uploading... ${percent}%`;
                    }
                });

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        replyprogressBar.classList.add('bg-success');
                        replyprogressBar.textContent = 'Reply Sent!';
                        bootstrap.Modal.getInstance(document.getElementById('replyTicketModal')).hide();
                        setTimeout(() => location.reload(), 1000); // or reload thread dynamically
                    } else {
                        replyprogressBar.classList.add('bg-danger');
                        replyprogressBar.textContent = 'Reply Failed';
                    }
                };

                xhr.onerror = function() {
                    replyprogressBar.classList.add('bg-danger');
                    replyprogressBar.textContent = 'Network Error';
                };

                xhr.send(formData);
            });


            // Function to open and populate reply modal
            window.openReplyModal = function(ticketId, subject) {
                document.getElementById('reply_ticket_id').value = ticketId;
                document.getElementById('replyTicketSubject').textContent = subject;
                document.getElementById('replyTicketId').textContent = `#${ticketId}`;
                document.getElementById('messageThread').innerHTML =
                    '<p class="text-muted text-center">Loading...</p>';

                // Reset attachments
                droppedReplyFiles = [];
                replyfileList.innerHTML = '';
                replyprogressWrapper.classList.add('d-none');
                replyprogressBar.style.width = '0%';
                replyprogressBar.textContent = 'Uploading...';

                fetch(`/admin/tickets/${ticketId}/messages`)
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('messageThread').innerHTML = html;
                    });

                const replymodal = new bootstrap.Modal(document.getElementById('replyTicketModal'));
                replymodal.show();
            }



            // VIEW MODAL
            let currentViewTicketId = null;

            // function openViewModal(ticketId) {
            document.querySelectorAll('.viewTicketBtn').forEach(button => {
                button.addEventListener('click', function() {
                    const ticketId = this.dataset.ticketId;
                    currentViewTicketId = ticketId;
                    // const viewmodal = new bootstrap.Modal(document.getElementById(
                    //     'viewTicketModal'));
                    document.getElementById('ticketMessages').innerHTML =
                        '<p class="text-muted text-center">Loading messages...</p>';
                    document.getElementById('ticketAttachmentList').innerHTML = '';
                    document.getElementById('ticketAttachments').classList.add('d-none');

                    fetch(`/admin/tickets/${ticketId}`)
                        .then(res => res.json())
                        .then(ticket => {
                            document.getElementById('ticketSubject').textContent = ticket
                                .subject;
                            document.getElementById('ticketDescription').textContent = ticket
                                .description ??
                                'No description';
                            document.getElementById('ticketStatus').textContent = ticket.status;
                            document.getElementById('ticketStatus').className =
                                `badge ${getStatusClass(ticket.status)}`;
                            document.getElementById('ticketPriority').textContent = ticket
                                .priority;
                            document.getElementById('ticketPriority').className =
                                `badge ${getPriorityClass(ticket.priority)}`;

                            if (ticket.attachments?.length) {
                                document.getElementById('ticketAttachments').classList.remove(
                                    'd-none');
                                ticket.attachments.forEach(att => {
                                    const a = document.createElement('a');
                                    a.href = att.url;
                                    a.target = '_blank';
                                    a.className = 'badge bg-secondary text-white';
                                    a.textContent = att.name;
                                    document.getElementById('ticketAttachmentList')
                                        .appendChild(a);
                                });
                            }

                            // Load messages
                            fetch(`/admin/tickets/${ticketId}/messages`)
                                .then(res => res.text())
                                .then(html => {
                                    document.getElementById('ticketMessages').innerHTML =
                                        html;
                                });

                            // viewmodal.show();
                        });

                    document.getElementById('closeTicketBtn').onclick = () => updateTicketStatus(
                        'closed');
                    document.getElementById('escalateTicketBtn').onclick = () => updateTicketStatus(
                        'escalated');
                });
            });

            function updateTicketStatus(status) {
                if (!currentViewTicketId) return;

                fetch(`/admin/tickets/${currentViewTicketId}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            status
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert(`Ticket updated to ${status}`);
                            bootstrap.Modal.getInstance(document.getElementById('viewTicketModal')).hide();
                            location.reload();
                        } else {
                            alert('Failed to update status');
                        }
                    });
            }

            function getStatusClass(status) {
                switch (status) {
                    case 'open':
                        return 'bg-success';
                    case 'closed':
                        return 'bg-secondary';
                    case 'pending':
                        return 'bg-warning text-dark';
                    default:
                        return 'bg-light text-dark';
                }
            }

            function getPriorityClass(priority) {
                switch (priority) {
                    case 'low':
                        return 'bg-success';
                    case 'medium':
                        return 'bg-warning text-dark';
                    case 'high':
                        return 'bg-danger';
                    default:
                        return 'bg-light text-dark';
                }
            }

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
                const replytargetform = e.target;
                const url = replytargetform.action;
                const data = new FormData(replytargetform);

                const submitBtn = replytargetform.querySelector('button[type="submit"]');
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
                            replytargetform.querySelector('textarea[name="message"]').value = '';
                            document.getElementById('message-error').classList.add('d-none');
                            // Reload message thread
                            document.querySelector(`.open-reply-modal[data-ticket-id="${response.ticket_id}"]`)
                                .click();
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

            // EDIT MODAL
            let droppedEditFiles = [];

            const editmodal = document.getElementById('editTicketModal');
            const editform = document.getElementById('editTicketForm');
            const editdropzone = document.getElementById('editDropzoneArea');
            const editinput = document.getElementById('edit_attachment_input');
            const editfileList = document.getElementById('edit_file_list');
            const editprogressWrapper = document.getElementById('edit_progress_wrapper');
            const editprogressBar = editprogressWrapper.querySelector('.progress-bar');

            // Open file input on click
            editdropzone.addEventListener('click', () => editinput.click());

            // Drag-over highlight
            editdropzone.addEventListener('dragover', e => {
                e.preventDefault();
                editdropzone.classList.add('border-primary');
            });

            editdropzone.addEventListener('dragleave', () => {
                editdropzone.classList.remove('border-primary');
            });

            // Handle drop
            editdropzone.addEventListener('drop', e => {
                e.preventDefault();
                editdropzone.classList.remove('border-primary');
                handleFiles(e.dataTransfer.files);
            });

            editinput.addEventListener('change', () => handleFiles(editinput.files));

            function handleFiles(files) {
                for (const file of files) {
                    const validTypes = ['application/pdf', 'image/jpeg', 'image/png'];
                    if (!validTypes.includes(file.type)) {
                        alert(`${file.name} is not a supported file type.`);
                        continue;
                    }

                    droppedEditFiles.push(file);

                    const item = document.createElement('div');
                    item.className = 'mb-1';

                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = e => {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.maxWidth = '100px';
                            img.className = 'me-2 mb-1 rounded border';
                            item.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    }

                    const fileInfo = document.createElement('span');
                    fileInfo.textContent = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                    item.appendChild(fileInfo);
                    editfileList.appendChild(item);
                }
            }

            // Handle form submit via AJAX
            editform.addEventListener('submit', function(e) {
                e.preventDefault();

                const editformData = new FormData(editform);
                for (const file of droppedEditFiles) {
                    editformData.append('attachments[]', file);
                }
                editformData.append('_method', 'PUT');

                editprogressWrapper.classList.remove('d-none');
                editprogressBar.style.width = '0%';
                editprogressBar.textContent = 'Uploading...';

                const ticketId = editform.querySelector('input[name="ticket_id"]').value;
                const url = `/admin/tickets/${ticketId}`;

                const xhr = new XMLHttpRequest();
                xhr.open('POST', url, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        editprogressBar.style.width = `${percent}%`;
                        editprogressBar.textContent = `Uploading... ${percent}%`;
                    }
                });

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        editprogressBar.classList.add('bg-success');
                        editprogressBar.textContent = 'Updated!';
                        bootstrap.Modal.getInstance(editmodal).hide();
                        location.reload(); // or update row manually
                    } else {
                        editprogressBar.classList.add('bg-danger');
                        editprogressBar.textContent = 'Failed to update';
                    }
                };

                xhr.onerror = function() {
                    editprogressBar.classList.add('bg-danger');
                    editprogressBar.textContent = 'Network Error';
                };

                xhr.send(editformData);
            });

            // Populate form dynamically
            const existingAttachments = document.getElementById('existing_attachments');
            document.querySelectorAll('.editTicketBtn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;

                    // Reset state
                    droppedEditFiles = [];
                    editfileList.innerHTML = '';
                    existingAttachments.innerHTML = '';
                    editprogressWrapper.classList.add('d-none');
                    editprogressBar.style.width = '0%';
                    editprogressBar.textContent = 'Uploading...';

                    fetch(`/admin/tickets/${id}`)
                        .then(res => res.json())
                        .then(data => {
                            editform.querySelector('#edit_ticket_id').value = data.id;
                            editform.querySelector('#edit_subject').value = data.subject;
                            editform.querySelector('#edit_status').value = data.status;
                            editform.querySelector('#edit_priority').value = data.priority;
                            editform.querySelector('#edit_message').value = data.messages
                                .map(m =>
                                    `${m.user?.name || 'User'} (${new Date(m.created_at).toLocaleString()}):\n${m.message}`
                                )
                                .join('\n\n');


                            if (data.attachments && data.attachments.length) {
                                data.attachments.forEach(att => {
                                    const link = document.createElement('a');
                                    link.href = att.url;
                                    link.target = '_blank';
                                    link.className =
                                        'badge bg-secondary text-white me-2 mb-1';
                                    link.textContent = att.name;
                                    existingAttachments.appendChild(link);
                                });
                            } else {
                                existingAttachments.innerHTML =
                                    '<em class="text-muted">No attachments</em>';
                            }
                        });
                });
            });



            // CREATE MODAL
            const dropzoneArea = document.getElementById('dropzoneArea');
            const attachmentInput = document.getElementById('attachmentInput');
            const createfileList = document.getElementById('fileList');
            const createform = document.getElementById('createTicketForm');
            const createmodal = document.getElementById('createTicketModal');

            let droppedFiles = [];

            // Add progress bar dynamically
            const createprogressWrapper = document.createElement('div');
            createprogressWrapper.className = 'progress mt-3 d-none';
            createprogressWrapper.innerHTML = `
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%">Uploading...</div>
    `;
            createfileList.after(createprogressWrapper);

            // Handle drag/drop & click
            dropzoneArea.addEventListener('click', () => attachmentInput.click());
            dropzoneArea.addEventListener('dragover', e => {
                e.preventDefault();
                dropzoneArea.classList.add('border-primary');
            });
            dropzoneArea.addEventListener('dragleave', () => {
                dropzoneArea.classList.remove('border-primary');
            });
            dropzoneArea.addEventListener('drop', e => {
                e.preventDefault();
                dropzoneArea.classList.remove('border-primary');
                handleFiles(e.dataTransfer.files);
            });
            attachmentInput.addEventListener('change', () => handleFiles(attachmentInput.files));

            function handleFiles(files) {
                for (const file of files) {
                    const validTypes = ['application/pdf', 'image/jpeg', 'image/png'];
                    if (!validTypes.includes(file.type)) {
                        alert(`${file.name} is not a supported file type.`);
                        continue;
                    }

                    droppedFiles.push(file);

                    const item = document.createElement('div');
                    item.className = 'mb-1';

                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = e => {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.maxWidth = '100px';
                            img.className = 'me-2 mb-1 rounded border';
                            item.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    }

                    const fileInfo = document.createElement('span');
                    fileInfo.textContent = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                    item.appendChild(fileInfo);

                    createfileList.appendChild(item);
                }
            }

            createform.addEventListener('submit', function(e) {
                e.preventDefault();
                const createformData = new FormData(createform);
                for (const file of droppedFiles) {
                    createformData.append('attachments[]', file);
                }

                // Show and reset progress
                createprogressWrapper.classList.remove('d-none');
                const createprogressBar = createprogressWrapper.querySelector('.progress-bar');
                createprogressBar.style.width = '0%';
                createprogressBar.textContent = 'Uploading...';

                // AJAX with progress
                const xhr = new XMLHttpRequest();
                xhr.open('POST', `{{ route('admin.tickets.store') }}`, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        createprogressBar.style.width = `${percent}%`;
                        createprogressBar.textContent = `Uploading... ${percent}%`;
                    }
                });

                xhr.onload = function() {
                    if (xhr.status === 200 || xhr.status === 201) {
                        createprogressBar.classList.remove('bg-danger');
                        createprogressBar.classList.add('bg-success');
                        createprogressBar.textContent = 'Upload Complete!';
                        bootstrap.Modal.getInstance(createmodal).hide();
                        setTimeout(() => location.reload(), 1000); // Optional reload
                    } else {
                        createprogressBar.classList.remove('bg-success');
                        createprogressBar.classList.add('bg-danger');
                        createprogressBar.textContent = 'Upload Failed';
                    }
                };

                xhr.onerror = function() {
                    createprogressBar.classList.add('bg-danger');
                    createprogressBar.textContent = 'Network Error';
                };

                xhr.send(createformData);
            });
        });

        // Assign
        document.querySelectorAll('.assignTicketBtn').forEach(button => {
            button.addEventListener('click', function() {
                const ticketId = this.dataset.ticketId;
                document.getElementById('assign_ticket_id').value = ticketId;
                new bootstrap.Modal(document.getElementById('assignTicketModal')).show();
            });
        });

        document.getElementById('assignTicketForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const ticketId = form.querySelector('#assign_ticket_id').value;
            const assignedTo = form.querySelector('#assigned_to').value;

            fetch(`/admin/tickets/${ticketId}/assign`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        assigned_to: assignedTo
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('assignTicketModal')).hide();
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to assign ticket');
                    }
                })
                .catch(() => alert('Request failed'));


        });
    </script>
@endpush

@push('styles')
    <style>
        #fileList img {
            display: inline-block;
            margin-right: 8px;
            vertical-align: middle;
        }

        .timeline {
            padding-left: 0;
            position: relative;
            margin-left: 1rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 22px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline li {
            position: relative;
        }

        .timeline li::before {
            content: '';
            position: absolute;
            left: 16px;
            top: 10px;
            width: 12px;
            height: 12px;
            background: #0d6efd;
            border-radius: 50%;
            z-index: 1;
        }
    </style>
@endpush
