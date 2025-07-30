@if (!auth()->user()->bank_name || !auth()->user()->account_number || !auth()->user()->account_name)
    <div class="alert alert-warning d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Bank Details Missing!</strong> Please update your bank details before requesting a payout.
        </div>
        <a href="{{ route('user.settings') }}" class="btn btn-sm btn-primary">
            Update Now
        </a>
    </div>
@else
    <div class="alert alert-info d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-info-circle-fill me-2"></i>
            <strong>Bank Details:</strong> {{ auth()->user()->bank_name }} - {{ auth()->user()->account_number }}
            ({{ auth()->user()->account_name }})
        </div>
        <a href="{{ route('user.settings') }}" class="btn btn-sm btn-outline-primary">
            Edit
        </a>
    </div>
@endif
