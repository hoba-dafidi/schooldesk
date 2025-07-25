<div class="modal fade" id="editSchoolModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="POST" class="modal-content shadow-lg border-0 rounded-3" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-header bg-primary text-white align-items-center">
                <h5 class="modal-title">Edit School</h5>
                <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal" aria-label="Close"
                    style="margin-right: 15px;"></button>
            </div>

            <div class="modal-body row g-3">
                <input type="hidden" name="id" id="edit-id">

                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" id="edit-name" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="edit-email" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="phone" id="edit-phone">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Logo</label>
                    <input type="file" class="form-control" name="logo">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Client (User)</label>
                    <select class="form-select" name="user_id" id="edit-user">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Plan</label>
                    <select class="form-select" name="plan_id" id="edit-plan">
                        <option value="">-- None --</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Type</label>
                    <input type="text" class="form-control" name="type" id="edit-type">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Established Year</label>
                    <input type="number" class="form-control" name="established_year" id="edit-year">
                </div>

                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" name="city" id="edit-city">
                </div>

                <div class="col-md-6">
                    <label class="form-label">State</label>
                    <input type="text" class="form-control" name="state" id="edit-state">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Country</label>
                    <input type="text" class="form-control" name="country" id="edit-country">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" id="edit-status">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="suspended">Suspended</option>
                        <option value="deleted">Deleted</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">About</label>
                    <textarea class="form-control" name="about" id="edit-about" rows="3"></textarea>
                </div>
            </div>

            <div class="modal-footer bg-light p-0">
                <button type="button" class="btn btn-outline-secondary m-2" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary m-2">Update School</button>
            </div>
        </form>
    </div>
</div>
