<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subdomain;
use Illuminate\Http\Request;

class SubdomainController extends Controller
{
    public function index()
    {
        $subdomains = Subdomain::all();
        return view('subdomains.index', compact('subdomains'));
    }

    public function create()
    {
        return view('subdomains.create');
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

        Subdomain::create($validated);

        return redirect()->route('subdomains.index')->with('success', 'Subdomain created successfully.');
    }

    public function edit(Subdomain $subdomain)
    {
        return view('subdomains.edit', compact('subdomain'));
    }

    public function update(Request $request, Subdomain $subdomain)
    {
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

        return redirect()->route('subdomains.index')->with('success', 'Subdomain updated successfully.');
    }

    public function destroy(Subdomain $subdomain)
    {
        $subdomain->delete();
        return redirect()->route('subdomains.index')->with('success', 'Subdomain deleted.');
    }
}
