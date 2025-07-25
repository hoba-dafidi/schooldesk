<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::with(['user', 'plan'])->latest()->get();
        $plans = Plan::active()->get();
        $users = User::whereHas('roles', fn ($q) => $q->where('name', 'user'))->get();

        return view('admin.schools.index', compact('schools', 'plans', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'          => 'nullable|exists:users,id',
            'plan_id'          => 'nullable|exists:plans,id',
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'phone'            => 'nullable|string|max:30',
            'status'           => 'required|in:active,inactive',
            'is_paid_plan'     => 'boolean',
            'address'          => 'nullable|string|max:255',
            'logo'             => 'nullable|image|max:2048',
            'type'             => 'nullable|string|max:100',
            'established_year' => 'nullable|integer|min:1900|max:' . now()->year,
            'about'            => 'nullable|string',
            'city'             => 'nullable|string|max:100',
            'state'            => 'nullable|string|max:100',
            'country'          => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        School::create($validated);

        return redirect()->back()->with('success', 'School created successfully.');
    }

    public function show(School $school)
    {
        $school->load(['client', 'plan', 'subdomain', 'customDomain']);
        return response()->json($school);
    }

    public function update(Request $request, School $school)
    {
      $this->authorize('update', $school); // <- will throw 403 if not allowed
        $validated = $request->validate([
            'user_id'          => 'nullable|exists:users,id',
            'plan_id'          => 'nullable|exists:plans,id',
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'phone'            => 'nullable|string|max:30',
            'status'           => 'required|in:active,inactive',
            'is_paid_plan'     => 'boolean',
            'address'          => 'nullable|string|max:255',
            'logo'             => 'nullable|image|max:2048',
            'type'             => 'nullable|string|max:100',
            'established_year' => 'nullable|integer|min:1900|max:' . now()->year,
            'about'            => 'nullable|string',
            'city'             => 'nullable|string|max:100',
            'state'            => 'nullable|string|max:100',
            'country'          => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('logo')) {
            if ($school->logo) {
                Storage::disk('public')->delete($school->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $school->update($validated);

        return redirect()->back()->with('success', 'School updated successfully.');
    }

    public function destroy(School $school)
    {
        if ($school->logo) {
            Storage::disk('public')->delete($school->logo);
        }

        $school->delete();

        return redirect()->back()->with('success', 'School deleted successfully.');
    }
}
