@extends('layouts/contentNavbarLayout')

@section('title', 'Invoices')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Manage Invoices</h4>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif


        {{-- Filter Form --}}
        <form method="GET" class="row align-items-end g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="user" class="form-control" placeholder="Search by user email"
                    value="{{ request('user') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Filter by Status --</option>
                    @foreach (['pending', 'paid', 'refunded', 'void'] as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5 d-flex gap-2">
                <button class="btn btn-primary flex-fill">Filter</button>
                <a href="{{ route('admin.invoices.export', array_merge(request()->all(), ['format' => 'csv'])) }}"
                    class="btn btn-outline-secondary flex-fill" target="_blank">
                    Export CSV
                </a>
                <a href="{{ route('admin.invoices.export', array_merge(request()->all(), ['format' => 'pdf'])) }}"
                    class="btn btn-outline-secondary flex-fill" target="_blank">
                    Export PDF
                </a>
            </div>
        </form>

        {{-- Invoice Table --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Invoices</h5>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Due</th>
                            <th>Paid</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                <td>
                                    <div>{{ $invoice->user->name }}</div>
                                    <small class="text-muted">{{ $invoice->user->email }}</small>
                                </td>
                                <td>{{ $invoice->formatted_total }}</td>
                                <td>{!! $invoice->status_label !!}</td>
                                <td>{{ optional($invoice->due_date)->format('d M, Y') ?? '-' }}</td>
                                <td>{{ optional($invoice->paid_at)->format('d M, Y') ?? '-' }}</td>
                                <td class="text-nowrap">
                                    <button class="btn btn-sm btn-outline-info view-invoice-btn" data-bs-toggle="modal"
                                        data-bs-target="#invoiceDetailModal" data-invoice='@json($invoice)'
                                        data-items='@json($invoice->items)' data-user='@json($invoice->user)'
                                        data-subscription='@json($invoice->subscription?->plan?->name)'>
                                        <i class="bx bx-show"></i>
                                    </button>

                                    <form action="{{ route('admin.invoices.resend', $invoice) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary"
                                            title="Resend Invoice">
                                            <i class="bx bx-send"></i>
                                        </button>
                                    </form>

                                    @if ($invoice->status !== 'paid')
                                        <form action="{{ route('admin.invoices.markPaid', $invoice) }}" method="POST"
                                            class="d-inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success"
                                                onclick="return confirm('Mark as paid?')">
                                                <i class="bx bx-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No invoices found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $invoices->withQueryString()->links() }}
            </div>
        </div>
    </div>

    {{-- Invoice Detail Modal --}}
    <div class="modal fade" id="invoiceDetailModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-3" id="printable-invoice">
                <div class="modal-header bg-primary text-white align-items-center">
                    <h5 class="modal-title" id="invoiceModalLabel">Invoice Details</h5>
                    <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal" aria-label="Close"
                        style="margin-right: 15px;"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <p><strong>User:</strong> <span id="modalUserName"></span><br>
                            <small id="modalUserEmail" class="text-muted"></small>
                        </p>
                        <p><strong>Status:</strong> <span id="modalStatus"></span></p>
                        <p><strong>Total:</strong> <span id="modalTotal"></span></p>
                        <p><strong>Due Date:</strong> <span id="modalDueDate"></span></p>
                        <p><strong>Paid At:</strong> <span id="modalPaidAt"></span></p>
                        <p><strong>Gateway Ref:</strong> <span id="modalGatewayRef"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Subscription:</strong> <span id="modalPlan"></span></p>
                        <p><strong>Items:</strong></p>
                        <ul class="list-unstyled" id="modalItemsList">
                            <li class="text-muted">Loading...</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light p-2">
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-outline-dark" onclick="window.print()">Print</button>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-success" id="modalDownloadBtn" target="_blank">
                            <i class="bx bx-download"></i> PDF
                        </a>
                        <form method="POST" id="modalResendForm" action="#">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bx bx-send"></i> Resend
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.view-invoice-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const invoice = JSON.parse(button.dataset.invoice);
                    const items = JSON.parse(button.dataset.items);
                    const user = JSON.parse(button.dataset.user);
                    const plan = button.dataset.subscription || '-';

                    // Set Modal Content
                    document.getElementById('invoiceModalLabel').textContent = 'Invoice #' + invoice
                        .invoice_number;
                    document.getElementById('modalUserName').textContent = user.name || '-';
                    document.getElementById('modalUserEmail').textContent = user.email || '-';
                    document.getElementById('modalStatus').innerHTML = getBadge(invoice.status);
                    document.getElementById('modalTotal').textContent = formatCurrency(invoice
                        .total_amount, invoice.currency);
                    document.getElementById('modalDueDate').textContent = formatDate(invoice
                        .due_date);
                    document.getElementById('modalPaidAt').textContent = formatDate(invoice
                        .paid_at);
                    document.getElementById('modalGatewayRef').textContent = invoice
                        .payment_gateway_id || '-';
                    document.getElementById('modalPlan').textContent = plan;

                    const itemList = document.getElementById('modalItemsList');
                    itemList.innerHTML = '';

                    if (items.length === 0) {
                        itemList.innerHTML = `<li class="text-muted">No invoice items found.</li>`;
                    } else {
                        items.forEach(item => {
                            const li = document.createElement('li');
                            li.innerHTML =
                                `<strong>${item.description}</strong><br><small>${item.quantity} × ${formatCurrency(item.unit_price)} = ${formatCurrency(item.total_price)}</small>`;
                            itemList.appendChild(li);
                        });
                    }

                    // Update actions
                    const downloadBtn = document.getElementById('modalDownloadBtn');
                    const resendForm = document.getElementById('modalResendForm');

                    downloadBtn.href = `/admin/invoices/${invoice.id}/download`;
                    resendForm.action = `/admin/invoices/${invoice.id}/resend`;
                });
            });

            function formatCurrency(amount, currency = 'NGN') {
                if (!amount) return '-';
                return currency.toUpperCase() + ' ' + (amount / 100).toFixed(2);
            }

            function formatDate(dateStr) {
                if (!dateStr) return '-';
                const date = new Date(dateStr);
                return date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            function getBadge(status) {
                const map = {
                    paid: 'success',
                    pending: 'warning text-dark',
                    refunded: 'info text-white',
                    void: 'secondary'
                };
                const cls = map[status] || 'light text-dark';
                return `<span class="badge bg-${cls}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        @media print {
            body *:not(#printable-invoice):not(#printable-invoice *) {
                visibility: hidden !important;
            }

            #printable-invoice,
            #printable-invoice * {
                visibility: visible;
            }

            #printable-invoice {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: white;
            }

            .modal-footer,
            .btn-close {
                display: none !important;
            }
        }
    </style>
@endpush
