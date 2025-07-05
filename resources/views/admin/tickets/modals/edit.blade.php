<!-- Edit Ticket Modal -->
<div class="modal fade" id="editTicketModal" tabindex="-1" aria-labelledby="editTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">

            <div class="modal-header bg-primary text-white align-items-center">
                <h5 class="modal-title" id="editTicketModalLabel">Edit Ticket</h5>
                <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal" aria-label="Close"
                    style="margin-right: 15px;"></button>
            </div>

            <form id="editTicketForm" method="POST" action="{{ route('admin.tickets.update', $ticket->id ?? '') }}">
                @csrf
                @method('PUT')

                <div class="modal-body row g-3">
                    <div class="col-md-12">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject"
                            value="{{ old('subject', $ticket->subject ?? '') }}" required>
                    </div>

                    <div class="col-md-12">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="4" required>{{ old('message', $ticket->message ?? '') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            @foreach (\App\Enums\TicketStatus::cases() as $status)
                                <option value="{{ $status->value }}"
                                    {{ isset($ticket) && $ticket->status === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status->value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select" id="priority" name="priority">
                            <option value="low"
                                {{ isset($ticket) && $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium"
                                {{ isset($ticket) && $ticket->priority === 'medium' ? 'selected' : '' }}>Medium
                            </option>
                            <option value="high"
                                {{ isset($ticket) && $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                        </select>
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
