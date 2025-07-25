@extends('layouts/contentNavbarLayout')

@section('title', 'Manage Subdomains & Custom Domains')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Manage Subdomains & Custom Domains</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input:
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @php
            $hasSubdomain = $schools->every(fn($school) => $school->subdomain);
            $hasCustomDomain = $schools->every(fn($school) => $school->customDomain);
        @endphp

        <!-- Subdomain Card -->
        <!-- Subdomain Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <h5>Subdomains</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#subdomainModal"
                    onclick="resetSubdomainForm()" {{ $hasSubdomain ? 'disabled' : '' }}>
                    <i class="bx bx-plus"></i> Add Subdomain
                </button>
            </div>
            <div class="table-responsive">
                <table class="table" id="subdomainsTable">
                    <thead>
                        <tr>
                            <th>School</th>
                            <th>Link</th>
                            <th>DB Host</th>
                            <th>DB Username</th>
                            <th>Table</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schools as $school)
                            @php $domain = $school->subdomain; @endphp
                            @if ($domain)
                                <tr>
                                    <td>{{ $domain->school->name }}</td>
                                    <td><a href="http://{{ $domain->link }}" target="_blank">{{ $domain->link }}</a></td>
                                    <td>{{ $domain->db_host }}</td>
                                    <td>{{ $domain->db_username }}</td>
                                    <td>{{ $domain->db_table }}</td>
                                    <td>
                                        <span class="badge bg-label-{{ $domain->is_active ? 'success' : 'secondary' }}">
                                            {{ $domain->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="d-flex gap-1">
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                            data-bs-target="#subdomainModal"
                                            onclick='editSubdomain(@json($domain))'>
                                            Edit
                                        </button>
                                        <form method="POST"
                                            action="{{ route('user.domains.subdomain.destroy', $domain->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure to delete this subdomain?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($schools->every(fn($school) => !$school->subdomain))
                <div class="text-center text-muted py-3">No subdomains found for any school.</div>
            @endif
        </div>


        <!-- Custom Domain Card -->
        <!-- Custom Domain Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Custom Domains</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#customDomainModal"
                    onclick="resetCustomDomainForm()" {{ $hasCustomDomain ? 'disabled' : '' }}>
                    <i class="bx bx-plus"></i> Add Custom Domain
                </button>
            </div>
            <div class="table-responsive">
                <table class="table" id="customDomainsTable">
                    <thead>
                        <tr>
                            <th>School</th>
                            <th>Link</th>
                            <th>DB Host</th>
                            <th>DB Username</th>
                            <th>Table</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schools as $school)
                            @php $domain = $school->customDomain; @endphp
                            @if ($domain)
                                <tr>
                                    <td>{{ $domain->school->name }}</td>
                                    <td><a href="http://{{ $domain->link }}" target="_blank">{{ $domain->link }}</a></td>
                                    <td>{{ $domain->db_host }}</td>
                                    <td>{{ $domain->db_username }}</td>
                                    <td>{{ $domain->db_table }}</td>
                                    <td>
                                        <span class="badge bg-label-{{ $domain->is_active ? 'success' : 'secondary' }}">
                                            {{ $domain->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="d-flex gap-1">
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                            data-bs-target="#customDomainModal"
                                            onclick='editCustomDomain(@json($domain))'>
                                            Edit
                                        </button>
                                        <form method="POST"
                                            action="{{ route('user.domains.custom.destroy', $domain->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure to delete this custom domain?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($schools->every(fn($school) => !$school->customDomain))
                <div class="text-center text-muted py-3">No custom domains found for any school.</div>
            @endif
        </div>


        <!-- Subdomain Modal -->
        <div class="modal fade" id="subdomainModal" tabindex="-1" aria-labelledby="subdomainModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form id="subdomainForm" method="POST" action="{{ route('user.domains.subdomain.store') }}">
                    @csrf
                    <input type="hidden" name="id" id="subdomain_id">

                    <div class="modal-content shadow-lg border-0 rounded-3">
                        <div class="modal-header bg-primary text-white align-items-center">
                            <h5 class="modal-title text-white mb-3" id="subdomainModalLabel">Create / Edit Subdomain</h5>
                            <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal"
                                aria-label="Close" style="margin-right: 15px;"></button>

                        </div>

                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="subdomain_school_id" class="form-label">School</label>
                                    <select name="school_id" id="subdomain_school_id" class="form-select" required>
                                        <option value="">Select School</option>
                                        @foreach ($schools as $school)
                                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="link" class="form-label">Subdomain Link</label>
                                    <input type="text" name="link" id="link" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="db_host" class="form-label">DB Host</label>
                                    <input type="text" name="db_host" id="db_host" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="db_username" class="form-label">DB Username</label>
                                    <input type="text" name="db_username" id="db_username" class="form-control"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label for="db_password" class="form-label">DB Password</label>
                                    <input type="password" name="db_password" id="db_password" class="form-control"
                                        autocomplete="new-password" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="db_table" class="form-label">DB Table</label>
                                    <input type="text" name="db_table" id="db_table" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="is_active" class="form-label">Status</label>
                                    <select name="is_active" id="is_active" class="form-select" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light p-0">
                            <button type="button" class="btn btn-outline-secondary m-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary m-2">Save Subdomain</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <!-- Custom Domain Modal -->
        <div class="modal fade" id="customDomainModal" tabindex="-1" aria-labelledby="customDomainModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form id="customDomainForm" method="POST" action="{{ route('user.domains.custom.store') }}">
                    @csrf
                    <input type="hidden" name="id" id="custom_domain_id">

                    <div class="modal-content shadow-lg border-0 rounded-3">
                        <div class="modal-header bg-primary text-white align-items-center">
                            <h5 class="modal-title text-white mb-3" id="customDomainModalLabel">Create Custom Domain</h5>
                            <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal"
                                aria-label="Close" style="margin-right: 15px;"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="custom_school_id" class="form-label">School</label>
                                    <select name="school_id" id="custom_school_id" class="form-select" required>
                                        <option value="">Select School</option>
                                        @foreach ($schools as $school)
                                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="custom_link" class="form-label">Link</label>
                                    <input type="text" name="link" id="custom_link" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="custom_db_host" class="form-label">DB Host</label>
                                    <input type="text" name="db_host" id="custom_db_host" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label for="custom_db_username" class="form-label">DB Username</label>
                                    <input type="text" name="db_username" id="custom_db_username"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="custom_db_password" class="form-label">DB Password</label>
                                    <input type="password" name="db_password" id="custom_db_password"
                                        class="form-control" autocomplete="new-password" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="custom_db_table" class="form-label">DB Table</label>
                                    <input type="text" name="db_table" id="custom_db_table" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label for="custom_is_active" class="form-label">Status</label>
                                    <select name="is_active" id="custom_is_active" class="form-select" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light p-0">
                            <button type="button" class="btn btn-outline-secondary m-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary m-2">Save Custom Domain</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


    </div>
@endsection

@push('scripts')
    <script>
        // Reset Subdomain form to empty for create
        function resetSubdomainForm() {
            const form = document.getElementById('subdomainForm');
            form.reset();
            form.action = "{{ route('user.domains.subdomain.store') }}";
            document.getElementById('subdomain_id').value = '';
            document.getElementById('subdomainModalLabel').textContent = 'Create Subdomain';

        }

        function editSubdomain(domain) {
            const form = document.getElementById('subdomainForm');
            form.action = `/user/domains/subdomain/${domain.id}`;
            if (!form.querySelector('input[name="_method"]')) {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);
            }
            document.getElementById('subdomain_id').value = domain.id;
            document.getElementById('subdomain_school_id').value = domain.school_id;
            document.getElementById('link').value = domain.link;
            document.getElementById('db_host').value = domain.db_host;
            document.getElementById('db_username').value = domain.db_username;
            document.getElementById('db_password').value = domain.db_password;
            document.getElementById('db_table').value = domain.db_table;
            document.getElementById('is_active').value = domain.is_active ? '1' : '0';
            document.getElementById('subdomainModalLabel').textContent = 'Edit Subdomain';
        }

        // Reset Custom Domain form for create
        function resetCustomDomainForm() {
            const form = document.getElementById('customDomainForm');
            form.reset();
            form.action = "{{ route('user.domains.custom.store') }}";
            document.getElementById('custom_domain_id').value = '';
            document.getElementById('customDomainModalLabel').textContent = 'Create Subdomain';
        }

        // Fill custom domain modal form for editing
        function editCustomDomain(domain) {
            const form = document.getElementById('customDomainForm');
            form.action = `/user/domains/custom/${domain.id}`;
            if (!form.querySelector('input[name="_method"]')) {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);
            }
            document.getElementById('custom_domain_id').value = domain.id;
            document.getElementById('custom_school_id').value = domain.school_id;
            document.getElementById('custom_link').value = domain.link;
            document.getElementById('custom_db_host').value = domain.db_host;
            document.getElementById('custom_db_username').value = domain.db_username;
            document.getElementById('custom_db_password').value = ''; // Leave blank for security
            document.getElementById('custom_db_table').value = domain.db_table;
            document.getElementById('custom_is_active').value = domain.is_active ? '1' : '0';
            document.getElementById('customDomainModalLabel').textContent = 'Edit Subdomain';
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Subdomain table
            $('#subdomainsTable').DataTable({
                responsive: true,
                pageLength: 10,
                autoWidth: false,
                columnDefs: [{
                    orderable: false,
                    targets: -1 // Disable sort on Actions column
                }]
            });

            // Initialize Custom Domain table
            $('#customDomainsTable').DataTable({
                responsive: true,
                pageLength: 10,
                autoWidth: false,
                columnDefs: [{
                    orderable: false,
                    targets: -1 // Disable sort on Actions column
                }]
            });

            // Bootstrap tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush
