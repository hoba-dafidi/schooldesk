<div class="modal fade" id="subscriptionModal" tabindex="-1" aria-labelledby="subscriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="subscriptionForm">
            @csrf
            <input type="hidden" name="school_id" id="subscription_school_id">
            <input type="hidden" name="plan_id" id="subscription_plan_id">
            <input type="hidden" name="payment_gateway_slug" id="payment_gateway_slug" value="manual">

            <div class="modal-content shadow-lg border-0 rounded-3">
                <div class="modal-header bg-primary text-white align-items-center">
                    <h5 class="modal-title" id="subscriptionModalLabel">Manage Subscription</h5>
                    <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal"
                        aria-label="Close" style="margin-right: 15px;"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Left: Current Subscription Info -->
                        <div class="col-12 col-md-5">
                            <div id="currentSubscriptionInfo" class="card border shadow-sm"
                                style="opacity: 0; display: none;">
                                <div
                                    class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
                                    <span>Current Subscription</span>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Plan</span>
                                            <strong id="currentPlanName" class="text-primary"></strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Price</span>
                                            <strong id="currentPlanPrice" class="text-success"></strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Status</span>
                                            <span id="currentStatus" class="badge px-3 py-1"></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Starts At</span>
                                            <span id="currentStartsAt" class="text-muted"></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Ends At</span>
                                            <span id="currentEndsAt" class="text-muted"></span>
                                        </li>
                                    </ul>
                                    <div class="d-flex justify-content-center py-3">
                                        <a class="btn btn-xs btn-outline-danger" id="cancelSubscriptionBtn"
                                            style="display: none;">
                                            <i class="bx bx-x-circle"></i> Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Form Fields -->
                        <div class="col-12 col-md-7">
                            <div class="mb-3">
                                <label for="planSelect" class="form-label">Select Plan</label>
                                <select name="plan_id" id="planSelect" class="form-select" required>
                                    <option value="">-- Choose a Plan --</option>
                                    @foreach ($plans as $plan)
                                        <option value="{{ $plan->id }}" data-duration="{{ $plan->duration_days }}">
                                            {{ $plan->name }} — {{ $plan->formatted_price }}
                                            ({{ $plan->duration_days }} days)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="gatewayFields">
                                <div class="mb-3">
                                    <label for="gatewaySelect" class="form-label">Select Payment Method</label>
                                    <select name="gateway_id" class="form-select" id="gatewaySelect" required>
                                        @foreach ($gateways as $gateway)
                                            <option value="{{ $gateway->id }}" data-slug="{{ $gateway['slug'] }}">
                                                {{ $gateway->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Payer Email</label>
                                    <input type="email" name="payer_email" class="form-control"
                                        placeholder="e.g. user@example.com">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Amount (₦)</label>
                                    <input type="number" name="amount" class="form-control" placeholder="Amount (₦)"
                                        min="100">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tax Amount (₦)</label>
                                    <input type="number" name="tax_amount" class="form-control" placeholder="e.g. 50"
                                        min="0" step="0.01" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Total (₦)</label>
                                    <input type="text" id="totalWithTax" class="form-control" readonly>
                                </div>
                            </div>

                            <div id="subscriptionMessages" class="mb-3"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light p-3">
                    <button type="button" id="renewBtn" class="btn btn-success me-auto" disabled>Renew</button>
                    <button type="button" id="upgradeBtn" class="btn btn-warning" disabled>Upgrade</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
