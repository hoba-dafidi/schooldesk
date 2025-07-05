<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Models\Invoice; // To ensure context if needed
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceItemController extends Controller
{
    /**
     * Store a newly created invoice item.
     * This might be called via API for dynamic invoice building.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        // Ensure authorized to add item to this invoice (e.g., admin or invoice owner)
        // For admin: Auth::user()->can('manage invoices');
        // For client: if (Auth::id() !== $invoice->user_id) { abort(403); }

        $totalPrice = $request->quantity * $request->unit_price;

        $item = InvoiceItem::create([
            'invoice_id' => $request->invoice_id,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'total_price' => $totalPrice,
        ]);

        // Update invoice total amount
        $invoice->total_amount = $invoice->items()->sum('total_price');
        $invoice->save();

        return response()->json(['message' => 'Invoice item added successfully', 'item' => $item, 'invoice_total' => $invoice->total_amount], 201);
    }

    /**
     * Update the specified invoice item.
     */
    public function update(Request $request, InvoiceItem $invoiceItem)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        // Ensure authorized to update this item
        // e.g., if (Auth::id() !== $invoiceItem->invoice->user_id) { abort(403); }

        $oldTotalPrice = $invoiceItem->total_price;
        $newTotalPrice = $request->quantity * $request->unit_price;

        $invoiceItem->update([
            'description' => $request->description,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'total_price' => $newTotalPrice,
        ]);

        // Update invoice total amount
        $invoice = $invoiceItem->invoice;
        $invoice->total_amount = $invoice->total_amount - $oldTotalPrice + $newTotalPrice;
        $invoice->save();

        return response()->json(['message' => 'Invoice item updated successfully', 'item' => $invoiceItem, 'invoice_total' => $invoice->total_amount]);
    }

    /**
     * Remove the specified invoice item.
     */
    public function destroy(InvoiceItem $invoiceItem)
    {
        // Ensure authorized to delete this item
        // e.g., if (Auth::id() !== $invoiceItem->invoice->user_id) { abort(403); }

        $invoice = $invoiceItem->invoice;
        $itemTotalPrice = $invoiceItem->total_price;

        $invoiceItem->delete();

        // Update invoice total amount
        $invoice->total_amount -= $itemTotalPrice;
        $invoice->save();

        return response()->json(['message' => 'Invoice item deleted successfully', 'invoice_total' => $invoice->total_amount]);
    }
}
