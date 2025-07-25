<div class="message mb-3 border rounded p-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <strong>{{ $message->user->name ?? 'System' }}</strong>
            <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
        </div>
    </div>
    <div class="message-content mb-2">
        {!! nl2br(e($message->message)) !!}
    </div>

    @if ($message->attachments && $message->attachments->count())
        <div class="attachments mt-2">
            <strong>Attachments:</strong>
            <ul class="list-unstyled">
                @foreach ($message->attachments as $attachment)
                    <li>
                        <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank">
                            {{ $attachment->original_name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
