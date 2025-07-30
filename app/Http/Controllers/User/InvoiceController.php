<?php

namespace App\Http\Controllers\User;

use App\Events\SubscriptionActivated;
use App\Http\Controllers\Controller;
use App\Mail\AdminManualPaymentSubmitted;
use App\Mail\InvoicePaid;
use App\Models\Invoice;
use App\Models\ManualPayment;
use App\Models\PaymentGateway;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
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

        $gateway = $invoice->paymentGateway;

        if (!$gateway || !$gateway->is_active) {
            return back()->with('error', 'Payment gateway is not available.');
        }

        switch ($gateway->slug) {
            case 'paystack':
                return $this->handlePaystack($invoice, $gateway);

            case 'flutterwave':
                return $this->handleFlutterwave($invoice, $gateway);

            case 'manual':
                return $this->processManualPayment($invoice, $gateway);
                // return redirect()->route('user.invoices.show', $invoice)
                //     ->with('info', 'Please contact admin to complete manual payment.');

            default:
                return back()->with('error', 'Unsupported payment method.');
        }
    }



    protected function handlePaystack(Invoice $invoice, PaymentGateway $gateway)
    {
        $paystack = new Paystack($gateway->api_secret);

        $data = [
            'amount' => (int) $invoice->total_amount,
            'email' => $invoice->user->email,
            'reference' => $invoice->reference,
            'metadata' => [
                'invoice_id' => $invoice->id,
                'user_id' => $invoice->user->id,
                'school_id' => $invoice->school_id,
            ],
            'callback_url' => route('paystack.callback'),
        ];

        try {
            $response = $paystack->transaction->initialize($data);
            return redirect()->away($response->data->authorization_url);
        } catch (\Exception $e) {
            return back()->with('error', 'Paystack error: ' . $e->getMessage());
        }
    }

    protected function handleFlutterwave(Invoice $invoice, PaymentGateway $gateway)
    {
        $user = $invoice->user;

        $callbackUrl = URL::route('user.flutterwave.callback'); // Define this named route for callback handling
        $secretKey = config('services.flutterwave.secret_key'); // store in config/services.php

        $response = Http::withToken($secretKey)->post('https://api.flutterwave.com/v3/payments', [
            'tx_ref' => $invoice->invoice_number,
            'amount' => number_format($invoice->total_amount / 100, 2, '.', ''),
            'currency' => 'NGN',
            'redirect_url' => $callbackUrl,
            'customer' => [
                'email' => $user->email,
                'name' => $user->name,
            ],
            'customizations' => [
                'title' => $invoice->title,
                'description' => $invoice->description,
            ],
        ]);

        if ($response->successful()) {
            $paymentLink = $response->json('data.link');
            return redirect()->away($paymentLink);
        }

        return back()->with('error', 'Unable to initialize Flutterwave payment. Please try again.');
    }


    protected function processManualPayment(Invoice $invoice, PaymentGateway $gateway)
    {
        return view('user.invoices.manual-payment', compact('invoice', 'gateway'));
    }

    public function submitManualPayment(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        $validated = $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'note' => 'nullable|string|max:1000',
        ]);

        if ($invoice->status === 'paid') {
            return redirect()->route('user.invoices.show', $invoice)
                ->with('info', 'This invoice is already marked as paid.');
        }

        $proofPath = $request->file('payment_proof')->store('manual-payments', 'public');

        // Update invoice status only
        $invoice->update([
            'status' => 'pending',
            'payment_method' => 'manual',
            'payment_note' => $validated['note'] ?? null,
            'payment_proof_path' => $proofPath,
        ]);

        // 🔥 Create the manual payment record
        ManualPayment::create([
            'user_id'       => $invoice->user_id,
            'invoice_id'    => $invoice->id,
            'proof_path'    => $proofPath,
            'status'        => 'pending', // or 'submitted'
            'reference'     => strtoupper(Str::random(10)),
            'note' => $validated['note'] ?? null,
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($invoice)
            ->withProperties(['invoice' => $invoice->invoice_number])
            ->log('Manual payment proof submitted');

        if (config('settings.notify_admin_on_manual_payment')) {
            Mail::to(config('settings.admin_email', 'admin@example.com'))
                ->queue(new AdminManualPaymentSubmitted($invoice));
        }

        return redirect()->route('user.invoices.show', $invoice)
            ->with('success', 'Your payment proof has been submitted. We will review and activate your subscription shortly.');
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

                        event(new SubscriptionActivated($subscription));

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
