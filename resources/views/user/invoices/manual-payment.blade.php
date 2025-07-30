@extends('layouts/contentNavbarLayout')

@section('title', 'Manual Payment - Invoice #' . $invoice->invoice_number)

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mx-auto" style="max-width: 720px;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class='bx bx-bank'></i> Manual Payment Instructions</h5>
            </div>
            <div class="card-body">
                <p>Please transfer the invoice amount to the account below and upload your proof of payment.</p>

                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item"><strong>Bank Name:</strong> Example Bank</li>
                    <li class="list-group-item"><strong>Account Name:</strong> RCCG New Hope Parish</li>
                    <li class="list-group-item"><strong>Account Number:</strong> 1234567890</li>
                    <li class="list-group-item"><strong>Amount:</strong>
                        ₦{{ number_format($invoice->total_amount / 100, 2) }}</li>
                    <li class="list-group-item"><strong>Reference:</strong> {{ $invoice->reference }}</li>
                </ul>

                <form action="{{ route('user.invoices.manual.submit', $invoice->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="payment_proof" class="form-label">Upload Proof of Payment (Image or PDF)</label>
                        <input type="file" name="payment_proof" id="payment_proof"
                            class="form-control @error('payment_proof') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf"
                            required>
                        @error('payment_proof')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">Optional Note</label>
                        <textarea name="note" id="note" class="form-control" rows="3" placeholder="Add any additional info...">{{ old('note') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('user.invoices.show', $invoice) }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-upload"></i> Submit Payment Proof
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
