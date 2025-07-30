@extends('layouts/contentNavbarLayout')

@section('title', 'My Invoices')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">


        <h4 class="fw-bold mb-4">My Invoices</h4>

        <div class="card">
            <div class="table-responsive">
                <table id="invoiceTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Invoice No</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $index => $invoice)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->formatted_total }}</td>
                                <td>
                                    <span class="badge bg-{{ $invoice->status_color }}" data-bs-toggle="tooltip"
                                        title="{{ ucfirst($invoice->status) }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('user.invoices.show', $invoice->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-show"></i> View
                                    </a>
                                    @if ($invoice->is_unpaid)
                                        <a href="{{ route('user.invoices.pay', $invoice->id) }}"
                                            class="btn btn-sm btn-success">
                                            <i class="bx bx-credit-card"></i> Pay
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Tooltip & DataTable Init -->
    <script>
        $(document).ready(function() {
            $('#invoiceTable').DataTable({
                pageLength: 10,
                order: [
                    [1, 'desc']
                ],
            });

            // Enable Bootstrap tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
