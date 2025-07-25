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
        @include('admin.dashboard.partials.hero')

        <div class="dashboard-grid">

            {{-- Metric Cards Left (Tickets and Schools) --}}
            <div class="grid-span-4">
                <div class="row">
                    <div class="col-6 mb-4">
                        <a href="{{ route('user.tickets.index') }}">
                            <x-dashboard.card-metric icon="{{ asset('assets/img/icons/unicons/support.png') }}"
                                title="Active Tickets" :amount="$activeTickets" trendColor="warning" trendIcon="bx-support"
                                trendPercent=""
                                tooltip="Tickets that require admin attention: open, in progress, or awaiting response." />
                        </a>
                    </div>
                    <div class="col-6 mb-4">
                        <x-dashboard.card-metric icon="{{ asset('assets/img/icons/unicons/building.png') }}"
                            title="Total Schools" amount="{{ $totalSchools }}" trendColor="primary" trendIcon="bx-buildings"
                            trendPercent="All Time" compact="true" />
                    </div>
                </div>
            </div>

            {{-- Notifications spanning 2 rows --}}
            <div class="grid-span-8 grid-row-span-2">
                @include('admin.dashboard.partials.notifications')
            </div>

            {{-- Right Side Cards --}}
            <div class="grid-span-4">
                <div class="row">
                    <div class="col-6 mb-4">
                        <x-dashboard.card-metric icon="{{ asset('assets/img/icons/unicons/domain.png') }}"
                            title="Custom Domains Linked" amount="{{ $customDomainsCount }}"
                            trendColor="{{ $domainTrendColor }}" trendIcon="{{ $domainTrendIcon }}"
                            trendPercent="{{ $domainGrowth }}%" compact="true" />
                    </div>
                    <div class="col-6 mb-4">
                        <x-dashboard.card-metric icon="{{ asset('assets/img/icons/unicons/invoice.png') }}"
                            title="Invoices Generated" amount="{{ $totalInvoices }}" trendColor="{{ $trendColor }}"
                            trendIcon="{{ $trendIcon }}" trendPercent="{{ $invoiceGrowth }}%" compact="true" />
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            {{-- Profile Report --}}
            <div class="col-12 col-md-4 mb-4">
                @include('admin.dashboard.partials.profile-report')
            </div>

            {{-- Total Revenue --}}
            <div class="col-12 col-md-4 mb-4">
                @include('admin.dashboard.partials.total-revenue')
            </div>

            {{-- Order Statistics --}}
            <div class="col-12 col-md-4 mb-4">
                @include('admin.dashboard.partials.order-statistics')
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="row">
            <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-6">
                @include('admin.dashboard.partials.ticket-report')
            </div>

            <div class="col-md-6 col-lg-4 order-1 mb-6">
                @include('admin.dashboard.partials.recent-activity')
            </div>

            <div class="col-md-6 col-lg-4 order-2 mb-6">
                @include('admin.dashboard.partials.transactions')
            </div>
        </div>
    @endsection

    @push('styles')
        <style>
            .dashboard-grid {
                display: grid;
                grid-template-columns: repeat(12, 1fr);
                grid-auto-rows: minmax(150px, auto);
                gap: 1.5rem;
            }

            .grid-span-8 {
                grid-column: span 8;
            }

            .grid-span-4 {
                grid-column: span 4;
            }

            .grid-row-span-2 {
                grid-row: span 2;
            }

            .timeline {
                list-style: none;
                padding: 0;
                margin: 0;
                position: relative;
            }

            .timeline:before {
                content: '';
                position: absolute;
                top: 0;
                bottom: 0;
                width: 2px;
                background: #d9dee3;
                left: 18px;
                margin-left: -1px;
            }

            .timeline-item {
                position: relative;
                margin-left: 40px;
            }

            .timeline-point {
                position: absolute;
                left: -22px;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                text-align: center;
                line-height: 36px;
                font-size: 18px;
                background-color: #d9dee3;
                color: #fff;
            }

            .timeline-point-primary {
                background-color: #696cff;
            }

            .timeline-point-info {
                background-color: #03c3ec;
            }

            .timeline-point-secondary {
                background-color: #8592a3;
            }

            .timeline-event {
                padding-left: 10px;
            }
        </style>
    @endpush
