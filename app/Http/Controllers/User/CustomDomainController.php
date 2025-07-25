<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomDomain;
use Illuminate\Support\Facades\Auth;


class CustomDomainController extends Controller
{
    public function index()
    {
        $domains = CustomDomain::whereHas('school', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();

        return view('user.custom-domains.index', compact('domains'));
    }

    public function create()
    {
        return view('user.custom-domains.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_id'    => 'required|exists:schools,id',
            'link'         => 'required|string|unique:custom_domains,link',
            'is_active'    => 'required|boolean',
            'db_host'      => 'nullable|string',
            'db_username'  => 'nullable|string',
            'db_password'  => 'nullable|string',
            'db_table'     => 'nullable|string',
        ]);

        if ($request->user()->id !== optional($request->user()->schools()->find($validated['school_id']))->user_id) {
            abort(403);
        }

        $customDomain = CustomDomain::create($validated);

        activity()
              ->causedBy(Auth::user())
              ->performedOn($customDomain)
              ->withProperties(['plan' => $customDomain->link])
              ->log('Custom Domain Created');

        return redirect()->route('user.custom-domains.index')->with('success', 'Custom domain created.');
    }

    public function edit(CustomDomain $customDomain)
    {
        $this->authorizeAccess($customDomain);
        return view('user.custom-domains.edit', compact('customDomain'));
    }

    public function update(Request $request, CustomDomain $customDomain)
    {
        $this->authorizeAccess($customDomain);

        $validated = $request->validate([
            'school_id'    => 'required|exists:schools,id',
            'link'         => 'required|string|unique:custom_domains,link,' . $customDomain->id,
            'is_active'    => 'required|boolean',
            'db_host'      => 'nullable|string',
            'db_username'  => 'nullable|string',
            'db_password'  => 'nullable|string',
            'db_table'     => 'nullable|string',
        ]);

        $customDomain->update($validated);

        activity()
              ->causedBy(Auth::user())
              ->performedOn($customDomain)
              ->withProperties(['plan' => $customDomain->link])
              ->log('Custom Domain Updated');

        return redirect()->route('user.custom-domains.index')->with('success', 'Custom domain updated.');
    }

    public function destroy(CustomDomain $customDomain)
    {
        $this->authorizeAccess($customDomain);
        $customDomain->delete();

        activity()
              ->causedBy(Auth::user())
              ->performedOn($customDomain)
              ->withProperties(['plan' => $customDomain->link])
              ->log('Custom Domain Deleted');
        return redirect()->route('user.custom-domains.index')->with('success', 'Custom domain deleted.');
    }

    private function authorizeAccess(CustomDomain $domain)
    {
        if ($domain->school->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
