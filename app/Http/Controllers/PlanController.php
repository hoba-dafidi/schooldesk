<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the plans.
     * Accessible by anyone, or only authenticated clients/admins.
     */
    public function index()
    {
        $plans = Plan::all();
        return view('plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new plan.
     * Only accessible by admins.
     */
    public function create()
    {
        return view('plans.create');
    }

    /**
     * Store a newly created plan in storage.
     * Only accessible by admins.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:plans,name',
            'slug' => 'required|string|max:255|unique:plans,slug',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'description' => 'nullable|string',
            'features' => 'nullable|array', // Expecting an array of strings
            'is_active' => 'boolean',
        ]);

        Plan::create($request->all());

        return redirect()->route('plans.index')
                         ->with('success', 'Plan created successfully.');
    }

    /**
     * Display the specified plan.
     * Accessible by anyone.
     */
    public function show(Plan $plan)
    {
        return view('plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified plan.
     * Only accessible by admins.
     */
    public function edit(Plan $plan)
    {
        return view('plans.edit', compact('plan'));
    }

    /**
     * Update the specified plan in storage.
     * Only accessible by admins.
     */
    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:plans,name,' . $plan->id,
            'slug' => 'required|string|max:255|unique:plans,slug,' . $plan->id,
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $plan->update($request->all());

        return redirect()->route('plans.index')
                         ->with('success', 'Plan updated successfully.');
    }

    /**
     * Remove the specified plan from storage.
     * Only accessible by admins.
     */
    public function destroy(Plan $plan)
    {
        $plan->delete();

        return redirect()->route('plans.index')
                         ->with('success', 'Plan deleted successfully.');
    }
}
