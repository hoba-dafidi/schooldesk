<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class SchoolController extends Controller
{
    /**
     * Display a listing of the user's schools.
     */
    public function index()
    {
        $schools = Auth::user()->schools;
        $plans = Plan::active()->get(); // or filter active plans
        $gateways = PaymentGateway::where('is_active', true)->get();

        return view('user.schools.index', compact('schools', 'plans', 'gateways'));
    }

    /**
     * Show form for creating a school.
     */
    public function create()
    {
        return view('schools.create');
    }

    /**
     * Store a new school.
     */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:schools,name',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048',
            'type' => 'nullable|string|max:50',
            'established_year' => 'nullable|integer|digits:4|min:1900|max:' . date('Y'),
            'about' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        $logoPath = $request->hasFile('logo')
            ? $request->file('logo')->store('school_logos', 'public')
            : null;

        $school = Auth::user()->schools()->create([
            'name' => $request->name,
            'status' => 'pending',
            'is_paid_plan' => false,
            'address' => $request->address,
            'email' => $request->email,
            'phone' => $request->phone,
            'logo' => $logoPath,
            'type' => $request->type,
            'established_year' => $request->established_year,
            'about' => $request->about,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
        ]);

        activity()
                          ->causedBy(Auth::user())
                          ->performedOn($school)
                          ->withProperties(['plan' => $school->name])
                          ->log('School successfully created');

        // Assign the FREE plan automatically
        $freePlan = Plan::where('slug', 'free')->first();

        if ($freePlan) {
            Subscription::create([
                'user_id' => Auth::id(),
                'school_id' => $school->id,
                'plan_id' => $freePlan->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addDays($freePlan->duration_days),
            ]);

            Log::info("Free subscription assigned to school: {$school->name}");
        } else {
            Log::warning("No free plan found. School '{$school->name}' created without default subscription.");
        }

        // dispatch(new \App\Jobs\ProvisionSchoolPortal($school));

        Log::info("School created: {$school->name}, User: " . Auth::id());

        return redirect()->route('user.schools.index')->with('success', 'School created. Provisioning started.');
    }


    /**
     * Show form to edit a school (optional).
     */
    public function edit(School $school)
    {
        $this->authorizeOwnership($school);
        return view('user.schools.edit', compact('school'));
    }

    /**
     * Update a school's details.
     */
    public function update(Request $request, School $school)
    {
        $this->authorizeOwnership($school);

        $request->validate([
            'name' => 'required|string|max:255|unique:schools,name,' . $school->id,
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048',
            'type' => 'nullable|string|max:50',
            'established_year' => 'nullable|integer|digits:4|min:1900|max:' . date('Y'),
            'about' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('logo')) {
            // Remove old logo
            if ($school->logo && Storage::disk('public')->exists($school->logo)) {
                Storage::disk('public')->delete($school->logo);
            }
            $school->logo = $request->file('logo')->store('school_logos', 'public');
        }

        $school->update($request->except(['logo']));

        Log::info("School updated: {$school->name}, User: " . Auth::id());

        return back()->with('success', 'School updated successfully.');
    }

    /**
     * Delete a school.
     */
    public function destroy(School $school)
    {
        $this->authorizeOwnership($school);

        // Delete logo
        if ($school->logo && Storage::disk('public')->exists($school->logo)) {
            Storage::disk('public')->delete($school->logo);
        }

        $school->delete();

        Log::warning("School deleted: {$school->name}, by User: " . Auth::id());

        return redirect()->route('user.schools.index')->with('success', 'School deleted.');
    }

    /**
     * Custom domain logic.
     */
    public function updateCustomDomain(Request $request, School $school)
    {
        $this->authorizeOwnership($school);

        $request->validate([
            'custom_domain' => 'required|string|max:255|unique:schools,custom_domain,' . $school->id,
        ]);

        $paymentSuccessful = true;

        if ($paymentSuccessful) {
            $school->custom_domain = $request->custom_domain;
            $school->is_paid_plan = true;
            $school->save();

            // dispatch(new \App\Jobs\ProvisionCustomDomain($school));

            Log::info("Custom domain updated: {$school->custom_domain}");

            return back()->with('success', 'Custom domain configured successfully.');
        }

        return back()->with('error', 'Payment failed. Please try again.');
    }

    // public function subscribe(Request $request)
    // {
    //     $request->validate([
    //         'school_id' => 'required|exists:schools,id',
    //         'plan_id' => 'required|exists:plans,id',
    //     ]);

    //     $school = School::findOrFail($request->school_id);
    //     $school->plan_id = $request->plan_id;
    //     $school->is_paid_plan = true;
    //     $school->save();

    //     return redirect()->back()->with('success', 'School subscribed to plan successfully.');
    // }

    public function getCurrentSubscription(School $school)
    {
        $subscription = $school->subscriptions()
            ->active()
            ->with('plan')
            ->first();

            // dd([
            //   'school_id' => $school->id,
            //   'subscriptions_query' => $school->subscriptions()->toSql(),
            //   'bindings' => $school->subscriptions()->getBindings(),
            //   'count' => $school->subscriptions()->count()
            // ]);

        if (!$subscription) {
            return response()->json(['subscription' => null]);
        }

        return response()->json([
            'subscription' => [
                'plan_id' => $subscription->plan->id,
                'plan_name' => $subscription->plan->name,
                'plan_slug' => $subscription->plan->slug,
                'plan_price' => $subscription->plan->formatted_price,
                'status' => $subscription->status,
                'starts_at' => $subscription->starts_at?->toDateString(),
                'ends_at' => $subscription->ends_at?->toDateString(),
            ]
        ]);
    }


    /**
     * Ensure user owns the school.
     */
    private function authorizeOwnership(School $school)
    {
        if (Auth::id() !== $school->user_id) {
            abort(403, 'Unauthorized');
        }
    }
}
