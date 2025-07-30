@extends('layouts/contentNavbarLayout')

@section('title', 'Commission Management')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Affiliate Commissions</h4>
            <a href="{{ route('admin.commissions.export') }}" class="btn btn-outline-success">
                <i class="fas fa-file-export me-1"></i> Export to Excel
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-striped table-hover align-middle w-100" id="commissionsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>User</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('vendor-script')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
@endpush

@push('page-script')
    <script>
        $(document).ready(function() {
            const table = $('#commissionsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.commissions.data') }}',
                columns: [{
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'status_label',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_name',
                        name: 'user.name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Approve commission
            $(document).on('click', '.approve-btn', function() {
                let id = $(this).data('id');
                $.post(`/admin/commissions/${id}/approve`, {
                    _token: '{{ csrf_token() }}'
                }, function(res) {
                    if (res.success) table.ajax.reload(null, false);
                });
            });

            // Mark as paid
            $(document).on('click', '.pay-btn', function() {
                let id = $(this).data('id');
                $.post(`/admin/commissions/${id}/paid`, {
                    _token: '{{ csrf_token() }}'
                }, function(res) {
                    if (res.success) table.ajax.reload(null, false);
                });
            });
        });
    </script>
@endpush
