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
    </div>
</div>
