<div class="modal fade" id="replyTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">

            <div class="modal-header bg-primary text-white align-items-center">
                <h5 class="modal-title">
                    Reply to Ticket: <span id="replyTicketSubject"></span>
                    <small class="text-white-50" id="replyTicketId" style="font-size: 0.8rem;"></small>
                </h5>
                <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal"
                    style="margin-right: 15px"></button>
            </div>

            <div class="modal-body">
                <!-- Message Thread -->
                <div id="messageThread" class="mb-4 border p-3 rounded" style="max-height: 300px; overflow-y:auto;">
                    <p class="text-muted text-center">Loading messages...</p>
                </div>

                <!-- Reply Form -->
                <form id="replyForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="ticket_id" id="reply_ticket_id">

                    <div class="mb-3">
                        <label class="form-label">Your Message</label>
                        <textarea name="message" rows="4" class="form-control" required></textarea>
                        <div class="invalid-feedback d-none" id="message-error"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Attachments (PDF, JPG, PNG)</label>
                        <div id="replyDropzoneArea" class="border rounded p-3 text-center bg-light">
                            <p class="mb-1">Drag & Drop files here or click to browse</p>
                            <input type="file" name="attachments[]" id="reply_attachment_input"
                                class="form-control d-none" multiple accept=".pdf,.jpg,.jpeg,.png">
                            <div id="reply_file_list" class="mt-2 text-start small text-muted"></div>
                        </div>
                        <div id="reply_progress_wrapper" class="progress mt-3 d-none">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                style="width: 0%">Uploading...</div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light p-0">
                        <button type="button" class="btn btn-outline-secondary m-2"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success m-2">Send Reply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
