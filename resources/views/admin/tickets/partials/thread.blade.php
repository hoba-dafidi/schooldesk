<ul class="timeline list-unstyled">
    @foreach ($messages as $message)
        <li class="d-flex mb-4 align-items-start">
            <div class="me-3">
                <img src="{{ $message->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($message->user->name) }}"
                    class="rounded-circle border shadow-sm" style="width: 45px; height: 45px;" alt="Avatar">
            </div>
            <div class="flex-grow-1">
                <div class="bg-light border rounded p-3 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong>{{ $message->user->name }}</strong>
                        <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-2">{{ $message->message }}</p>

                    @if ($message->attachments->count())
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($message->attachments as $file)
                                <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                                    class="badge bg-secondary">{{ $file->original_name }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </li>
    @endforeach
</ul>
