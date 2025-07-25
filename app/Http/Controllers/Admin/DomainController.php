<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subdomain;
use App\Models\CustomDomain;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|developer']);
    }

    /**
     * Display all custom and subdomains.
     */
    public function index()
    {
        $subdomains = Subdomain::with('school')->latest()->get();
        $customDomains = CustomDomain::with('school')->latest()->get();

        return view('admin.schools.domains', compact('subdomains', 'customDomains'));
    }

    /**
     * Delete a subdomain.
     */
    public function destroySubdomain(Subdomain $subdomain)
    {
        $schoolName = $subdomain->school->name ?? 'Unknown';

        $subdomain->delete();

        return back()->with('success', "Subdomain for \"$schoolName\" deleted successfully.");
    }

    /**
     * Delete a custom domain.
     */
    public function destroyCustom(CustomDomain $customDomain)
    {
        $schoolName = $customDomain->school->name ?? 'Unknown';

        $customDomain->delete();

        return back()->with('success', "Custom domain for \"$schoolName\" deleted successfully.");
    }
}
