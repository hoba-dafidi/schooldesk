<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Recent Transactions</h5>
    </div>
    <div class="card-body">
        <ul class="p-0 m-0">
            @forelse($transactions as $invoice)
                <li class="d-flex mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <img src="{{ asset('assets/img/icons/unicons/' . ($invoice->paymentGateway?->slug ?? 'wallet') . '.png') }}"
                            class="rounded" alt="{{ $invoice->paymentGateway?->name ?? 'Gateway' }}">
                    </div>
                    <div class="d-flex w-100 align-items-center justify-content-between">
                        <div class="me-2">
                            <h6 class="mb-0">{{ $invoice->title ?? 'Subscription Payment' }}</h6>
                            <small
                                class="text-muted">{{ $invoice->formatted_paid_at ?? $invoice->formatted_due_date }}</small>
                        </div>
                        <div class="d-flex align-items-center flex-column text-end">
                            <h6 class="mb-0 text-{{ $invoice->statusColor }}">
                                {{ $invoice->is_paid ? '+' : '-' }}{{ $invoice->formatted_total }}
                            </h6>
                            <span class="badge bg-{{ $invoice->statusColor }} mt-1">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </div>
                    </div>
                </li>
            @empty
                <li class="text-muted text-center">No transactions yet</li>
            @endforelse
        </ul>
    </div>
</div>
