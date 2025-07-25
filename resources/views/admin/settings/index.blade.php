@extends('layouts/contentNavbarLayout')

@section('title', 'System Settings')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold mb-4">System Settings</h4>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="accordion" id="settingsAccordion">

                {{-- GENERAL SETTINGS --}}
                <div class="accordion-item">
                    <h2 class="accordion-header" id="generalSettingsHeader">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#generalSettings">
                            General Settings
                        </button>
                    </h2>
                    <div id="generalSettings" class="accordion-collapse collapse show" data-bs-parent="#settingsAccordion">
                        <div class="accordion-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label">App Name</label>
                                <input type="text" name="app_name" class="form-control"
                                    value="{{ $settings['app_name'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Default Currency</label>
                                <input type="text" name="default_currency" class="form-control"
                                    value="{{ $settings['default_currency'] ?? 'NGN' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Timezone</label>
                                <input type="text" name="timezone" class="form-control"
                                    value="{{ $settings['timezone'] ?? 'Africa/Lagos' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Support Email</label>
                                <input type="email" name="support_email" class="form-control"
                                    value="{{ $settings['support_email'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BRANDING SETTINGS --}}
                <div class="accordion-item">
                    <h2 class="accordion-header" id="brandingSettingsHeader">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#brandingSettings">
                            Branding Settings
                        </button>
                    </h2>
                    <div id="brandingSettings" class="accordion-collapse collapse" data-bs-parent="#settingsAccordion">
                        <div class="accordion-body row g-3">

                            {{-- Company Info --}}
                            <div class="col-md-6">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control"
                                    value="{{ $settings['company_name'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company Address</label>
                                <input type="text" name="company_address" class="form-control"
                                    value="{{ $settings['company_address'] ?? '' }}">
                            </div>

                            {{-- Homepage URL --}}
                            <div class="col-md-6">
                                <label class="form-label">Homepage URL</label>
                                <input type="text" name="homepage_url" class="form-control"
                                    value="{{ $settings['homepage_url'] ?? '' }}">
                            </div>

                            {{-- Homepage URL --}}
                            <div class="col-md-6">
                                {{-- <label class="form-label">Homepage URL</label>
                                <input type="text" name="homepage_url" class="form-control"
                                    value="{{ $settings['homepage_url'] ?? '' }}"> --}}
                            </div>

                            {{-- App Logo --}}
                            <div class="col-md-6">
                                <label class="form-label">App Logo</label>
                                @if (!empty($settings['app_logo_url']))
                                    <div class="mb-2 d-flex align-items-center gap-2">
                                        <img src="{{ asset('storage/' . $settings['app_logo_url']) }}" alt="App Logo"
                                            height="0" id="preview_app_logo">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_app_logo"
                                                value="1" id="removeAppLogo">
                                            <label class="form-check-label" for="removeAppLogo">Remove</label>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" name="app_logo_file" class="form-control"
                                    onchange="previewImage(this, 'preview_app_logo_new')">
                                <small class="text-muted">Max 1MB. JPG/PNG only.</small>
                                <img id="preview_app_logo_new" class="mt-2" style="max-height: 60px;" hidden>
                            </div>

                            {{-- Favicon --}}
                            <div class="col-md-6">
                                <label class="form-label">Favicon</label>
                                @if (!empty($settings['favicon_url']))
                                    <div class="mb-2 d-flex align-items-center gap-2">
                                        <img src="{{ asset('storage/' . $settings['favicon_url']) }}" alt="Favicon"
                                            height="60" id="preview_favicon">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_favicon"
                                                value="1" id="removeFavicon">
                                            <label class="form-check-label" for="removeFavicon">Remove</label>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" name="favicon_file" class="form-control"
                                    onchange="previewImage(this, 'preview_favicon_new')">
                                <small class="text-muted">Max 256KB. ICO/PNG only.</small>
                                <img id="preview_favicon_new" class="mt-2" style="max-height: 40px;" hidden>
                            </div>

                            {{-- Background Image --}}
                            <div class="col-md-6">
                                <label class="form-label">Login Background</label>
                                @if (!empty($settings['background_url']))
                                    <div class="mb-2 d-flex align-items-center gap-2">
                                        <img src="{{ asset('storage/' . $settings['background_url']) }}" alt="Background"
                                            height="60" id="preview_background">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_background"
                                                value="1" id="removeBackground">
                                            <label class="form-check-label" for="removeBackground">Remove</label>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" name="background_file" class="form-control"
                                    onchange="previewImage(this, 'preview_background_new')">
                                <small class="text-muted">Max 2MB. JPG/PNG only.</small>
                                <img id="preview_background_new" class="mt-2" style="max-height: 60px;" hidden>
                            </div>

                            {{-- Watermark --}}
                            <div class="col-md-6">
                                <label class="form-label">Watermark</label>
                                @if (!empty($settings['watermark_url']))
                                    <div class="mb-2 d-flex align-items-center gap-2">
                                        <img src="{{ asset('storage/' . $settings['watermark_url']) }}" alt="Watermark"
                                            height="60" id="preview_watermark">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_watermark"
                                                value="1" id="removeWatermark">
                                            <label class="form-check-label" for="removeWatermark">Remove</label>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" name="watermark_file" class="form-control"
                                    onchange="previewImage(this, 'preview_watermark_new')">
                                <small class="text-muted">Max 1MB. PNG only.</small>
                                <img id="preview_watermark_new" class="mt-2" style="max-height: 60px;" hidden>
                            </div>

                        </div>
                    </div>
                </div>



                {{-- EMAIL SETTINGS --}}
                <div class="accordion-item">
                    <h2 class="accordion-header" id="emailSettingsHeader">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#emailSettings">
                            Email Settings
                        </button>
                    </h2>
                    <div id="emailSettings" class="accordion-collapse collapse" data-bs-parent="#settingsAccordion">
                        <div class="accordion-body row g-3">
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
                </div>

                {{-- INVOICE SETTINGS --}}
                <div class="accordion-item">
                    <h2 class="accordion-header" id="invoiceSettingsHeader">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#invoiceSettings">
                            Invoice & Tax Settings
                        </button>
                    </h2>
                    <div id="invoiceSettings" class="accordion-collapse collapse" data-bs-parent="#settingsAccordion">
                        <div class="accordion-body row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Invoice Prefix</label>
                                <input type="text" name="invoice_prefix" class="form-control"
                                    value="{{ $settings['invoice_prefix'] ?? 'INV-' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tax Percentage (%)</label>
                                <input type="number" name="tax_percentage" step="0.01" class="form-control"
                                    value="{{ $settings['tax_percentage'] ?? '0' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Invoice Terms</label>
                                <input type="text" name="invoice_terms" class="form-control"
                                    value="{{ $settings['invoice_terms'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function previewImage(input, previewId) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(previewId);
                img.src = e.target.result;
                img.hidden = false;
            };
            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
@endpush
