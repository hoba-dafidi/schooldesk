<div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">

            <div class="modal-header bg-primary text-white align-items-center">
                <h5 class="modal-title">Create New Ticket</h5>
                <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal" aria-label="Close"
                    style="margin-right: 15px"></button>
            </div>

            <form id="createTicketForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select" required>
                            @foreach (\App\Enums\TicketPriority::cases() as $priority)
                                <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Message</label>
                        <textarea name="message" rows="4" class="form-control" required></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Attachments (PDF, JPG, PNG only)</label>
                        <div id="dropzoneArea" class="border rounded p-3 text-center bg-light">
                            <p class="mb-1">Drag & Drop files here or click to browse</p>
                            <input type="file" name="attachments[]" id="attachmentInput" class="form-control d-none"
                                multiple accept=".pdf,.jpg,.jpeg,.png">
                            <div id="fileList" class="mt-2 text-start small text-muted"></div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light p-0">
                    <button type="button" class="btn btn-outline-secondary m-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary m-2">Create Ticket</button>
                </div>
            </form>

        </div>
    </div>
</div>
