<span class="badge bg-{{ $row->status === 'approved' ? 'success' : 'warning' }}">
    {{ ucfirst($row->status) }}
</span>
@if ($row->is_paid)
    <span class="badge bg-info">Paid</span>
@endif
