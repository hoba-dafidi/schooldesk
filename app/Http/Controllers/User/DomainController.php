<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomDomain;
use App\Models\Subdomain;
use Illuminate\Support\Facades\Auth;

class DomainController extends Controller
{
    public function index()
    {
        $subdomains = Subdomain::whereHas('school', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();


        $customDomains = CustomDomain::whereHas('school', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();

        return view('user.schools.domains', compact('subdomains', 'customDomains'));
    }

    public function create()
    {
        return view('user.subdomains.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_id'    => 'required|exists:schools,id',
            'link'         => 'required|string|unique:subdomains,link',
            'is_active'    => 'required|boolean',
            'db_host'      => 'nullable|string',
            'db_username'  => 'nullable|string',
            'db_password'  => 'nullable|string',
            'db_table'     => 'nullable|string',
        ]);

        // Verify ownership
        if ($request->user()->id !== optional($request->user()->schools()->find($validated['school_id']))->user_id) {
            abort(403);
        }

        Subdomain::create($validated);

        return redirect()->route('user.subdomains.index')->with('success', 'Subdomain created successfully.');
    }

    public function edit(Subdomain $subdomain)
    {
        $this->authorizeAccess($subdomain);
        return view('user.subdomains.edit', compact('subdomain'));
    }

    public function update(Request $request, Subdomain $subdomain)
    {
        $this->authorizeAccess($subdomain);

        $validated = $request->validate([
            'school_id'    => 'required|exists:schools,id',
            'link'         => 'required|string|unique:subdomains,link,' . $subdomain->id,
            'is_active'    => 'required|boolean',
            'db_host'      => 'nullable|string',
            'db_username'  => 'nullable|string',
            'db_password'  => 'nullable|string',
            'db_table'     => 'nullable|string',
        ]);

        $subdomain->update($validated);

        return redirect()->route('user.subdomains.index')->with('success', 'Subdomain updated successfully.');
    }

    public function destroy(Subdomain $subdomain)
    {
        $this->authorizeAccess($subdomain);
        $subdomain->delete();
        return redirect()->route('user.subdomains.index')->with('success', 'Subdomain deleted.');
    }

    private function authorizeAccess(Subdomain $subdomain)
    {
        if ($subdomain->school->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
