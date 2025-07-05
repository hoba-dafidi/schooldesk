<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the invoices for the authenticated user.
     */
    public function index()
    {
        $invoices = Auth::user()->invoices()->orderBy('created_at', 'desc')->get();
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        // Ensure the authenticated user owns this invoice
        if (Auth::id() !== $invoice->user_id) {
            abort(403);
        }
        $invoice->load('items'); // Load associated invoice items
        return view('invoices.show', compact('invoice'));
    }

    // --- Admin-specific methods (example for generating invoices) ---
    /**
     * Generate a new invoice (Admin only).
     */
    public function generate(Request $request)
    {
        // Validation for admin-generated invoice
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:0.01',
            'description' => 'required|string', // For the first invoice item
            // ... more validation for invoice items if needed
        ]);

        $invoice = Invoice::create([
            'user_id' => $request->user_id,
            'invoice_number' => 'INV-' . time(), // Simple unique number
            'total_amount' => $request->total_amount,
            'currency' => 'USD', // Default currency
            'status' => 'pending',
            'due_date' => now()->addDays(7),
        ]);

        // Add a primary invoice item
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => $request->description,
            'quantity' => 1,
            'unit_price' => $request->total_amount,
            'total_price' => $request->total_amount,
        ]);

        return redirect()->route('invoices.show', $invoice->id) // Redirect to invoice view
                         ->with('success', 'Invoice generated successfully.');
    }

    /**
     * Mark an invoice as paid (Admin only).
     */
    public function markAsPaid(Invoice $invoice)
    {
        $invoice->update(['status' => 'paid', 'paid_at' => now()]);
        return back()->with('success', 'Invoice marked as paid.');
    }
}
