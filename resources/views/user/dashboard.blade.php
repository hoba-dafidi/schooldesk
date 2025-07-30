@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('page-script')
    @vite('resources/assets/js/dashboards-analytics.js')
@endsection

@section('content')
    <div class="row">
        <x-flash-messages />

        {{-- Hero Section --}}
        @include('_partials.dashboard.hero')

        {{-- Metric Cards --}}
        <div class="col-lg-4 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-6 mb-6">
                    <a href="{{ route('user.tickets.index') }}">
                        <x-dashboard.card-metric icon="{{ asset('assets/img/icons/unicons/support.png') }}"
                            title="Active Tickets" :amount="$activeTickets" trendColor="danger" trendIcon="bx-info" trendPercent=""
                            tooltip="Open or unresolved support tickets" />
                    </a>
                    {{-- <x-dashboard.card-metric icon="assets/img/icons/unicons/chart-success.png" title="Profit"
                        amount="$12,628" trendColor="success" trendIcon="bx-up-arrow-alt" trendPercent="+72.80%" /> --}}
                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-6">
                    <x-dashboard.card-metric icon="assets/img/icons/unicons/building.png" title="Your Schools"
                        amount="{{ $totalSchools }}" trendColor="primary" trendIcon="bx-buildings" trendPercent="Total"
                        compact="true" />

                </div>
            </div>
        </div>

        {{-- Total Revenue --}}
        <div class="col-12 col-xxl-8 order-2 order-md-3 order-xxl-2 mb-6">
            @include('_partials.dashboard.notifications')
        </div>

        {{-- Right Side Cards --}}
        <div class="col-12 col-md-8 col-lg-12 col-xxl-4 order-3 order-md-2">
            <div class="row">
                <div class="col-6 mb-6">
                    <x-dashboard.card-metric icon="assets/img/icons/unicons/domain.png" title="Custom Domains"
                        amount="{{ $customDomainsCount }}" trendColor="{{ $domainTrendColor }}"
                        trendIcon="{{ $domainTrendIcon }}" trendPercent="{{ $domainGrowth }}%" compact="true" />

                </div>
                <div class="col-6 mb-6">
                    <x-dashboard.card-metric icon="assets/img/icons/unicons/invoice.png" title="Total Invoices"
                        amount="{{ $totalInvoices }}" trendColor="{{ $trendColor }}" trendIcon="{{ $trendIcon }}"
                        trendPercent="{{ $invoiceGrowth }}%" compact="true" />

                </div>

                {{-- Profile Report --}}
                <div class="col-12 mb-6">
                    @include('_partials.dashboard.profile-report')
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="row">
        <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-6">
            @include('_partials.dashboard.ticket-report')
        </div>

        <div class="col-md-6 col-lg-4 order-1 mb-6">
            @include('_partials.dashboard.recent-activity')
        </div>

        <div class="col-md-6 col-lg-4 order-2 mb-6">
            @include('_partials.dashboard.transactions')
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .hover-bg-light:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }
    </style>
@endpush
