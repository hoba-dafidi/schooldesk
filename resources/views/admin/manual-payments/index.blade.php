@extends('layouts/contentNavbarLayout')

@section('title', 'Manual Payment Reviews')

{{-- @section('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
@endsection

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
@endsection --}}

@section('content')
    <div class="container mt-4">
        <h4 class="mb-4">Manual Payment Submissions</h4>

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <form method="GET" class="d-flex gap-2">
                <select name="status" class="form-select w-auto" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <input type="text" name="search" placeholder="Search user..." class="form-control w-auto"
                    value="{{ request('search') }}">
                <button class="btn btn-outline-primary">Filter</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Invoice</th>
                        <th>Amount</th>
                        <th>Submitted At</th>
                        <th>Status</th>
                        <th>Proof</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($manualPayments as $payment)
                        <tr id="payment-row-{{ $payment->id }}" data-id="{{ $payment->id }}"
                            data-user="{{ $payment->user->name }}" data-invoice="{{ $payment->invoice->invoice_number }}"
                            data-amount="{{ number_format($payment->amount / 100, 2) }}"
                            data-created="{{ $payment->created_at->format('Y-m-d H:i') }}"
                            data-note="{{ e($payment->note ?? '') }}"
                            data-proof="{{ $payment->proof_url ? $payment->proof_url : '' }}">



                            <td>{{ $payment->user->name }}</td>
                            <td><a
                                    href="{{ route('admin.invoices.show', $payment->invoice_id) }}">#{{ $payment->invoice->invoice_number }}</a>
                            </td>
                            <td>₦{{ number_format($payment->amount / 100, 2) }}</td>
                            <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                            <td><span id="status-badge-{{ $payment->id }}"
                                    class="badge bg-{{ $payment->status === 'approved' ? 'success' : ($payment->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($payment->status) }}</span>
                            </td>
                            <td>
                                @if ($payment->proof_url)
                                    <a href="{{ $payment->proof_url }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">View</a>
                                @else
                                    <span class="text-muted">No file</span>
                                @endif
                            </td>
                            <td>
                                @if ($payment->status === 'pending')
                                    <button class="btn btn-sm btn-primary open-review-modal"
                                        data-id="{{ $payment->id }}">Review</button>
                                @else
                                    <em class="text-muted">Reviewed</em>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Review Modal --}}
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-3">
                <div class="modal-header bg-primary text-white align-items-center">
                    <h5 class="modal-title">Review Manual Payment</h5>
                    <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal" aria-label="Close"
                        style="margin-right: 15px;"></button>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" id="reviewPaymentId">
                    <div class="col-md-6">
                        <label class="form-label">User</label>
                        <input type="text" class="form-control" id="reviewUser" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Invoice Number</label>
                        <input type="text" class="form-control" id="reviewInvoiceNumber" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Amount</label>
                        <input type="text" class="form-control" id="reviewAmount" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Submitted At</label>
                        <input type="text" class="form-control" id="reviewCreatedAt" readonly>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Note</label>
                        <textarea class="form-control" id="reviewNote" rows="3" readonly></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Proof of Payment</label><br>
                        <div id="reviewProofWrapper"></div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-0">
                    <button class="btn btn-success m-2 review-action" data-status="approved">Approve</button>
                    <button class="btn btn-outline-danger m-2 review-action" data-status="rejected">Reject</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('reviewModal'));

            $('.open-review-modal').on('click', function() {
                const id = $(this).data('id');
                const row = $(`#payment-row-${id}`);
                const note = row.data('note') || ''; // fallback if note is missing

                $('#reviewPaymentId').val(id);
                $('#reviewUser').val(row.data('user'));
                $('#reviewInvoiceNumber').val('#' + row.data('invoice'));
                $('#reviewAmount').val('₦' + row.data('amount'));
                $('#reviewCreatedAt').val(row.data('created'));
                $('#reviewNote').val(note);

                const proofUrl = row.data('proof');


                if (proofUrl) {
                    $('#reviewProofWrapper').html(
                        `<a href="${proofUrl}" target="_blank">
                <img src="${proofUrl}" class="img-fluid rounded shadow-sm" style="max-height: 300px;">
             </a>`
                    );
                } else {
                    $('#reviewProofWrapper').html('<span class="text-muted">No file uploaded.</span>');
                }

                modal.show();
            });


            $('.review-action').on('click', function() {
                const id = $('#reviewPaymentId').val();
                const status = $(this).data('status');
                const btn = $(this);
                btn.prop('disabled', true).text(status.charAt(0).toUpperCase() + status.slice(1) +
                    'ing...');

                $.ajax({
                    url: `/admin/manual-payments/${id}/review`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status
                    },
                    success: function(res) {
                        modal.hide();

                        const badge = $(`#status-badge-${id}`);
                        badge.removeClass('bg-warning bg-success bg-danger')
                            .addClass(status === 'approved' ? 'bg-success' : 'bg-danger')
                            .text(status.charAt(0).toUpperCase() + status.slice(1));

                        Toastify({
                            text: "Payment " + status + " successfully.",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: status === 'approved' ? "#198754" :
                                "#dc3545",
                        }).showToast();

                        $(`#payment-row-${id} .open-review-modal`).replaceWith(
                            '<em class="text-muted">Reviewed</em>');
                    },
                    error: function() {
                        Toastify({
                            text: "Something went wrong.",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#dc3545",
                        }).showToast();
                    },
                    complete: function() {
                        btn.prop('disabled', false).text(status.charAt(0).toUpperCase() + status
                            .slice(1));
                    }
                });
            });
        });
    </script>
@endpush
