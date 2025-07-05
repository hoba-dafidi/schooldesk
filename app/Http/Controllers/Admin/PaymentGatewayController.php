<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    public function index()
    {
        $gateways = PaymentGateway::latest()->get();
        return view('admin.gateways.index', compact('gateways'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:payment_gateways,slug',
            'currency' => 'nullable|string|max:10',
            'driver_class' => 'nullable|string|max:255',
            'callback_url' => 'nullable|url',
            'webhook_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
            'sandbox_mode' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sandbox_mode'] = $request->has('sandbox_mode');

        PaymentGateway::create($validated);

        return redirect()->route('admin.gateways.index')->with('success', 'Gateway created successfully.');
    }

    public function update(Request $request, PaymentGateway $gateway)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:payment_gateways,slug,' . $gateway->id,
            'currency' => 'nullable|string|max:10',
            'driver_class' => 'nullable|string|max:255',
            'callback_url' => 'nullable|url',
            'webhook_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
            'sandbox_mode' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sandbox_mode'] = $request->has('sandbox_mode');

        $gateway->update($validated);

        return redirect()->route('admin.gateways.index')->with('success', 'Gateway updated successfully.');
    }

    public function toggleStatus(PaymentGateway $gateway)
    {
        $gateway->is_active = !$gateway->is_active;
        $gateway->save();

        return redirect()->route('admin.gateways.index')->with('success', 'Gateway status updated.');
    }

    public function destroy(PaymentGateway $gateway)
    {
        $gateway->delete();

        return redirect()->route('admin.gateways.index')->with('success', 'Gateway deleted.');
    }
}
