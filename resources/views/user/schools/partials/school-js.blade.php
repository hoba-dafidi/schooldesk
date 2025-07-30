<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
    let currentSubscription = null;
    const gateways = @json($gateways);
    const plans = @json($plans);
    const TAX_RATE = {{ settings('tax_percentage') ?? 0 }};

    function previewLogo(event) {
        const input = event.target;
        const preview = document.getElementById('logoPreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '';
            preview.classList.add('d-none');
        }
    }

    function resetSchoolForm() {
        const form = document.getElementById('schoolForm');
        form.reset();
        form.action = "{{ route('user.schools.store') }}";
        document.getElementById('school_id').value = '';
        document.getElementById('schoolModalLabel').textContent = 'Create School';
        form.querySelector('[name="_method"]')?.remove();
        document.getElementById('logoPreview').classList.add('d-none');
    }

    function editSchool(school) {
        const form = document.getElementById('schoolForm');
        form.action = `/user/schools/${school.id}`;
        form.querySelector('[name="_method"]')?.remove();
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);

        document.getElementById('school_id').value = school.id;
        document.getElementById('name').value = school.name;
        document.getElementById('email').value = school.email ?? '';
        document.getElementById('phone').value = school.phone ?? '';
        document.getElementById('address').value = school.address ?? '';
        document.getElementById('type').value = school.type ?? '';
        document.getElementById('established_year').value = school.established_year ?? '';
        document.getElementById('about').value = school.about ?? '';
        document.getElementById('city').value = school.city ?? '';
        document.getElementById('state').value = school.state ?? '';
        document.getElementById('country').value = school.country ?? '';
        document.getElementById('status').value = school.status;
        document.getElementById('is_paid_plan').value = school.is_paid_plan ? '1' : '0';
        document.getElementById('schoolModalLabel').textContent = 'Edit School';

        if (school.logo_url) {
            const preview = document.getElementById('logoPreview');
            preview.src = school.logo_url;
            preview.classList.remove('d-none');
        }
    }


    function showToast(type, message) {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: 'right',
            backgroundColor: type === 'success' ? "#28a745" : "#dc3545",
        }).showToast();
    }

    function calculateTaxAndTotal() {
        const amountInput = document.querySelector('input[name="amount"]');
        const taxInput = document.querySelector('input[name="tax_amount"]');
        const totalInput = document.getElementById('totalWithTax');

        const amount = parseFloat(amountInput.value) || 0;
        const tax = (amount * TAX_RATE / 100).toFixed(2);
        const total = (amount + parseFloat(tax)).toFixed(2);

        taxInput.value = tax;
        totalInput.value = total;
    }


    document.addEventListener('DOMContentLoaded', function() {


        $('#schoolsTable').DataTable({
            responsive: true,
            pageLength: 10,
            autoWidth: false,
            columnDefs: [{
                orderable: false,
                targets: -1
            }]
        });

        new bootstrap.Tooltip(document.body, {
            selector: '[data-bs-toggle="tooltip"]'
        });

        const amountInput = document.querySelector('input[name="amount"]');
        const planSelect = document.getElementById('planSelect');

        amountInput?.addEventListener('input', calculateTaxAndTotal);
        planSelect?.addEventListener('change', () => {
            setTimeout(calculateTaxAndTotal, 100);
        });

        calculateTaxAndTotal(); // Initial trigger

        $('#subscriptionModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const schoolId = button.data('school-id');
            $('#subscription_school_id').val(schoolId);
            $('#subscriptionMessages').html('');
            $('#planSelect').val('');
            $('#renewBtn, #upgradeBtn').prop('disabled', true);

            updateGatewaySlug();


            // Reset and disable fields
            $('input[name="payer_email"]').val('{{ auth()->user()->email }}').prop('readonly', true);
            $('input[name="amount"]').val('').prop('readonly', true);

            $.ajax({
                url: `/user/schools/${schoolId}/current-subscription`,
                method: 'GET',
                success: function(response) {
                    const s = response.subscription;
                    if (s) {
                        currentSubscription = s;

                        if (currentSubscription.plan_slug === 'free') {
                            $('#cancelSubscriptionBtn').hide();
                        } else {
                            $('#cancelSubscriptionBtn').show();
                        }

                        $('#currentPlanName').text(s.plan_name);
                        $('#currentPlanPrice').text(s.plan_price);

                        const statusSpan = $('#currentStatus');
                        statusSpan.removeClass(
                                'bg-success bg-warning bg-danger bg-secondary text-dark')
                            .text(s.status.charAt(0).toUpperCase() + s.status.slice(1));
                        switch (s.status.toLowerCase()) {
                            case 'active':
                                statusSpan.addClass('bg-success');
                                break;
                            case 'trialing':
                                statusSpan.addClass('bg-warning text-dark');
                                break;
                            case 'cancelled':
                                statusSpan.addClass('bg-danger');
                                break;
                            default:
                                statusSpan.addClass('bg-secondary');
                        }

                        $('#currentStartsAt').text(s.starts_at ?? 'N/A');
                        $('#currentEndsAt').text(s.ends_at ?? 'N/A');

                        $('#currentSubscriptionLabel').show();
                        $('#currentSubscriptionInfo').show().animate({
                            opacity: 1
                        }, 500);
                    } else {
                        $('#currentSubscriptionLabel, #currentSubscriptionInfo').hide();
                    }
                },
                error: function() {
                    $('#currentSubscriptionLabel, #currentSubscriptionInfo').hide();
                }
            });


        });


        $('#planSelect').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const selectedPlanId = selectedOption.val();
            const selectedPlanPrice = selectedOption.text().match(/₦?[\d,\.]+/); // optional price match
            const selectedPlan = plans.find(p => p.id == selectedPlanId);
            const gatewayFields = document.getElementById('gatewayFields');

            if (selectedPlan && selectedPlan.slug === 'free') {
                gatewayFields.style.display = 'none';
            } else {
                gatewayFields.style.display = 'block';
            }

            if (selectedPlanPrice) {
                $('input[name="amount"]').val(selectedPlanPrice[0].replace(/[₦,]/g, '')).prop(
                    'readonly', true);
            }

            if (!selectedPlanId) {
                $('#renewBtn, #upgradeBtn').prop('disabled', true);
                return;
            }

            if (!currentSubscription) {
                $('#upgradeBtn').prop('disabled', false);
                $('#renewBtn').prop('disabled', true);
                return;
            }

            if (selectedPlanId == currentSubscription.plan_id) {
                $('#renewBtn').prop('disabled', false);
                $('#upgradeBtn').prop('disabled', true);
            } else {
                $('#renewBtn').prop('disabled', true);
                $('#upgradeBtn').prop('disabled', false);
            }
        });

        $('#gatewaySelect').on('change', function() {
            updateGatewaySlug();
        });



        // Bind buttons
        $('#renewBtn').on('click', function() {
            handleSubscriptionFlow({
                action: 'Renewing',
                url: `/user/schools/${$('#subscription_school_id').val()}/renew`,
                buttonId: '#renewBtn'
            });
        });

        $('#upgradeBtn').on('click', function() {
            handleSubscriptionFlow({
                action: 'Upgrading',
                url: `/user/schools/${$('#subscription_school_id').val()}/upgrade`,
                buttonId: '#upgradeBtn'
            });
        });



        const cancelBtn = document.getElementById('cancelSubscriptionBtn');

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                const schoolId = document.getElementById('subscription_school_id').value;

                if (!schoolId) return alert('Invalid school selected.');

                if (!confirm('Are you sure you want to cancel the current subscription?')) return;

                fetch(`/user/schools/${schoolId}/cancel`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showToast('success', data.message);
                            $('#subscriptionModal').modal('hide');
                            $('#schoolsTable').DataTable().ajax?.reload(null, false);
                        } else {
                            showToast('error', data.message || 'Failed to cancel subscription.');
                        }
                    })
                    .catch(() => showToast('error', 'Server error occurred.'));
            });


        }
    });

    function handleSubscriptionFlow({
        action,
        url,
        buttonId
    }) {
        const $button = $(buttonId);
        const schoolId = $('#subscription_school_id').val();
        const planId = $('#planSelect').val();
        const payment_gateway_slug = $('#payment_gateway_slug').val();
        const gatewayId = $('#gatewaySelect').val();
        const email = $('input[name="payer_email"]').val();
        const amount = $('input[name="amount"]').val();

        if (!schoolId || !planId || !email || (!gatewayId && planId !== 'free')) {
            alert('Please fill in all required fields.');
            return;
        }

        const plan = plans.find(p => p.id == planId);
        if (!plan) return showToast('error', 'Invalid plan selected.');

        // Handle Free Plan (No Payment Needed)
        if (plan.slug === 'free') {
            $.post({
                url,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    plan_id: planId,
                    school_id: schoolId,
                    gateway_id: null,
                    reference: null
                },
                beforeSend: function() {
                    $button.prop('disabled', true).text(`${action}...`);
                },
                success: function(res) {
                    $button.prop('disabled', false).text(action);
                    if (res.success) {
                        $('#subscriptionModal').modal('hide');
                        showToast('success', res.message);
                    } else {
                        $('#subscriptionMessages').html(
                            `<div class="alert alert-danger">${res.message}</div>`);
                    }
                },
                error: function() {
                    $button.prop('disabled', false).text(action);
                    $('#subscriptionMessages').html(
                        `<div class="alert alert-danger">Server error occurred.</div>`);
                }
            });
            return;
        }

        // Paid Plan (Invoice Required)
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                plan_id: planId,
                school_id: schoolId,
                gateway_id: gatewayId,
                payment_gateway_slug: payment_gateway_slug
            },
            beforeSend: function() {
                $button.prop('disabled', true).text(`${action}...`);
                $('#subscriptionMessages').html('');
            },
            success: function(res) {
                $button.prop('disabled', false).text(action);

                if (res.success && res.redirect_url) {
                    window.location.href = res.redirect_url;
                } else {
                    $('#subscriptionMessages').html(
                        `<div class="alert alert-danger">${res.message || 'Failed to create invoice.'}</div>`
                    );
                }
            },
            error: function(xhr) {
                $button.prop('disabled', false).text(action);
                let message = 'Server error occurred.';
                if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }
                $('#subscriptionMessages').html(
                    `<div class="alert alert-danger">${message}</div>`
                );
            }
        });
    }

    function updateGatewaySlug() {
        const selected = $('#gatewaySelect').find(':selected');
        const slug = selected.data('slug') || selected.val();
        $('#payment_gateway_slug').val(slug);
    }
</script>
