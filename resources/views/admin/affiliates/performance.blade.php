@extends('layouts/contentNavbarLayout')

@section('title', 'Affiliate Performance')

@push('vendor-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.45.2/dist/apexcharts.css">
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Top Affiliate Performance</h4>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div id="affiliateChart" style="min-height: 400px;"></div>
            </div>
        </div>

    </div>
@endsection

@push('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.2"></script>
@endpush

@push('page-script')
    <script>
        const chartData = @json($chartData); // Expecting ['labels' => [...], 'series' => [...]]

        const options = {
            chart: {
                type: 'bar',
                height: 400,
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: 'Total Commission (₦)',
                data: chartData.series
            }],
            xaxis: {
                categories: chartData.labels,
                title: {
                    text: 'Affiliates'
                }
            },
            yaxis: {
                title: {
                    text: 'Commission (₦)'
                }
            },
            colors: ['#00b894'],
            dataLabels: {
                enabled: true,
                formatter: val => '₦' + val.toLocaleString()
            },
            tooltip: {
                y: {
                    formatter: val => '₦' + val.toLocaleString()
                }
            }
        };

        const chart = new ApexCharts(document.querySelector("#affiliateChart"), options);
        chart.render();
    </script>
@endpush
