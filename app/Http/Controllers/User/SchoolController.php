<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // For logging important events
use Illuminate\Support\Str; // For generating subdomains

class SchoolController extends Controller
{
    /**
     * Display a listing of the client's schools.
     */
    public function index()
    {
        $schools = Auth::user()->schools; // Assuming a 'schools' relationship on the User model
        return view('user.schools.index', compact('schools'));
    }

    /**
     * Show the form for creating a new school.
     */
    public function create()
    {
        return view('schools.create');
    }

    /**
     * Store a newly created school in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:schools,name',
            // Add validation rules for new fields here
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048', // Example for image upload
            'type' => 'nullable|string|max:50',
            'established_year' => 'nullable|integer|digits:4|min:1900|max:' . date('Y'),
            'about' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        // Handle logo upload if present
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('school_logos', 'public'); // 'public' disk
        }

        // Generate a unique subdomain from the school name
        $baseSubdomain = Str::slug($request->name);
        $subdomain = $baseSubdomain;
        $counter = 1;


        $school = Auth::user()->schools()->create([
            'name' => $request->name,
            'status' => 'pending', // Initially pending until provisioned
            'is_paid_plan' => false, // Default to free plan
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

        // --- Important: Trigger the provisioning process here ---
        // This should be an asynchronous job as it can take time.
        // For example: dispatch a job to handle server setup.
        // You would need to set up queues for this (e.g., Redis, database queue).
        // dispatch(new \App\Jobs\ProvisionSchoolPortal($school)); // Example Job

        Log::info("School creation initiated for: {$school->name} by client: " . Auth::id());

        return redirect()->route('user.schools.index')
                         ->with('success', 'School account created! Provisioning is in progress.');
    }

    /**
     * Handle custom domain request.
     */
    public function updateCustomDomain(Request $request, School $school)
    {
        // Ensure the authenticated user owns this school
        if (Auth::id() !== $school->user_id) { // Changed to user_id
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'custom_domain' => 'required|string|max:255|unique:schools,custom_domain,' . $school->id,
        ]);

        // Integrate with a payment gateway here (e.g., Stripe, Paystack)
        // Check if payment is successful.
        $paymentSuccessful = true; // Replace with actual payment logic

        if ($paymentSuccessful) {
            $school->custom_domain = $request->custom_domain;
            $school->is_paid_plan = true;
            $school->save();

            // Trigger custom domain provisioning (e.g., update DNS, server config)
            // dispatch(new \App\Jobs\ProvisionCustomDomain($school)); // Example Job

            Log::info("Custom domain requested for school: {$school->name} to {$school->custom_domain}");
            return back()->with('success', 'Custom domain requested. It will be active soon!');
        } else {
            return back()->with('error', 'Payment failed. Please try again.');
        }
    }
}
