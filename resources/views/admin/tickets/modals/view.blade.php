<div class="modal fade" id="viewTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">

            <div class="modal-header bg-info text-white align-items-center">
                <h5 class="modal-title" id="viewModalTitle">Ticket Details</h5>
                <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal"
                    style="margin-right: 15px"></button>
            </div>

            <div class="modal-body">
                <p><strong>Subject:</strong> <span id="ticketSubject"></span></p>

                <p><strong>Description:</strong></p>
                <p id="ticketDescription" class="text-muted"></p>

                <p><strong>Status:</strong>
                    <span id="ticketStatus" class="badge px-2 py-1 fw-medium"></span>
                </p>

                <p><strong>Priority:</strong>
                    <span id="ticketPriority" class="badge px-2 py-1 fw-medium"></span>
                </p>

                <div id="ticketAttachments" class="mt-3">
                    <strong>Attachments:</strong>
                    <div class="d-flex flex-wrap gap-2 mt-1" id="ticketAttachmentList"></div>
                </div>

                <hr>

                <div>
                    <h6 class="fw-bold">Message Thread</h6>
                    <div id="ticketMessages" style="max-height: 250px; overflow-y: auto;">
                        <p class="text-muted text-center">Loading messages...</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light justify-content-between px-3 py-2">
                <div>
                    <button class="btn btn-outline-danger btn-sm" id="closeTicketBtn"><i class="fas fa-lock"></i> Close
                        Ticket</button>
                    <button class="btn btn-outline-warning btn-sm" id="escalateTicketBtn"><i
                            class="fas fa-arrow-up"></i> Escalate</button>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
