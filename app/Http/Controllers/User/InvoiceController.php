<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\InvoicePaid;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use Yabacon\Paystack;

class InvoiceController extends Controller
{

  public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->invoices()->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $invoices = $query->paginate(10);

        return view('user.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        return view('user.invoices.show', compact('invoice'));
    }

    public function download(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice')); // You can switch to user.invoices.pdf if needed
        $filename = 'Invoice_' . $invoice->invoice_number . '.pdf';

        return $pdf->download($filename);
    }

    public function email(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        Mail::to($invoice->user->email)->queue(new InvoicePaid($invoice));

        return back()->with('success', 'Invoice sent to your email successfully.');
    }

    public function pay(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        if ($invoice->is_paid) {
            return redirect()->route('user.invoices.show', $invoice)->with('info', 'This invoice is already paid.');
        }

        $paystack = new Paystack(config('paystack.secret_key'));

        $user = Auth::user();

        $data = [
            'amount' => (int) $invoice->total_amount, // amount in kobo
            'email' => $user->email,
            'reference' => $invoice->reference,
            'metadata' => [
                'invoice_id' => $invoice->id,
                'user_id' => $user->id,
                'school_id' => $invoice->school_id,
            ],
            'callback_url' => route('paystack.callback'), // route for handling payment response
        ];

        try {
            $response = $paystack->transaction->initialize($data);

            return redirect()->away($response->data->authorization_url);
        } catch (\Yabacon\Paystack\Exception\ApiException $e) {
            return back()->with('error', 'Paystack error: ' . $e->getMessage());
        }
    }



    public function handlePaystackCallback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('dashboard')->with('error', 'Missing payment reference.');
        }

        $invoice = Invoice::where('reference', $reference)->first();

        if (!$invoice) {
            return redirect()->route('dashboard')->with('error', 'Invoice not found.');
        }

        try {
            $paystack = new Paystack(config('paystack.secret_key'));
            $response = $paystack->transaction->verify(['reference' => $reference]);

            if ($response->data->status === 'success') {
                if ($invoice->status !== 'paid') {
                    $invoice->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);

                    // Activate associated subscription
                    if ($invoice->subscription) {
                        $subscription = $invoice->subscription;

                        $subscription->update([
                            'status' => 'active',
                            'starts_at' => now(),
                            'ends_at' => now()->addDays($subscription->plan->duration_days ?? 30),
                        ]);

                        activity()
                          ->causedBy(Auth::user())
                          ->performedOn($subscription)
                          ->withProperties(['plan' => $subscription->plan->name])
                          ->log('Subscription successfully updated');
                    }

                    // Send email to user
                    if ($invoice->user && $invoice->user->email) {
                        Mail::to($invoice->user->email)->queue(new InvoicePaid($invoice));
                    }
                }

                return redirect()->route('user.invoices.show', $invoice)
                    ->with('success', 'Payment successful! Your invoice and subscription are now active.');
            } else {
                return redirect()->route('user.invoices.show', $invoice)
                    ->with('error', 'Payment verification failed. Status: ' . $response->data->status);
            }
        } catch (\Yabacon\Paystack\Exception\ApiException $e) {
            return redirect()->route('user.invoices.show', $invoice)
                ->with('error', 'Paystack error: ' . $e->getMessage());
        }
    }



    protected function authorizeInvoice(Invoice $invoice)
    {
        if ($invoice->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
