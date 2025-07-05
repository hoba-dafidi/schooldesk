@extends('layouts/contentNavbarLayout')

@section('title', 'Manage Plans')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Manage Plans</h4>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> Please fix the following errors:
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Filter Form -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.plans.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search plans..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">-- Filter by Status --</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary" type="submit">Filter</button>
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Subscription Plans</h5>
                @role('admin')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#planModal"
                        onclick="resetPlanForm()">
                        <i class="bx bx-plus"></i> Add Plan
                    </button>
                @endrole
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Price</th>
                            <th>Currency</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($plans as $plan)
                            <tr>
                                <td>{{ $plan->name }}</td>
                                <td>{{ $plan->slug }}</td>
                                <td>{{ number_format($plan->price, 2) }}</td>
                                <td>{{ $plan->currency }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $plan->is_active ? 'success' : 'secondary' }}">
                                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    @role('admin')
                                        <button class="btn btn-sm btn-secondary"
                                            onclick='viewPlan(@json($plan))'>
                                            View
                                        </button>

                                        <button class="btn btn-sm btn-info" onclick='editPlan(@json($plan))'
                                            data-bs-toggle="modal" data-bs-target="#planModal">Edit</button>

                                        <form action="{{ route('admin.plans.toggleStatus', $plan->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-sm {{ $plan->is_active ? 'btn-warning' : 'btn-success' }}"
                                                onclick="return confirm('Are you sure you want to {{ $plan->is_active ? 'deactivate' : 'activate' }} this plan?')">
                                                {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST"
                                            style="display:inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure to delete this plan?')">Delete</button>
                                        </form>
                                    @endrole
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $plans->withQueryString()->links() }}
            </div>
        </div>

        <!-- Plan Modal -->
        <div class="modal fade" id="planModal" tabindex="-1" aria-labelledby="planModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form id="planForm" action="{{ route('admin.plans.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="plan_id">
                    <div class="modal-content shadow-lg border-0 rounded-3">
                        <div class="modal-header bg-primary text-white align-items-center">
                            <h5 class="modal-title text-white mb-3" id="planModalLabel">Create New Plan</h5>
                            <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal"
                                aria-label="Close" style="margin-right: 15px;"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" id="plan_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Slug</label>
                                    <input type="text" name="slug" id="plan_slug" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Price</label>
                                    <input type="number" name="price" id="plan_price" class="form-control"
                                        step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Currency</label>
                                    <input type="text" name="currency" id="plan_currency" class="form-control"
                                        value="NGN" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" id="plan_description" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Features</label>
                                    <div id="featuresContainer">
                                        <input type="text" name="features[]" class="form-control mb-2"
                                            placeholder="Feature name">
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="addFeatureField()">Add Feature</button>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="is_active" id="plan_is_active" class="form-select">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light p-0">
                            <button type="button" class="btn btn-outline-secondary m-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary m-2">Save Plan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Show Plan Modal --}}
        <!-- View Plan Modal -->
        <div class="modal fade" id="showPlanModal" tabindex="-1" aria-labelledby="showPlanModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg border-0 rounded-3">
                    <div class="modal-header bg-primary text-white align-items-center">
                        <h5 class="modal-title text-white mb-3" id="showPlanModalLabel">Plan Details</h5>
                        <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal"
                            aria-label="Close" style="margin-right: 15px;"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Name:</label>
                                <p id="view_plan_name" class="form-control-plaintext"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Slug:</label>
                                <p id="view_plan_slug" class="form-control-plaintext"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Price:</label>
                                <p id="view_plan_price" class="form-control-plaintext"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Currency:</label>
                                <p id="view_plan_currency" class="form-control-plaintext"></p>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Description:</label>
                                <p id="view_plan_description" class="form-control-plaintext"></p>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Features:</label>
                                <ul id="view_plan_features" class="list-group list-group-flush"></ul>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status:</label>
                                <p id="view_plan_status" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-0">
                        <button type="button" class="btn btn-outline-secondary m-2"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function resetPlanForm() {
            const form = document.getElementById('planForm');
            form.reset();
            document.getElementById('plan_id').value = '';
            document.getElementById('planModalLabel').textContent = 'Create New Plan';
            document.getElementById('featuresContainer').innerHTML =
                '<input type="text" name="features[]" class="form-control mb-2" placeholder="Feature name">';
            form.action = "{{ route('admin.plans.store') }}";
            if (form.querySelector('input[name="_method"]')) form.querySelector('input[name="_method"]').remove();
        }

        function editPlan(plan) {
            const form = document.getElementById('planForm');
            form.action = `/admin/plans/${plan.id}`;
            if (!form.querySelector('input[name="_method"]')) {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);
            }
            document.getElementById('plan_id').value = plan.id;
            document.getElementById('plan_name').value = plan.name;
            document.getElementById('plan_slug').value = plan.slug;
            document.getElementById('plan_price').value = plan.price;
            document.getElementById('plan_currency').value = plan.currency;
            document.getElementById('plan_description').value = plan.description;
            document.getElementById('plan_is_active').value = plan.is_active ? '1' : '0';

            const container = document.getElementById('featuresContainer');
            container.innerHTML = '';
            if (Array.isArray(plan.features)) {
                plan.features.forEach(feature => {
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.name = 'features[]';
                    input.className = 'form-control mb-2';
                    input.value = feature;
                    container.appendChild(input);
                });
            }
        }

        function addFeatureField() {
            const container = document.getElementById('featuresContainer');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'features[]';
            input.className = 'form-control mb-2';
            input.placeholder = 'Feature name';
            container.appendChild(input);
        }

        function viewPlan(plan) {
            document.getElementById('view_plan_name').textContent = plan.name;
            document.getElementById('view_plan_slug').textContent = plan.slug;
            document.getElementById('view_plan_price').textContent = parseFloat(plan.price).toFixed(2);
            document.getElementById('view_plan_currency').textContent = plan.currency;
            document.getElementById('view_plan_description').textContent = plan.description || '-';

            // Features
            const featuresContainer = document.getElementById('view_plan_features');
            featuresContainer.innerHTML = '';
            if (Array.isArray(plan.features) && plan.features.length > 0) {
                plan.features.forEach(feature => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item';
                    li.textContent = feature;
                    featuresContainer.appendChild(li);
                });
            } else {
                featuresContainer.innerHTML = '<li class="list-group-item text-muted">No features listed</li>';
            }

            // Status
            document.getElementById('view_plan_status').textContent = plan.is_active ? 'Active' : 'Inactive';

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('showPlanModal'));
            modal.show();
        }
    </script>
@endpush
