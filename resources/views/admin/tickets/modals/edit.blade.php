<div class="modal fade" id="editTicketModal" tabindex="-1" aria-labelledby="editTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">

            <div class="modal-header bg-primary text-white align-items-center">
                <h5 class="modal-title" id="editTicketModalLabel">Edit Ticket</h5>
                <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal" aria-label="Close"
                    style="margin-right: 15px;"></button>
            </div>

            <form id="editTicketForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body row g-3">
                    <input type="hidden" name="ticket_id" id="edit_ticket_id">

                    <div class="col-md-12">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" name="subject" id="edit_subject" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="edit_status" required>
                            @foreach (\App\Enums\TicketStatus::cases() as $status)
                                <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Priority</label>
                        <select class="form-select" name="priority" id="edit_priority">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Message (Reply)</label>
                        <textarea name="message" rows="4" class="form-control" id="edit_message"></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Existing Attachments</label>
                        <div id="existing_attachments" class="d-flex flex-wrap gap-2 text-muted small"></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Attachments (optional)</label>
                        <div id="editDropzoneArea" class="border rounded p-3 text-center bg-light">
                            <p class="mb-1">Drag & Drop files here or click to browse</p>
                            <input type="file" name="attachments[]" id="edit_attachment_input"
                                class="form-control d-none" multiple accept=".pdf,.jpg,.jpeg,.png">
                            <div id="edit_file_list" class="mt-2 text-start small text-muted"></div>
                        </div>
                        <div id="edit_progress_wrapper" class="progress mt-3 d-none">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                style="width: 0%">Uploading...</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light p-0">
                    <button type="button" class="btn btn-outline-secondary m-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary m-2">Update Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>
