<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomDomain;
use Illuminate\Http\Request;

class CustomDomainController extends Controller
{
    public function index()
    {
        $domains = CustomDomain::all();
        return view('custom-domains.index', compact('domains'));
    }

    public function create()
    {
        return view('custom-domains.create');
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

        CustomDomain::create($validated);

        return redirect()->route('custom-domains.index')->with('success', 'Custom domain created.');
    }

    public function edit(CustomDomain $customDomain)
    {
        return view('custom-domains.edit', compact('customDomain'));
    }

    public function update(Request $request, CustomDomain $customDomain)
    {
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

        return redirect()->route('custom-domains.index')->with('success', 'Custom domain updated.');
    }

    public function destroy(CustomDomain $customDomain)
    {
        $customDomain->delete();
        return redirect()->route('custom-domains.index')->with('success', 'Custom domain deleted.');
    }
}
