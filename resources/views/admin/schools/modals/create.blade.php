<!-- Create School Modal -->
<div class="modal fade" id="createSchoolModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header bg-primary text-white align-items-center">
                <h5 class="modal-title">Add New School</h5>
                <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal" aria-label="Close"
                    style="margin-right: 15px;"></button>
            </div>

            <form action="{{ route('admin.schools.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">School Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Client (User)</label>
                        <select name="user_id" class="form-select">
                            <option value="">-- Select --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Plan</label>
                        <select name="plan_id" class="form-select">
                            <option value="">-- Select --</option>
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->formatted_price }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Established Year</label>
                        <input type="number" name="established_year" class="form-control" min="1900"
                            max="{{ now()->year }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">About</label>
                        <textarea name="about" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo" class="form-control">
                    </div>
                </div>

                <div class="modal-footer bg-light p-0">
                    <button type="submit" class="btn btn-primary m-2">Create</button>
                    <button type="button" class="btn btn-outline-secondary m-2" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
