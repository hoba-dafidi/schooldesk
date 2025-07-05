<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the plans.
     */
    public function index()
    {
        $plans = Plan::latest()->paginate(10);
        return view('admin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create()
    {
        return view('admin.plans.create');
    }

    /**
     * Store a newly created plan in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'slug'        => 'required|string|unique:plans,slug|max:50',
            'price'       => 'required|numeric|min:0',
            'currency'    => 'required|string|max:10',
            'description' => 'nullable|string|max:255',
            'features'    => 'nullable|array',
            'features.*'  => 'nullable|string|max:100',
            'is_active'   => 'boolean',
        ]);

        $validated['features'] = array_filter($validated['features'] ?? []);
        $validated['is_active'] = $request->boolean('is_active', true);

        Plan::create($validated);

        return redirect()->route('admin.plans.index')->with('success', 'Plan created successfully.');
    }

    /**
     * Display the specified plan.
     */
    public function show(Plan $plan)
    {
        return view('admin.plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified plan in storage.
     */
    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'slug'        => 'required|string|max:50|unique:plans,slug,' . $plan->id,
            'price'       => 'required|numeric|min:0',
            'currency'    => 'required|string|max:10',
            'description' => 'nullable|string|max:255',
            'features'    => 'nullable|array',
            'features.*'  => 'nullable|string|max:100',
            'is_active'   => 'boolean',
        ]);

        $validated['features'] = array_filter($validated['features'] ?? []);
        $validated['is_active'] = $request->boolean('is_active', true);

        $plan->update($validated);

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated successfully.');
    }

    /**
     * Remove the specified plan from storage.
     */
    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted successfully.');
    }

    /**
     * Toggle plan active/inactive status.
     */
    public function toggleStatus(Plan $plan)
    {
        $plan->is_active = !$plan->is_active;
        $plan->save();

        return back()->with('success', 'Plan status updated.');
    }
}
