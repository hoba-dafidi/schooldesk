@extends('layouts/contentNavbarLayout')

@section('title', 'Manage Schools')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4 d-flex justify-content-between align-items-center">
            Manage Schools
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#schoolModal" onclick="resetSchoolForm()">
                <i class="bx bx-plus"></i> Create New School
            </button>
        </h4>

        <!-- Alert Notifications -->
        <div id="schoolAlert" class="alert d-none" role="alert"></div>

        <!-- Schools List as Table -->
        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Name</th>
                            {{-- <th>Subdomain</th>
            <th>Custom Domain</th> --}}
                            <th>Status</th>
                            <th>Plan</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($schools as $school)
                            <tr>
                                <td>
                                    @if ($school->logo)
                                        <a href="{{ asset('storage/' . $school->logo) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo"
                                                class="rounded" style="height: 40px;">
                                        </a>
                                    @else
                                        <span class="text-muted">No Logo</span>
                                    @endif
                                </td>
                                <td>{{ $school->name }}</td>
                                {{-- <td>{{ $school->subdomain }}</td>
            <td>{{ $school->custom_domain }}</td> --}}
                                <td>
                                    <span class="badge bg-label-{{ $school->status === 'active' ? 'success' : 'warning' }}">
                                        {{ ucfirst($school->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-label-{{ $school->is_paid_plan ? 'primary' : 'secondary' }}">
                                        {{ $school->is_paid_plan ? 'Paid' : 'Free' }}
                                    </span>
                                </td>
                                <td class="d-flex gap-1">
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                        data-bs-target="#schoolModal"
                                        onclick='editSchool(@json($school))'>Edit</button>
                                    <form action="{{ route('user.schools.destroy', $school->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No schools found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create/Edit School Modal -->
        <div class="modal fade" id="schoolModal" tabindex="-1" aria-labelledby="schoolModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg border-0 rounded-3">
                    <form id="schoolForm" method="POST" action="{{ route('user.schools.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="school_id">
                        <div class="modal-header bg-primary text-white align-items-center">
                            <h5 class="modal-title text-white mb-3" id="schoolModalLabel">Create School</h5>
                            <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal"
                                aria-label="Close" style="margin-right: 15px;"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>
                                {{-- <div class="col-md-6">
                <label class="form-label">Subdomain</label>
                <input type="text" name="subdomain" id="subdomain" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">Custom Domain</label>
                <input type="text" name="custom_domain" id="custom_domain" class="form-control">
              </div> --}}
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="active">Active</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Plan</label>
                                    <select name="is_paid_plan" id="is_paid_plan" class="form-select">
                                        <option value="1">Paid</option>
                                        <option value="0">Free</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Logo</label>
                                    <input type="file" name="logo" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light p-0">
                            <button type="button" class="btn btn-outline-secondary m-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary m-2">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner"
        class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-light bg-opacity-50 d-none"
        style="z-index:1055">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function resetSchoolForm() {
            const form = document.getElementById('schoolForm');
            form.reset();
            form.action = "{{ route('user.schools.store') }}";
            document.getElementById('school_id').value = '';
            document.getElementById('schoolModalLabel').textContent = 'Create School';
            form.querySelector('[name="_method"]')?.remove();
        }

        function editSchool(school) {
            const form = document.getElementById('schoolForm');
            form.action = `/user/schools/${school.id}`;
            form.querySelector('[name="_method"]')?.remove();

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            document.getElementById('school_id').value = school.id;
            document.getElementById('name').value = school.name;
            // document.getElementById('subdomain').value = school.subdomain;
            // document.getElementById('custom_domain').value = school.custom_domain;
            document.getElementById('status').value = school.status;
            document.getElementById('is_paid_plan').value = school.is_paid_plan ? '1' : '0';
            document.getElementById('schoolModalLabel').textContent = 'Edit School';
        }
    </script>
@endpush
