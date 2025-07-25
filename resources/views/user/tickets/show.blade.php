@extends('layouts/contentNavbarLayout')

@section('title', 'Ticket: ' . $ticket->subject)

@section('content')
    <div class="container py-4">
        <h4 class="fw-bold mb-3">Ticket #{{ $ticket->id }}: {{ $ticket->subject }}</h4>

        <div class="mb-4">
            <span class="badge bg-{{ getStatusColor($ticket->status) }}">{{ $ticket->status->label() }}</span>
            <span class="badge bg-{{ getPriorityColor($ticket->priority) }}">{{ $ticket->priority->label() }}</span>
            <span class="text-muted ms-3">Opened {{ $ticket->created_at->diffForHumans() }}</span>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-bold">Initial Description</h6>
                <p>{{ $ticket->description }}</p>
            </div>
        </div>

        <div id="messages" class="mb-4">
            <h5 class="fw-bold">Conversation</h5>

            @forelse ($ticket->messages as $msg)
                <div class="mb-3" id="message-{{ $msg->id }}">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $msg->user->name }} {{ $msg->user->hasRole('admin') ? '(Admin)' : '' }}</strong>
                        <small class="text-muted">{{ $msg->created_at->format('d M Y, H:i') }}</small>
                    </div>
                    <div class="border rounded p-2 mt-1">
                        {{ $msg->content }}

                        @if ($msg->attachments->count())
                            <div class="mt-2">
                                @foreach ($msg->attachments as $attachment)
                                    <div>
                                        <a href="{{ Storage::url($attachment->file_path) }}" target="_blank">📎
                                            {{ $attachment->original_name }}</a>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if (auth()->id() === $msg->user_id)
                            <div class="mt-2">
                                <button class="btn btn-sm btn-outline-secondary edit-btn"
                                    data-id="{{ $msg->id }}">Edit</button>
                                <form method="POST" action="{{ route('user.tickets.deleteMessage', $msg) }}"
                                    class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Delete this message?')">Delete</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-muted">No replies yet.</p>
            @endforelse
        </div>

        <div class="card">
            <div class="card-header fw-bold">Reply</div>
            <div class="card-body">
                <form id="replyForm" action="{{ route('user.tickets.reply', $ticket) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="4" required></textarea>
                        @error('message')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Attachment (optional)</label>
                        <input type="file" name="attachment" class="form-control">
                        @error('attachment')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <button class="btn btn-primary">Send Reply</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // document.addEventListener("DOMContentLoaded", function() {
        //     if (window.Echo) {
        //         window.Echo.private(`ticket-{{ $ticket->id }}`)
        //             .listen('.message.created', (e) => {
        //                 const container = document.getElementById('messages');
        //                 container.insertAdjacentHTML('beforeend', e.view);
        //             });
        //     } else {
        //         console.warn("Echo is not available.");
        //     }
        // });
    </script>
@endpush
