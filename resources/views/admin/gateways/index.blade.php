@extends('layouts/contentNavbarLayout')

@section('title', 'Payment Gateways')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Manage Payment Gateways</h4>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Gateways</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#gatewayModal"
                    onclick="openGatewayModal()">
                    <i class="bx bx-plus"></i> Add Gateway
                </button>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Currency</th>
                            <th>Status</th>
                            <th>Sandbox</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gateways as $gateway)
                            <tr>
                                <td>{{ $gateway->name }}</td>
                                <td>{{ $gateway->slug }}</td>
                                <td>{{ $gateway->currency }}</td>
                                <td><span
                                        class="badge bg-{{ $gateway->is_active ? 'success' : 'secondary' }}">{{ $gateway->is_active ? 'Active' : 'Inactive' }}</span>
                                </td>
                                <td><span
                                        class="badge bg-{{ $gateway->sandbox_mode ? 'warning' : 'info' }}">{{ $gateway->sandbox_mode ? 'Sandbox' : 'Live' }}</span>
                                </td>
                                <td class="text-nowrap">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="showGatewayDetails(@json($gateway))" data-bs-toggle="modal"
                                        data-bs-target="#viewGatewayModal">
                                        <i class="bx bx-show"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info"
                                        onclick="editGateway(@json($gateway))" data-bs-toggle="modal"
                                        data-bs-target="#gatewayModal">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.gateways.toggleStatus', $gateway->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Toggle Status">
                                            <i class="bx bx-refresh"></i>
                                        </button>
                                    </form>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteGatewayModal"
                                        onclick="setDeleteAction({{ $gateway->id }})">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Reusable Create/Edit Modal -->
    <div class="modal fade" id="gatewayModal" tabindex="-1" aria-labelledby="gatewayModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-3">
                <form id="gatewayForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="POST" id="formMethod">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="gatewayModalLabel">Add Payment Gateway</h5>
                        <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal"
                            style="margin-right: 15px;"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="gatewayName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug</label>
                            <input type="text" class="form-control" name="slug" id="gatewaySlug" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Currency</label>
                            <input type="text" class="form-control" name="currency" id="gatewayCurrency">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Driver Class</label>
                            <input type="text" class="form-control" name="driver_class" id="gatewayDriver">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Callback URL</label>
                            <input type="text" class="form-control" name="callback_url" id="gatewayCallback">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Webhook URL</label>
                            <input type="text" class="form-control" name="webhook_url" id="gatewayWebhook">
                        </div>

                        <hr class="mt-4 mb-2">
                        <h6>API Credentials</h6>

                        <div class="col-md-6">
                            <label class="form-label">API Key</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="api_key" id="gatewayApiKey">
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="gatewayApiKey">
                                    <i class="bx bx-show"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">API Secret</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="api_secret" id="gatewayApiSecret">
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="gatewayApiSecret">
                                    <i class="bx bx-show"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Merchant ID</label>
                            <input type="text" class="form-control" name="merchant_id" id="gatewayMerchantId">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Webhook Secret</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="webhook_secret"
                                    id="gatewayWebhookSecret">
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="gatewayWebhookSecret">
                                    <i class="bx bx-show"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Encryption Key</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="encryption_key"
                                    id="gatewayEncryptionKey">
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="gatewayEncryptionKey">
                                    <i class="bx bx-show"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="is_active" id="gatewayActive">
                                <label class="form-check-label" for="gatewayActive">Active</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="sandbox_mode" id="gatewaySandbox">
                                <label class="form-check-label" for="gatewaySandbox">Sandbox Mode</label>
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

    <!-- View Modal -->
    <div class="modal fade" id="viewGatewayModal" tabindex="-1" aria-labelledby="viewGatewayModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-3">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="viewGatewayModalLabel">Gateway Details</h5>
                    <button type="button" class="btn-close btn-close-white mt-2" data-bs-dismiss="modal"
                        style="margin-right: 15px;"></button>
                </div>
                <div class="modal-body">
                    <dl class="row">
                        <dt class="col-sm-4">Name</dt>
                        <dd class="col-sm-8" id="viewName"></dd>

                        <dt class="col-sm-4">Slug</dt>
                        <dd class="col-sm-8" id="viewSlug"></dd>

                        <dt class="col-sm-4">Currency</dt>
                        <dd class="col-sm-8" id="viewCurrency"></dd>

                        <dt class="col-sm-4">Driver</dt>
                        <dd class="col-sm-8" id="viewDriver"></dd>

                        <dt class="col-sm-4">Callback URL</dt>
                        <dd class="col-sm-8" id="viewCallback"></dd>

                        <dt class="col-sm-4">Webhook URL</dt>
                        <dd class="col-sm-8" id="viewWebhook"></dd>

                        <dt class="col-sm-4">API Key</dt>
                        <dd class="col-sm-8"><span id="viewApiKey">-</span> <button
                                class="btn btn-sm btn-outline-secondary ms-2"
                                onclick="toggleMask('viewApiKey')">Show</button></dd>

                        <dt class="col-sm-4">Merchant ID</dt>
                        <dd class="col-sm-8" id="viewMerchant"></dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8" id="viewStatus"></dd>

                        <dt class="col-sm-4">Mode</dt>
                        <dd class="col-sm-8" id="viewMode"></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const target = document.getElementById(this.dataset.target);
                const icon = this.querySelector('i');
                if (target.type === 'password') {
                    target.type = 'text';
                    icon.classList.replace('bx-show', 'bx-hide');
                } else {
                    target.type = 'password';
                    icon.classList.replace('bx-hide', 'bx-show');
                }
            });
        });

        function editGateway(gateway) {
            document.getElementById('gatewayForm').action = `/admin/gateways/${gateway.id}`;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('gatewayModalLabel').innerText = `Edit ${gateway.name}`;

            document.getElementById('gatewayName').value = gateway.name;
            document.getElementById('gatewaySlug').value = gateway.slug;
            document.getElementById('gatewayCurrency').value = gateway.currency ?? '';
            document.getElementById('gatewayDriver').value = gateway.driver_class ?? '';
            document.getElementById('gatewayCallback').value = gateway.callback_url ?? '';
            document.getElementById('gatewayWebhook').value = gateway.webhook_url ?? '';
            document.getElementById('gatewayApiKey').value = gateway.api_key ?? '';
            document.getElementById('gatewayApiSecret').value = gateway.api_secret ?? '';
            document.getElementById('gatewayMerchantId').value = gateway.merchant_id ?? '';
            document.getElementById('gatewayWebhookSecret').value = gateway.webhook_secret ?? '';
            document.getElementById('gatewayEncryptionKey').value = gateway.encryption_key ?? '';
            document.getElementById('gatewayActive').checked = gateway.is_active;
            document.getElementById('gatewaySandbox').checked = gateway.sandbox_mode;
        }

        function showGatewayDetails(g) {
            document.getElementById('viewName').innerText = g.name;
            document.getElementById('viewSlug').innerText = g.slug;
            document.getElementById('viewCurrency').innerText = g.currency ?? '-';
            document.getElementById('viewDriver').innerText = g.driver_class ?? '-';
            document.getElementById('viewCallback').innerText = g.callback_url ?? '-';
            document.getElementById('viewWebhook').innerText = g.webhook_url ?? '-';
            const masked = g.api_key ? g.api_key.replace(/.(?=.{4})/g, '*') : '-';
            document.getElementById('viewApiKey').innerText = masked;
            document.getElementById('viewApiKey').setAttribute('data-real', g.api_key);
            document.getElementById('viewMerchant').innerText = g.merchant_id ?? '-';
            document.getElementById('viewStatus').innerText = g.is_active ? 'Active' : 'Inactive';
            document.getElementById('viewMode').innerText = g.sandbox_mode ? 'Sandbox' : 'Live';
        }

        function toggleMask(id) {
            const el = document.getElementById(id);
            const current = el.innerText;
            const real = el.getAttribute('data-real');
            if (current.includes('*')) {
                el.innerText = real;
            } else {
                el.innerText = real.replace(/.(?=.{4})/g, '*');
            }
        }
    </script>
@endsection
