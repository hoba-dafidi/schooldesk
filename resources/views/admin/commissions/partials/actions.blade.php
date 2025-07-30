<div class="d-flex gap-2">
    @if ($row->status === 'pending')
        <button class="btn btn-sm btn-success approve-btn" data-id="{{ $row->id }}">Approve</button>
    @endif

    @if (!$row->is_paid)
        <button class="btn btn-sm btn-outline-primary pay-btn" data-id="{{ $row->id }}">Mark as Paid</button>
    @else
        <span class="text-muted small">Paid {{ $row->paid_at->format('d M Y') }}</span>
    @endif
</div>
