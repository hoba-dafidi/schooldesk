@extends('layouts/contentNavbarLayout')

@section('title', 'Invoice #' . $invoice->invoice_number)

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold mb-4">Invoice #{{ $invoice->invoice_number }}</h4>

        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <p><strong>User:</strong> {{ $invoice->user->name }} ({{ $invoice->user->email }})</p>
                        <p><strong>School:</strong> {{ $invoice->school->name ?? 'N/A' }}</p>
                        <p><strong>Plan:</strong> {{ $invoice->plan->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong>
                            <span
                                class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </p>
                        <p><strong>Amount:</strong> ₦{{ number_format($invoice->total_amount / 100, 2) }}</p>
                        <p><strong>Created At:</strong> {{ $invoice->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>

                @if ($invoice->payment_method === 'manual')
                    <hr>
                    <h5>Manual Payment Info</h5>
                    <p><strong>Note:</strong> {{ $invoice->payment_note ?? 'N/A' }}</p>
                    @if ($invoice->payment_proof_path)
                        <p><strong>Proof:</strong>
                            <a href="{{ asset('storage/' . $invoice->payment_proof_path) }}"
                                class="btn btn-sm btn-outline-primary" target="_blank">View Proof</a>
                        </p>
                    @endif
                @endif
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> Back to Invoices
            </a>
        </div>
    </div>
@endsection
