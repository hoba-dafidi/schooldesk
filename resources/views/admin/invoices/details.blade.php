@extends('layouts/contentNavbarLayout')

@section('title', 'Invoice #' . $invoice->invoice_number)

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <x-flash-messages />

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <h4 class="fw-bold mb-0">Invoice #{{ $invoice->invoice_number }}</h4>

            <div class="d-flex flex-wrap gap-2">
                <button onclick="window.print()" class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip"
                    title="Print Invoice">
                    <i class="bx bx-printer"></i>
                </button>
                <a href="{{ route('admin.invoices.pdf', $invoice->id) }}" class="btn btn-outline-primary btn-sm"
                    target="_blank" data-bs-toggle="tooltip" title="Download PDF">
                    <i class="bx bx-download"></i>
                </a>
                <form action="{{ route('admin.invoices.email', $invoice->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Email Invoice">
                        <i class="bx bx-envelope"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="mb-2">Billed To:</h6>
                        <p class="mb-0">{{ $invoice->user->name ?? '-' }}</p>
                        <small>{{ $invoice->user->email ?? '-' }}</small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="mb-2">Invoice Info:</h6>
                        <p class="mb-0"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                        <p class="mb-0"><strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</p>
                        <p class="mb-0"><strong>Due:</strong> {{ $invoice->due_date?->format('M d, Y') ?? '-' }}</p>
                        <p class="mb-0"><strong>Status:</strong> {!! $invoice->status_label !!}</p>
                    </div>
                </div>

                <hr>

                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->title }}</strong><br>
                                        <small>{{ $item->description }}</small>
                                    </td>
                                    <td class="text-end">
                                        {{ strtoupper($invoice->currency) . ' ' . number_format($item->amount / 100, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th>Subtotal</th>
                                <td class="text-end">{{ $invoice->formatted_total }}</td>
                            </tr>
                            @if ($invoice->tax_amount)
                                <tr>
                                    <th>Tax</th>
                                    <td class="text-end">{{ $invoice->formatted_tax }}</td>
                                </tr>
                            @endif
                            @if ($invoice->discount_amount)
                                <tr>
                                    <th>Discount</th>
                                    <td class="text-end">-{{ $invoice->formatted_discount }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Total</th>
                                <td class="text-end fw-bold">{{ $invoice->formatted_total }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if ($invoice->notes)
                    <div class="mt-4">
                        <h6>Notes</h6>
                        <p class="mb-0">{{ $invoice->notes }}</p>
                    </div>
                @endif

                @if ($invoice->gateway_response)
                    <div class="mt-4">
                        <h6>Gateway Response</h6>
                        <pre class="bg-light p-3 rounded small">{{ json_encode($invoice->gateway_response, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
