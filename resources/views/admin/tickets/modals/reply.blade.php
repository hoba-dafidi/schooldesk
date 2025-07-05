<div class="modal fade" id="replyTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header bg-primary text-white align-items-center">
                <h5 class="modal-title">
                    Reply to Ticket: <span id="replyTicketSubject"></span>
                    <small class="text-white-50" id="replyTicketId"></small>
                </h5>
                <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal"
                    style="margin-right: 15px"></button>
            </div>

            <div class="modal-body">
                <!-- Message Thread -->
                <div id="messageThread" class="mb-4" style="max-height: 300px; overflow-y:auto;">
                    <p class="text-muted text-center">Loading messages...</p>
                </div>

                <!-- Reply Form -->
                <form method="POST" id="replyForm" enctype="multipart/form-data" onsubmit="submitReply(event)">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Your Message</label>
                        <textarea name="message" rows="4" class="form-control" required></textarea>
                        <div class="mb-3">
                            <label class="form-label">Attachment (optional)</label>
                            <input type="file" name="attachment" class="form-control">
                        </div>
                        <div class="invalid-feedback d-none" id="message-error"></div>
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
