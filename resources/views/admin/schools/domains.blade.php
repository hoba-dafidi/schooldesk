@extends('layouts/contentNavbarLayout')

@section('title', 'Manage Domains')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold mb-4">Manage Domains</h4>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover table-striped align-middle" id="domainsTable">
                    <thead class="table-light">
                        <tr>
                            <th>School</th>
                            <th>Type</th>
                            <th>Link</th>
                            <th>Status</th>
                            <th>Verified</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subdomains as $sub)
                            <tr>
                                <td>{{ $sub->school->name ?? '-' }}</td>
                                <td><span class="badge bg-secondary">Subdomain</span></td>
                                <td>{{ $sub->link }}</td>
                                <td><span
                                        class="badge bg-{{ $sub->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($sub->status) }}</span>
                                </td>
                                <td>{!! $sub->is_verified ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>' !!}</td>
                                <td>{{ $sub->created_at->format('d M, Y') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-info view-domain-btn"
                                            data-domain='@json($sub)' data-type="subdomain">
                                            <i class="bx bx-show"></i>
                                        </button>
                                        <form action="{{ route('admin.subdomains.destroy', $sub->id) }}" method="POST"
                                            onsubmit="return confirm('Delete this subdomain?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        @foreach ($customDomains as $custom)
                            <tr>
                                <td>{{ $custom->school->name ?? '-' }}</td>
                                <td><span class="badge bg-primary">Custom</span></td>
                                <td>{{ $custom->link }}</td>
                                <td><span
                                        class="badge bg-{{ $custom->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($custom->status) }}</span>
                                </td>
                                <td>{!! $custom->is_verified
                                    ? '<span class="badge bg-success">Yes</span>'
                                    : '<span class="badge bg-danger">No</span>' !!}</td>
                                <td>{{ $custom->created_at->format('d M, Y') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-info view-domain-btn"
                                            data-domain='@json($custom)' data-type="custom">
                                            <i class="bx bx-show"></i>
                                        </button>
                                        <form action="{{ route('admin.custom-domains.destroy', $custom->id) }}"
                                            method="POST" onsubmit="return confirm('Delete this custom domain?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- View Domain Modal --}}
    <div class="modal fade" id="viewDomainModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content shadow-lg border-0 rounded-3">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Domain Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p><strong>Type:</strong> <span class="modal-type badge"></span></p>
                    <p><strong>Domain:</strong> <span class="modal-domain fw-bold"></span></p>
                    <p><strong>Status:</strong> <span class="modal-status badge"></span></p>
                    <p><strong>Verified:</strong> <span class="modal-verified badge"></span></p>
                    <p><strong>School:</strong> <span class="modal-school text-muted"></span></p>
                    <p><strong>Created:</strong> <span class="modal-created small text-muted"></span></p>
                </div>
                <div class="modal-footer bg-light px-3 py-2">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.querySelector('#domainsTable');
            if (table) {
                new DataTable(table, {
                    responsive: true,
                    perPage: 10,
                });
            }

            const modal = document.getElementById('viewDomainModal');

            document.querySelectorAll('.view-domain-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const domain = JSON.parse(this.getAttribute('data-domain'));
                    const type = this.getAttribute('data-type');

                    modal.querySelector('.modal-type').textContent = type === 'custom' ?
                        'Custom Domain' : 'Subdomain';
                    modal.querySelector('.modal-type').className = 'badge bg-' + (type ===
                        'custom' ? 'primary' : 'secondary') + ' modal-type';

                    modal.querySelector('.modal-domain').textContent = domain.link;
                    modal.querySelector('.modal-status').textContent = domain.status;
                    modal.querySelector('.modal-status').className = 'badge bg-' + (domain
                        .status === 'active' ? 'success' : 'secondary') + ' modal-status';

                    modal.querySelector('.modal-verified').textContent = domain.is_verified ?
                        'Verified' : 'Unverified';
                    modal.querySelector('.modal-verified').className = 'badge bg-' + (domain
                        .is_verified ? 'success' : 'danger') + ' modal-verified';

                    modal.querySelector('.modal-school').textContent = domain.school?.name ?? '—';
                    modal.querySelector('.modal-created').textContent = new Date(domain.created_at)
                        .toLocaleDateString();

                    new bootstrap.Modal(modal).show();
                });
            });
        });
    </script>
@endpush
