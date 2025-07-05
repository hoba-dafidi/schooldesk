<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::latest()->get();
        return view('schools.index', compact('schools'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'nullable|string|max:255|unique:schools,subdomain',
            'custom_domain' => 'nullable|string|max:255|unique:schools,custom_domain',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'is_paid_plan' => 'required|boolean',
            'address' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'established_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'about' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $validated['user_id'] = Auth::id();
        School::create($validated);

        return back()->with('success', 'School created successfully.');
    }

    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'nullable|string|max:255|unique:schools,subdomain,' . $school->id,
            'custom_domain' => 'nullable|string|max:255|unique:schools,custom_domain,' . $school->id,
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'is_paid_plan' => 'required|boolean',
            'address' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'established_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'about' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($school->logo) {
                Storage::disk('public')->delete($school->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $school->update($validated);

        return back()->with('success', 'School updated successfully.');
    }

    public function destroy(School $school)
    {
        if ($school->logo) {
            Storage::disk('public')->delete($school->logo);
        }

        $school->delete();

        return back()->with('success', 'School deleted.');
    }
}
