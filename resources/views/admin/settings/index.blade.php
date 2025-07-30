@extends('layouts/contentNavbarLayout')

@section('title', 'System Settings')

@section('content')
    <div class="container-xxl">
        <h4 class="fw-bold mb-4">System Settings</h4>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#branding">Branding</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#contact">Contact</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#email">Email</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#invoice">Invoice</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#affiliate">Affiliate</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#other">Other</a></li>
        </ul>

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="tab-content card p-3">
                {{-- Branding --}}
                <div class="tab-pane fade show active" id="branding">
                    <div class="row g-4">
                        @foreach ([
            'app_logo_url' => 'App Logo',
            'favicon_url' => 'Favicon',
            'background_url' => 'Background',
            'watermark_url' => 'Watermark',
        ] as $key => $label)
                            <div class="col-md-6">
                                <label class="form-label">{{ $label }}</label><br>
                                @if (!empty($settings[$key]))
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $settings[$key]) }}" alt="{{ $label }}"
                                            class="img-thumbnail" width="100">
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="remove_{{ $key }}"
                                            id="remove_{{ $key }}">
                                        <label class="form-check-label" for="remove_{{ $key }}">Remove</label>
                                    </div>
                                @endif
                                <input type="file" name="{{ str_replace('_url', '_file', $key) }}" class="form-control">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Contact --}}
                <div class="tab-pane fade" id="contact">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Support Email</label>
                            <input type="email" name="support_email" class="form-control"
                                value="{{ $settings['support_email'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-control"
                                value="{{ $settings['company_name'] ?? '' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Company Address</label>
                            <input type="text" name="company_address" class="form-control"
                                value="{{ $settings['company_address'] ?? '' }}">
                        </div>
                    </div>
                </div>

                {{-- Email --}}
                <div class="tab-pane fade" id="email">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Email From Name</label>
                            <input type="text" name="email_from_name" class="form-control"
                                value="{{ $settings['email_from_name'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email From Address</label>
                            <input type="email" name="email_from_address" class="form-control"
                                value="{{ $settings['email_from_address'] ?? '' }}">
                        </div>
                    </div>
                </div>

                {{-- Invoice --}}
                <div class="tab-pane fade" id="invoice">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label">Invoice Prefix</label>
                            <input type="text" name="invoice_prefix" class="form-control"
                                value="{{ $settings['invoice_prefix'] ?? '' }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Invoice Terms</label>
                            <input type="text" name="invoice_terms" class="form-control"
                                value="{{ $settings['invoice_terms'] ?? '' }}">
                        </div>
                    </div>
                </div>

                {{-- Affiliate (Dynamic Levels) --}}
                <div class="tab-pane fade" id="affiliate">
                    <div class="row g-4">
                        {{-- Default Commission (fallback) --}}
                        <div class="col-md-4">
                            <label class="form-label">
                                Default Commission %
                                <span class="text-muted" data-bs-toggle="tooltip"
                                    title="Used only if no level-based rate applies.">ⓘ</span>
                            </label>
                            <input type="number" step="0.01" name="affiliate_commission_percentage"
                                class="form-control" value="{{ $settings['affiliate_commission_percentage'] ?? '30' }}">
                        </div>
                    </div>

                    <hr>

                    @php
                        // Read levels from JSON; default to 1–3 if empty.
                        $levelsJson = $settings['affiliate_commission_levels'] ?? '{}';
                        $levelsArr = json_decode($levelsJson, true);
                        if (!is_array($levelsArr) || empty($levelsArr)) {
                            $levelsArr = [1 => 0.1, 2 => 0.05, 3 => 0.02];
                        }
                        // Convert decimals (0.10) to display percent (10)
                        $displayLevels = [];
                        foreach ($levelsArr as $lvl => $dec) {
                            $displayLevels[(int) $lvl] = is_numeric($dec) ? ($dec > 1 ? $dec : $dec * 100) : 0;
                        }
                        ksort($displayLevels);
                    @endphp

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="mb-0">Commission Levels</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addLevelBtn">Add Level</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle" id="levelsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 140px;">Level</th>
                                    <th>Percentage (%)</th>
                                    <th style="width: 80px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($displayLevels as $level => $percent)
                                    <tr>
                                        <td>
                                            <input type="number" min="1" step="1"
                                                class="form-control level-input" name="level_keys[]"
                                                value="{{ $level }}">
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" step="0.01" min="0"
                                                    class="form-control pct-input"
                                                    name="commission_levels[{{ $level }}]"
                                                    value="{{ rtrim(rtrim(number_format((float) $percent, 2, '.', ''), '0'), '.') }}">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <small class="text-muted">This will be saved as a decimal (e.g., 10% →
                                                0.10).</small>
                                        </td>
                                        <td class="text-end">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger remove-level-btn">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <template id="levelRowTemplate">
                        <tr>
                            <td>
                                <input type="number" min="1" step="1" class="form-control level-input"
                                    name="level_keys[]" value="">
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control pct-input"
                                        name="" value="">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">This will be saved as a decimal (e.g., 12.5% → 0.125).</small>
                            </td>
                            <td class="text-end">
                                <button type="button"
                                    class="btn btn-sm btn-outline-danger remove-level-btn">Remove</button>
                            </td>
                        </tr>
                    </template>
                </div>

                {{-- Other --}}
                <div class="tab-pane fade" id="other">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label">Tax %</label>
                            <input type="number" step="0.01" name="tax_percentage" class="form-control"
                                value="{{ $settings['tax_percentage'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Default Currency</label>
                            <input type="text" name="default_currency" class="form-control"
                                value="{{ $settings['default_currency'] ?? 'NGN' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Timezone</label>
                            <input type="text" name="timezone" class="form-control"
                                value="{{ $settings['timezone'] ?? 'Africa/Lagos' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Homepage URL</label>
                            <input type="url" name="homepage_url" class="form-control"
                                value="{{ $settings['homepage_url'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Terms URL</label>
                            <input type="url" name="terms_url" class="form-control"
                                value="{{ $settings['terms_url'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Privacy Policy URL</label>
                            <input type="url" name="privacy_url" class="form-control"
                                value="{{ $settings['privacy_url'] ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Enable BS tooltips
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
            new bootstrap.Tooltip(el);
        });

        (function() {
            const tableBody = document.querySelector('#levelsTable tbody');
            const addBtn = document.getElementById('addLevelBtn');
            const tpl = document.getElementById('levelRowTemplate');

            // Helper: recompute input names based on Level column
            const syncNames = (row) => {
                const levelInput = row.querySelector('.level-input');
                const pctInput = row.querySelector('.pct-input');
                const lvl = (levelInput.value || '').trim();

                // When level number is present, bind name as commission_levels[<level>]
                if (lvl) {
                    pctInput.name = `commission_levels[${lvl}]`;
                } else {
                    pctInput.name = '';
                }
            };

            // Add new level row
            addBtn?.addEventListener('click', function() {
                const clone = tpl.content.cloneNode(true);
                tableBody.appendChild(clone);
            });

            // Delegate: handle remove and name sync
            tableBody?.addEventListener('input', function(e) {
                if (e.target.classList.contains('level-input')) {
                    const row = e.target.closest('tr');
                    syncNames(row);
                }
            });

            tableBody?.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-level-btn')) {
                    e.preventDefault();
                    const row = e.target.closest('tr');
                    row?.remove();
                }
            });

            // Initialize names for existing rows
            tableBody?.querySelectorAll('tr').forEach(syncNames);
        })();
    </script>
@endpush
