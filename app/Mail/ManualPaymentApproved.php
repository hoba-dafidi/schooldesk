<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ManualPaymentApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Manual Payment Has Been Approved')
                    ->markdown('emails.manual-payment-approved');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Manual Payment Approved',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.manual-payment-approved',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
