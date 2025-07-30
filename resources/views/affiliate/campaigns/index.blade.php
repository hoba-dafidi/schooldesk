@extends('layouts/contentNavbarLayout')

@section('title', 'Affiliate Campaigns')

@section('content')
    <div class="container-xxl">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">Campaign Manager</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCampaignModal">
                <i class="ti ti-plus"></i> New Campaign
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> Please fix the errors below.
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-striped" id="campaignsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Share Link</th>
                                <th>Clicks</th>
                                <th>Signups</th>
                                <th>Commission</th>
                                <th>Active Window</th>
                                <th style="width: 160px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($campaigns as $c)
                                @php
                                    $shareLink = url('/register?camp=' . $c->referral_code); // unified ?camp=CODE
                                    $commissionTotal = $commissionTotals[$c->id] ?? 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $c->name }}</strong>
                                        <div class="text-muted small">{{ $c->slug }}</div>
                                    </td>
                                    <td><code>{{ $c->referral_code }}</code></td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" value="{{ $shareLink }}" readonly
                                                id="link-{{ $c->id }}">
                                            <button class="btn btn-outline-secondary" type="button"
                                                onclick="copyLink('link-{{ $c->id }}', this)">Copy</button>
                                        </div>
                                    </td>
                                    <td>{{ number_format($c->clicks) }}</td>
                                    <td>{{ number_format($c->unique_signups) }}</td>
                                    <td>₦{{ number_format($commissionTotal, 2) }}</td>
                                    <td class="small">
                                        @if ($c->starts_at)
                                            {{ \Carbon\Carbon::parse($c->starts_at)->format('d M Y') }}
                                        @else
                                            —
                                        @endif
                                        –
                                        @if ($c->ends_at)
                                            {{ \Carbon\Carbon::parse($c->ends_at)->format('d M Y') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-secondary" data-bs-toggle="modal"
                                                data-bs-target="#editCampaignModal-{{ $c->id }}">Edit</button>
                                            <form action="{{ route('affiliate.campaigns.destroy', $c) }}" method="POST"
                                                onsubmit="return confirm('Delete this campaign?');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
                                        <form action="{{ route('affiliate.campaigns.regenerate', $c) }}" method="POST"
                                            class="mt-1">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-primary w-100" type="submit">Regenerate
                                                Code</button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editCampaignModal-{{ $c->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form class="modal-content" method="POST"
                                            action="{{ route('affiliate.campaigns.update', $c) }}">
                                            @csrf @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Campaign</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body row g-3">
                                                <div class="col-12">
                                                    <label class="form-label">Name</label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ old('name', $c->name) }}" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Slug</label>
                                                    <input type="text" name="slug" class="form-control"
                                                        value="{{ old('slug', $c->slug) }}" placeholder="auto if empty">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Code</label>
                                                    <input type="text" name="code" class="form-control"
                                                        value="{{ old('referral_code', $c->referral_code) }}"
                                                        placeholder="auto if empty">
                                                    <small class="text-muted">Used in share link (?ref=CODE). Must be
                                                        unique.</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Custom Commission %</label>
                                                    <input type="number" step="0.01" min="0" max="100"
                                                        name="custom_commission_rate" class="form-control"
                                                        value="{{ old('custom_commission_rate', $c->custom_commission_rate) }}">
                                                </div>
                                                <div class="col-md-6"></div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Starts At</label>
                                                    <input type="date" name="starts_at" class="form-control"
                                                        value="{{ old('starts_at', optional($c->starts_at)->format('Y-m-d')) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Ends At</label>
                                                    <input type="date" name="ends_at" class="form-control"
                                                        value="{{ old('ends_at', optional($c->ends_at)->format('Y-m-d')) }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                                    type="button">Cancel</button>
                                                <button class="btn btn-primary" type="submit">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $campaigns->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Create Modal --}}
    <div class="modal fade" id="createCampaignModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('affiliate.campaigns.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">New Campaign</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug') }}" class="form-control"
                            placeholder="auto if empty">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Code</label>
                        <input type="text" name="code" value="{{ old('referral_code') }}" class="form-control"
                            placeholder="auto if empty">
                        <small class="text-muted">Used in share link (?ref=CODE). Must be unique.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Custom Commission %</label>
                        <input type="number" step="0.01" min="0" max="100"
                            name="custom_commission_rate" value="{{ old('custom_commission_rate') }}"
                            class="form-control">
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <label class="form-label">Starts At</label>
                        <input type="date" name="starts_at" value="{{ old('starts_at') }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ends At</label>
                        <input type="date" name="ends_at" value="{{ old('ends_at') }}" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-primary" type="submit">Create Campaign</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        // Copy link helper
        function copyLink(inputId, btn) {
            const input = document.getElementById(inputId);
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand('copy');
            btn.textContent = 'Copied!';
            setTimeout(() => btn.textContent = 'Copy', 1500);
        }

        // Enhance table if there are rows
        $(function() {
            if ($('#campaignsTable tbody tr').length) {
                $('#campaignsTable').DataTable({
                    paging: true,
                    searching: true,
                    lengthChange: true,
                    pageLength: 10,
                    order: [
                        [0, 'asc']
                    ],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search campaigns..."
                    }
                });
            }
        });
    </script>
@endsection
