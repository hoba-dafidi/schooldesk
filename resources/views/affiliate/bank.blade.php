@extends('layouts/contentNavbarLayout')

@section('content')
    <div class="container">
        <h4 class="mb-4">Bank Account Settings</h4>

        <a href="{{ route('affiliate.dashboard') }}" class="btn btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>

        <form id="bankForm">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="bank" class="form-label">Bank</label>
                <select class="form-select" name="bank_code" id="bank" required>
                    @foreach ($banks as $bank)
                        <option value="{{ $bank['code'] }}" {{ $user->bank_code == $bank['code'] ? 'selected' : '' }}>
                            {{ $bank['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="account_number" class="form-label">Account Number</label>
                <input type="text" name="account_number" class="form-control" id="account_number" maxlength="10"
                    value="{{ $user->account_number }}" required>
            </div>

            <div class="mb-3" id="resolvedNameGroup">
                <label for="resolved_account_name" class="form-label">Account Name</label>
                <input type="text" readonly class="form-control" id="resolved_account_name"
                    value="{{ $user->account_name }}">
            </div>

            <input type="hidden" name="account_name" id="account_name" value="{{ $user->account_name }}">
            <input type="hidden" name="bank_name" id="bank_name">
            <input type="hidden" name="force" id="force" value="false">

            <div id="alertBox" class="alert mt-3 d-none"></div>

            <button type="submit" class="btn btn-primary mt-2" id="saveBtn">Save</button>
        </form>



        <div id="alertBox" class="alert mt-3 d-none"></div>
    </div>
@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let resolving = false;

            // Auto-resolve when 10 digits are entered
            $('#account_number').on('input', function() {
                const accountNumber = $(this).val();
                const bankCode = $('#bank').val();

                if (accountNumber.length === 10 && !resolving && bankCode) {
                    resolving = true;
                    $('#alertBox').addClass('d-none').removeClass(
                        'alert-success alert-danger alert-warning');
                    $('#resolvedNameGroup').addClass('d-none');
                    $('#saveBtn').prop('disabled', true);

                    $.ajax({
                        url: '{{ route('paystack.resolve.account') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            bank_code: bankCode,
                            account_number: accountNumber
                        },
                        success: function(res) {
                            resolving = false;

                            if (res.status) {
                                $('#resolved_account_name').val(res.resolved_name);
                                $('#resolvedNameGroup').removeClass('d-none');
                                $('#account_name').val(res.resolved_name);
                                $('#bank_name').val($('#bank option:selected').text());
                                $('#alertBox').removeClass('d-none alert-danger alert-warning')
                                    .addClass('alert alert-success')
                                    .text(res.message);
                                $('#saveBtn').prop('disabled', false);
                            } else if (res.can_force) {
                                $('#resolved_account_name').val(res.resolved_name);
                                $('#resolvedNameGroup').removeClass('d-none');
                                $('#alertBox').removeClass('d-none alert-success')
                                    .addClass('alert alert-warning').html(`
                                    ${res.message}<br>
                                    Expected: <strong>${res.expected_name}</strong><br>
                                    Resolved: <strong>${res.resolved_name}</strong><br>
                                    Match Score: ${res.match_score}%
                                    <br><br>
                                    <button class="btn btn-sm btn-danger" id="confirmForce">Use Anyway</button>
                                `);
                                $('#confirmForce').on('click', function() {
                                    $('#force').val('true');
                                    $('#saveBtn').prop('disabled', false);
                                });
                            } else {
                                $('#alertBox').removeClass('d-none alert-success alert-warning')
                                    .addClass('alert alert-danger')
                                    .text(res.message);
                            }
                        },
                        error: function() {
                            resolving = false;
                            $('#alertBox').removeClass('d-none alert-success alert-warning')
                                .addClass('alert alert-danger')
                                .text('Something went wrong. Please try again.');
                        }
                    });
                }
            });

            // Submit final save via AJAX
            $('#bankForm').on('submit', function(e) {
                e.preventDefault();
                $('#saveBtn').prop('disabled', true).text('Saving...');
                $('#alertBox').addClass('d-none');

                $.ajax({
                    url: '{{ route('update.bank') }}', // adjust to your actual route
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#saveBtn').prop('disabled', false).text('Save');
                        if (res.status) {
                            $('#alertBox').removeClass('d-none alert-danger alert-warning')
                                .addClass('alert alert-success').text(res.message);
                        } else {
                            $('#alertBox').removeClass('d-none alert-success')
                                .addClass('alert alert-danger').text(res.message);
                        }
                    },
                    error: function() {
                        $('#saveBtn').prop('disabled', false).text('Save');
                        $('#alertBox').removeClass('d-none alert-success')
                            .addClass('alert alert-danger')
                            .text('Something went wrong while saving.');
                    }
                });
            });
        });
    </script>
@endpush
