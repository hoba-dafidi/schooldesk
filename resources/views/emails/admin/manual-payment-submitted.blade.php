@component('mail::message')
    # Manual Payment Submitted

    User **{{ $invoice->user->name }}** has submitted a manual payment proof for Invoice
    **#{{ $invoice->invoice_number }}**.

    **Amount:** ₦{{ number_format($invoice->total_amount / 100, 2) }}
    **Reference:** {{ $invoice->reference }}

    @component('mail::button', ['url' => route('admin.invoices.show', $invoice)])
        View Invoice
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
