@extends('layouts/contentNavbarLayout')

@section('title', 'Schools')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">School Management</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSchoolModal">
                <i class="bx bx-plus"></i> Add School
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body table-responsive">
                <table id="schoolsTable" class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Client</th>
                            <th>Email</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schools as $school)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ $school->logo_url }}" alt="Logo" class="rounded-circle" width="40"
                                        height="40">
                                </td>
                                <td>{{ $school->name }}</td>
                                <td>{{ $school->client?->name ?? '-' }}</td>
                                <td>{{ $school->email }}</td>
                                <td>
                                    @if ($school->plan)
                                        <span class="badge bg-info" data-bs-toggle="tooltip"
                                            title="{{ $school->plan->description }}">
                                            {{ $school->plan->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">None</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $school->status_badge_class }}">
                                        {{ ucfirst($school->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <button class="btn btn-xs btn-info" data-bs-toggle="modal"
                                            data-bs-target="#viewSchoolModal" data-school='@json($school)'>
                                            <i class="bx bx-show"></i>
                                        </button>

                                        @role(['admin', 'developer', 'support'])
                                            <button class="btn btn-xs btn-warning edit-school-btn"
                                                data-school='@json($school)'>
                                                <i class="bx bx-edit"></i>
                                            </button>
                                        @endrole

                                        <form action="{{ route('admin.schools.destroy', $school->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this school?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-danger">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create Modal --}}
    @include('admin.schools.modals.create', ['plans' => $plans, 'users' => $users])

    {{-- ✅ Reusable View Modal --}}
    @include('admin.schools.modals.view')

    {{-- ✅ Reusable Edit Modal --}}
    @include('admin.schools.modals.edit', ['plans' => $plans, 'users' => $users])

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable
            const table = document.querySelector('#schoolsTable');
            if (table) {
                new DataTable(table, {
                    responsive: true,
                    pageLength: 10,
                });
            }

            // Initialize Bootstrap tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Handle view modal show
            const viewModal = document.getElementById('viewSchoolModal');
            if (viewModal) {
                viewModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const school = JSON.parse(button.getAttribute('data-school'));

                    viewModal.querySelector('.modal-title').textContent = school.name;
                    viewModal.querySelector('.school-logo').setAttribute('src', school.logo_url);
                    viewModal.querySelector('.school-name').textContent = school.name;
                    viewModal.querySelector('.school-email').textContent = school.email;
                    viewModal.querySelector('.school-phone').textContent = school.phone;
                    viewModal.querySelector('.school-address').textContent = school.address;
                    viewModal.querySelector('.school-type').textContent = school.type;
                    viewModal.querySelector('.school-city').textContent = school.city;
                    viewModal.querySelector('.school-state').textContent = school.state;
                    viewModal.querySelector('.school-country').textContent = school.country;
                    viewModal.querySelector('.school-about').textContent = school.about ?? '-';

                    const sub = school.subdomain?.link;
                    viewModal.querySelector('.school-subdomain').innerHTML = sub ?
                        `<a href="http://${sub}" target="_blank">${sub}</a>` :
                        '—';

                    const cus = school.custom_domain?.link;
                    viewModal.querySelector('.school-customdomain').innerHTML = cus ?
                        `<a href="http://${cus}" target="_blank">${cus}</a>` :
                        '—';

                    viewModal.querySelector('.school-year').textContent = school.established_year ?? '-';

                    const statusBadge = viewModal.querySelector('.school-status');
                    statusBadge.textContent = school.status;
                    statusBadge.className = 'badge bg-' + (school.status === 'active' ? 'success' :
                        'secondary') + ' school-status';

                    viewModal.querySelector('.school-plan').textContent = school.plan?.name ?? 'None';

                    const featuresList = viewModal.querySelector('.school-plan-features');
                    featuresList.innerHTML = '';
                    if (Array.isArray(school.plan?.features)) {
                        school.plan.features.forEach(feature => {
                            const li = document.createElement('li');
                            li.textContent = feature;
                            featuresList.appendChild(li);
                        });
                    } else {
                        const li = document.createElement('li');
                        li.textContent = 'No features';
                        featuresList.appendChild(li);
                    }
                });
            }

            // Handle edit modal
            const editModal = document.getElementById('editSchoolModal');
            if (table) {
                table.addEventListener('click', function(e) {
                    const btn = e.target.closest('.edit-school-btn');
                    if (!btn) return;

                    const school = JSON.parse(btn.getAttribute('data-school'));
                    const form = editModal.querySelector('form');

                    // Set form action
                    form.setAttribute('action', `/admin/schools/${school.id}`);

                    // Populate inputs
                    form.querySelector('#edit-id').value = school.id;
                    form.querySelector('#edit-name').value = school.name;
                    form.querySelector('#edit-email').value = school.email;
                    form.querySelector('#edit-phone').value = school.phone;
                    form.querySelector('#edit-user').value = school.user_id;
                    form.querySelector('#edit-plan').value = school.plan_id;
                    form.querySelector('#edit-type').value = school.type;
                    form.querySelector('#edit-year').value = school.established_year;
                    form.querySelector('#edit-city').value = school.city;
                    form.querySelector('#edit-state').value = school.state;
                    form.querySelector('#edit-country').value = school.country;
                    form.querySelector('#edit-status').value = school.status;
                    form.querySelector('#edit-about').value = school.about ?? '';

                    // Show modal
                    const modal = new bootstrap.Modal(editModal);
                    modal.show();
                });
            }
        });
    </script>
@endpush
