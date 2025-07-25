<!-- Assign Ticket Modal -->
<div class="modal fade" id="assignTicketModal" tabindex="-1" aria-labelledby="assignTicketLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header bg-primary text-white align-items-center">
                <h5 class="modal-title" id="assignTicketLabel">Assign Ticket</h5>
                <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal" aria-label="Close"
                    style="margin-right: 15px;"></button>
            </div>
            <form id="assignTicketForm">
                @csrf
                <div class="modal-body row g-3">
                    <input type="hidden" name="ticket_id" id="assign_ticket_id">

                    <div class="col-12">
                        <label for="assigned_to" class="form-label">Assign To</label>
                        <select name="assigned_to" id="assigned_to" class="form-select" required>
                            <option value="">-- Unassigned --</option>
                            @foreach ($agents as $agent)
                                @php
                                    $roles = $agent->getRoleNames()->map(fn($r) => ucfirst($r))->join(', ');
                                @endphp
                                <option value="{{ $agent->id }}">
                                    {{ $agent->name }} ({{ $agent->email }}) - [{{ $roles }}]
                                </option>
                            @endforeach
                        </select>

                    </div>
                </div>

                <div class="modal-footer bg-light p-0">
                    <button type="button" class="btn btn-outline-secondary m-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary m-2">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>
