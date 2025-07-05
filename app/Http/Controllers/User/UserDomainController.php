<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CustomDomain;
use App\Models\School;
use App\Models\Subdomain;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserDomainController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $schools = $user->schools()->with(['subdomain', 'customDomain'])->get();

        // $subdomains = Subdomain::whereHas('school', function ($query) {
        //     $query->where('user_id', Auth::id());
        // })->get();


        // $customDomains = CustomDomain::whereHas('school', function ($query) {
        //     $query->where('user_id', Auth::id());
        // })->get();
        return view('user.schools.domains', compact('schools'));
    }

    public function storeSubdomain(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_id'   => 'required|exists:schools,id',
            'link'        => 'required|string|unique:subdomains,link',
            'db_host'     => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'required|string',
            'db_table'    => 'required|string',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $school = Auth::user()->schools()->findOrFail($request->school_id);

             // Check if the school already has a subdomain
            if ($school->subdomain) {
                return back()->withErrors(['school_id' => 'This school already has a subdomain.'])->withInput();
            }

            $school->subdomain()->create([
                'link'        => $request->link,
                'db_host'     => $request->db_host,
                'db_username' => $request->db_username,
                'db_password' => $request->db_password,
                'db_table'    => $request->db_table,
                'is_active'   => $request->boolean('is_active', true),
            ]);

            return back()->with('success', 'Subdomain created successfully.');
        } catch (QueryException $e) {
            // Handle unique constraint violation (e.g., duplicate domain)
            if ($e->getCode() === '23000') {
                return back()->withErrors(['link' => 'This subdomain already exists.'])->withInput();
            }

            throw $e; // Re-throw if not a duplicate error
        }
    }

    public function storeCustomDomain(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_id'   => 'required|exists:schools,id',
            'link'        => 'required|string|unique:custom_domains,link',
            'db_host'     => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'required|string',
            'db_table'    => 'required|string',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $school = Auth::user()->schools()->findOrFail($request->school_id);

             // Check if the school already has a subdomain
            if ($school->customDomain) {
                return back()->withErrors(['school_id' => 'This school already has a domain.'])->withInput();
            }

            $school->customDomain()->create([
                'link'        => $request->link,
                'db_host'     => $request->db_host,
                'db_username' => $request->db_username,
                'db_password' => $request->db_password,
                'db_table'    => $request->db_table,
                'is_active'   => $request->boolean('is_active', true),
            ]);

            return back()->with('success', 'Custom domain created successfully.');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->withErrors(['link' => 'This custom domain already exists.'])->withInput();
            }

            throw $e;
        }
    }

    public function updateCustomDomain(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'link'        => 'required|string|unique:custom_domains,link,' . $id,
            'db_host'     => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'required|string',
            'db_table'    => 'required|string',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $customDomain = CustomDomain::whereHas('school', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($id);

            $customDomain->update([
                'link'        => $request->link,
                'db_host'     => $request->db_host,
                'db_username' => $request->db_username,
                'db_password' => $request->db_password,
                'db_table'    => $request->db_table,
                'is_active'   => $request->boolean('is_active', true),
            ]);

            return back()->with('success', 'Custom domain updated successfully.');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->withErrors(['link' => 'This custom domain already exists.'])->withInput();
            }

            throw $e;
        }
    }

    public function updateSubdomain(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'school_id'   => 'required|exists:schools,id',
            'link'        => 'required|string|unique:subdomains,link,' . $id,
            'db_host'     => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'required|string',
            'db_table'    => 'required|string',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $subdomain = Subdomain::whereHas('school', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($id);

            $subdomain->update([
                'school_id'   => $request->school_id,
                'link'        => $request->link,
                'db_host'     => $request->db_host,
                'db_username' => $request->db_username,
                'db_password' => $request->db_password,
                'db_table'    => $request->db_table,
                'is_active'   => $request->boolean('is_active', true),
            ]);

            return back()->with('success', 'Subdomain updated successfully.');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->withErrors(['link' => 'This subdomain already exists.'])->withInput();
            }

            throw $e;
        }
    }

    public function destroySubdomain($id)
    {
        $subdomain = Subdomain::whereHas('school', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        $subdomain->delete();

        return back()->with('success', 'Subdomain deleted.');
    }

    public function destroyCustomDomain($id)
    {
        $customDomain = CustomDomain::whereHas('school', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        $customDomain->delete();

        return back()->with('success', 'Custom domain deleted.');
    }
}
