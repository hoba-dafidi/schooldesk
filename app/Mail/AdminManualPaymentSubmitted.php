<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminManualPaymentSubmitted extends Mailable
{
    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function build()
    {
        return $this->subject('Manual Payment Proof Submitted')
            ->markdown('emails.admin.manual-payment-submitted');
    }
}
