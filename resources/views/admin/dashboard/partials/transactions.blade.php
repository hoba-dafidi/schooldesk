<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0">Recent Transactions</h5>
    </div>
    <div class="card-body table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->created_at->format('d M, Y') }}</td>
                        <td>{{ $invoice->user->name ?? '-' }}</td>
                        <td>&#8358;{{ number_format($invoice->amount / 100, 2) }}</td>
                        <td><span
                                class="badge bg-{{ $invoice->status === 'success' ? 'success' : 'warning' }}">{{ ucfirst($invoice->status) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No Invoice</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
