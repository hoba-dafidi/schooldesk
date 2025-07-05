<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Invoice $invoice;

    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice->load('items', 'user', 'subscription');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $invoice = $this->invoice;
        $user = $invoice->user;

        $mail = (new MailMessage)
            ->subject('Invoice #' . $invoice->invoice_number)
            ->greeting('Hello ' . $user->name . ',')
            ->line('Here is your invoice for the plan: **' . optional($invoice->subscription)->plan->name . '**')
            ->line('Invoice Number: **' . $invoice->invoice_number . '**')
            ->line('Total Amount: **' . $invoice->formatted_total . '**')
            ->line('Due Date: ' . optional($invoice->due_date)->format('F j, Y'))
            ->action('View Invoice', route('admin.invoices.show', $invoice->id))
            ->line('Thank you for your business!');

        // Optional: attach PDF invoice
        $pdf = Pdf::loadView('admin.invoices.export_pdf', ['invoices' => collect([$invoice])]);
        $mail->attachData($pdf->output(), 'invoice_' . $invoice->invoice_number . '.pdf', [
            'mime' => 'application/pdf',
        ]);

        return $mail;
    }

    /**
     * Array representation (for database channel if needed).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'amount' => $this->invoice->total_amount,
            'currency' => $this->invoice->currency,
            'status' => $this->invoice->status,
        ];
    }
}
