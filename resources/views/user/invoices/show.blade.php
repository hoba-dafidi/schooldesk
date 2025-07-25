@extends('layouts/contentNavbarLayout')

@section('title', 'Invoice #' . $invoice->invoice_number)

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-flash-messages />

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                    <h4 class="fw-bold mb-2">Invoice #{{ $invoice->invoice_number }}</h4>
                    <div class="btn-group">
                        @if ($invoice->is_unpaid)
                            <a href="{{ route('user.invoices.pay', $invoice->id) }}" class="btn btn-success">
                                <i class="bx bx-credit-card"></i> Pay Now
                            </a>
                        @endif
                        <a href="{{ route('user.invoices.download', $invoice->id) }}" class="btn btn-outline-primary">
                            <i class="bx bx-download"></i> Download PDF
                        </a>
                        <form action="{{ route('user.invoices.email', $invoice->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="bx bx-envelope"></i> Email Invoice
                            </button>
                        </form>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="mb-2">Billed To:</h6>
                        <p class="mb-0">{{ $invoice->user->name }}</p>
                        <small>{{ $invoice->user->email }}</small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="mb-2">Invoice Info:</h6>
                        <p class="mb-0"><strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</p>
                        <p class="mb-0"><strong>Due Date:</strong> {{ $invoice->formatted_due_date ?? '-' }}</p>
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
                                        {{ strtoupper($invoice->currency) . ' ' . number_format($item->unit_price / 100, 2) }}
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
                        <p>{{ $invoice->notes }}</p>
                    </div>
                @endif

                <div class="mt-4">
                    <h6>Need help?</h6>
                    <p>If you have any questions about this invoice, contact support at
                        <a href="mailto:{{ config('app.support_email', 'support@example.com') }}">
                            {{ config('app.support_email', 'support@example.com') }}
                        </a>

                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
