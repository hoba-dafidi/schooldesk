<div class="modal fade" id="schoolModal" tabindex="-1" aria-labelledby="schoolModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <form id="schoolForm" method="POST" action="{{ route('user.schools.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="school_id">

                <div class="modal-header bg-primary text-white align-items-center">
                    <h5 class="modal-title text-white" id="schoolModalLabel">Create School</h5>
                    <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal"
                        aria-label="Close" style="margin-right: 15px;"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" id="address" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">Select Type</option>
                                <option value="Primary">Primary</option>
                                <option value="Secondary">Secondary</option>
                                <option value="Tertiary">Tertiary</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Established Year</label>
                            <input type="number" name="established_year" id="established_year" class="form-control"
                                min="1900" max="{{ date('Y') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" name="city" id="city" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">State</label>
                            <input type="text" name="state" id="state" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" id="country" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                                <option value="deleted">Deleted</option>
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
                            <input type="file" name="logo" class="form-control" onchange="previewLogo(event)">
                            <img id="logoPreview" src="#" alt="Logo Preview"
                                class="img-thumbnail mt-2 d-none" style="max-height: 100px;">
                        </div>

                        <div class="col-12">
                            <label class="form-label">About</label>
                            <textarea name="about" id="about" class="form-control" rows="3"></textarea>
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
