@extends('layouts/contentNavbarLayout')

@section('title', 'Manage School Domains')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4 d-flex justify-content-between align-items-center">
            Manage School Domains
            <button class="btn btn-primary" onclick="openCreateModal()">
                <i class="bx bx-plus"></i> Create New
            </button>
        </h4>

        <!-- Domains List -->
        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="table table-bordered datatables-basic">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Link</th>
                            <th>DB Host</th>
                            <th>DB User</th>
                            <th>Table</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($domains as $domain)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="badge bg-label-info">{{ ucfirst($domain->type) }}</span></td>
                                <td><a href="http://{{ $domain->link }}" target="_blank">{{ $domain->link }}</a></td>
                                <td>{{ $domain->db_host }}</td>
                                <td>{{ $domain->db_username }}</td>
                                <td>{{ $domain->table_name }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $domain->is_active ? 'success' : 'secondary' }}">
                                        {{ $domain->is_active ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary"
                                        onclick="openEditModal(@json($domain))">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <form action="{{ route('domains.destroy', $domain->id) }}" method="POST"
                                        style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit/Create Domain Modal -->
        <div class="modal fade" id="editDomainModal" tabindex="-1" aria-labelledby="editDomainModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDomainModalLabel">Edit Domain</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editDomainForm" method="POST">
                            @csrf
                            <input type="hidden" name="_method" value="PUT" id="_method">
                            <input type="hidden" name="id" id="domain_id">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Domain Type</label>
                                    <select class="form-select" name="type" id="type" required>
                                        <option value="subdomain">Subdomain</option>
                                        <option value="custom_domain">Custom Domain</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Domain Link</label>
                                    <input type="text" class="form-control" name="link" id="link"
                                        placeholder="e.g. school.example.com" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Is Active?</label>
                                    <select class="form-select" name="is_active" id="is_active" required>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">DB Host</label>
                                    <input type="text" class="form-control" name="db_host" id="db_host"
                                        placeholder="127.0.0.1" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">DB Name</label>
                                    <input type="text" class="form-control" name="db_name" id="db_name"
                                        placeholder="school_db" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">DB Username</label>
                                    <input type="text" class="form-control" name="db_username" id="db_username"
                                        placeholder="root" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">DB Password</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" class="form-control" name="db_password" id="db_password"
                                            placeholder="••••••••">
                                        <span class="input-group-text cursor-pointer" onclick="togglePassword()"><i
                                                class="bx bx-hide"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(domain) {
            const form = document.getElementById('editDomainForm');
            form.action = `/domains/${domain.id}`;
            document.getElementById('editDomainModalLabel').textContent = 'Edit Domain';
            document.getElementById('_method').value = 'PUT';
            document.getElementById('domain_id').value = domain.id;
            document.getElementById('type').value = domain.type;
            document.getElementById('link').value = domain.link;
            document.getElementById('is_active').value = domain.is_active ? '1' : '0';
            document.getElementById('db_host').value = domain.db_host;
            document.getElementById('db_name').value = domain.db_name;
            document.getElementById('db_username').value = domain.db_username;
            document.getElementById('db_password').value = domain.db_password;
            new bootstrap.Modal(document.getElementById('editDomainModal')).show();
        }

        function openCreateModal() {
            const form = document.getElementById('editDomainForm');
            form.reset();
            form.action = '/domains';
            document.getElementById('editDomainModalLabel').textContent = 'Create Domain';
            document.getElementById('_method').value = 'POST';
            document.getElementById('domain_id').value = '';
            new bootstrap.Modal(document.getElementById('editDomainModal')).show();
        }

        function togglePassword() {
            const input = document.getElementById('db_password');
            const icon = event.currentTarget.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bx-hide');
                icon.classList.add('bx-show');
            } else {
                input.type = 'password';
                icon.classList.remove('bx-show');
                icon.classList.add('bx-hide');
            }
        }
    </script>
@endsection
