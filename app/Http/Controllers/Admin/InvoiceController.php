<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Notifications\InvoiceNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    /**
     * Display a list of invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['user', 'subscription'])
            ->latest();

        if ($request->filled('user')) {
            $query->whereHas('user', fn($q) =>
                $q->where('email', 'like', '%' . $request->user . '%'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->paginate(20);

        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Show a single invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('items', 'user', 'subscription');

        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Export invoices to CSV or PDF.
     */
    public function export(Request $request)
    {
        $query = Invoice::with(['user', 'subscription']);

        if ($request->filled('user')) {
            $query->whereHas('user', fn($q) =>
                $q->where('email', 'like', '%' . $request->user . '%'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->get();

        if ($request->get('format') === 'pdf') {
            $pdf = Pdf::loadView('admin.invoices.export_pdf', compact('invoices'));
            return $pdf->download('invoices_' . now()->format('Ymd_His') . '.pdf');
        }

        $filename = 'invoices_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate",
            "Expires" => "0",
        ];

        $columns = ['Invoice #', 'User', 'Email', 'Amount', 'Currency', 'Status', 'Due Date', 'Paid At'];

        $callback = function () use ($invoices, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->invoice_number,
                    $invoice->user->name,
                    $invoice->user->email,
                    $invoice->total_amount / 100,
                    $invoice->currency,
                    $invoice->status,
                    optional($invoice->due_date)->format('Y-m-d'),
                    optional($invoice->paid_at)->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Resend invoice to the user via email.
     */
    public function resendNotification(Invoice $invoice)
    {
        $invoice->user->notify(new InvoiceNotification($invoice->load('items', 'subscription')));

        return back()->with('success', 'Invoice sent successfully!');
    }

    /**
     * Mark the invoice as paid manually.
     */
    public function markAsPaid(Invoice $invoice)
    {
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Invoice marked as paid.');
    }

    public function download(Invoice $invoice)
    {
        $pdf = PDF::loadView('admin.invoices.pdf', compact('invoice'));
        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }

    public function resend(Invoice $invoice)
    {
        $invoice->user->notify(new InvoiceNotification($invoice->load('items', 'subscription')));
        return back()->with('success', 'Invoice resent to user.');
    }

}
