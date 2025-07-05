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
                <p><strong>Status:</strong> <span id="ticketStatus" class="badge"></span></p>
                <p><strong>Priority:</strong> <span id="ticketPriority" class="badge"></span></p>
            </div>
        </div>
    </div>
</div>
