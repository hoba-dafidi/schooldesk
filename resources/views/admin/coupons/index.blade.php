@extends('layouts/contentNavbarLayout')

@section('title', 'Manage Coupons')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <x-flash-messages />

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">Coupons</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#couponModal" onclick="openCouponModal()">New
                Coupon</button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped" id="couponTable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Used</th>
                            <th>Expires</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coupons as $coupon)
                            <tr>
                                <td>{{ $coupon->code }}</td>
                                <td><span class="badge bg-info text-uppercase">{{ $coupon->type }}</span></td>
                                <td>
                                    {{ $coupon->type === 'percentage' ? $coupon->amount . '%' : '₦' . number_format($coupon->amount, 2) }}
                                </td>
                                <td>{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}</td>
                                <td>{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $coupon->active ? 'success' : 'secondary' }}">
                                        {{ $coupon->active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="editCoupon(@json($coupon))">Edit</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-3">
                <div class="modal-header bg-primary text-white align-items-center">
                    <h5 class="modal-title" id="couponModalLabel">Create Coupon</h5>
                    <button type="button" class="btn-close btn-close-white mt-2 me-2" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.coupons.store') }}" id="couponForm">
                    @csrf
                    <div class="modal-body row g-3 p-3">
                        <input type="hidden" name="id" id="couponId">

                        <div class="col-md-6">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" name="code" id="code" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="type" id="type" required>
                                <option value="percentage">Percentage</option>
                                <option value="fixed">Fixed</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="amount"
                                id="amount" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Usage Limit (optional)</label>
                            <input type="number" min="1" class="form-control" name="usage_limit" id="usage_limit">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Expires At (optional)</label>
                            <input type="date" class="form-control" name="expires_at" id="expires_at">
                        </div>

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="active" id="active" checked>
                                <label class="form-check-label" for="active">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light p-2">
                        <button type="button" class="btn btn-outline-secondary m-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary m-2">Save Coupon</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const modal = new bootstrap.Modal(document.getElementById('couponModal'));

        function openCouponModal() {
            document.getElementById('couponForm').reset();
            document.getElementById('couponId').value = '';
            document.getElementById('couponModalLabel').innerText = 'Create Coupon';
        }

        function editCoupon(coupon) {
            document.getElementById('couponForm').action = `/admin/coupons/${coupon.id}`;
            document.getElementById('couponId').value = coupon.id;
            document.getElementById('code').value = coupon.code;
            document.getElementById('type').value = coupon.type;
            document.getElementById('amount').value = coupon.amount;
            document.getElementById('usage_limit').value = coupon.usage_limit ?? '';
            document.getElementById('expires_at').value = coupon.expires_at ?? '';
            document.getElementById('active').checked = coupon.active;
            document.getElementById('couponModalLabel').innerText = 'Edit Coupon';
            modal.show();
        }

        $(document).ready(function() {
            $('#couponTable').DataTable();
        });
    </script>
@endpush
