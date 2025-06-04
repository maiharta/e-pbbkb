<!-- filepath: c:\Users\Admin\Documents\project\maiharta\e-pbbkb\resources\views\pages\dashboard\index.blade.php -->
@extends('layouts.dashboard-base')

@push('styles')
    <style>
        .dashboard-card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .bg-soft-primary {
            background-color: rgba(67, 94, 190, 0.15);
        }

        .bg-soft-success {
            background-color: rgba(40, 199, 111, 0.15);
        }

        .bg-soft-warning {
            background-color: rgba(255, 159, 67, 0.15);
        }

        .bg-soft-danger {
            background-color: rgba(234, 84, 85, 0.15);
        }

        .text-value {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .text-label {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .chart-container {
            min-height: 350px;
        }

        .badge-trend {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #435ebe;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .section-icon {
            margin-right: 10px;
            display: flex;
            align-items: center;
            line-height: 1;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h3>Dashboard</h3>
                    <p class="text-muted">Monitoring PBBKB Provinsi Bali</p>
                </div>
            </div>
        </div>

        <section class="section">
            <!-- Summary Cards -->
            <div class="row">
                <div class="col-12 col-md-6 col-lg-3 mb-4">
                    <div class="card dashboard-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="card-icon bg-soft-primary">
                                    <i class="isax isax-money fs-3 text-primary"></i>
                                </div>
                            </div>
                            <h5 class="text-value"
                                id="totalPBBKB">Rp 5,8 M</h5>
                            <p class="text-label mb-0">Total PBBKB Terkumpul</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3 mb-4">
                    <div class="card dashboard-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="card-icon bg-soft-success">
                                    <i class="isax isax-people fs-3 text-primary"></i>
                                </div>
                            </div>
                            <h5 class="text-value"
                                id="totalPelaporan">342</h5>
                            <p class="text-label mb-0">Wapu Terverifikasi</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3 mb-4">
                    <div class="card dashboard-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="card-icon bg-soft-warning">
                                    <i class="isax isax-verify fs-3 text-primary"></i>
                                </div>
                            </div>
                            <h5 class="text-value"
                                id="pendingPelaporan">28</h5>
                            <p class="text-label mb-0">Penginputan Terverikasi</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3 mb-4">
                    <div class="card dashboard-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="card-icon bg-soft-danger">
                                    <i class="isax isax-clock fs-3 text-primary"></i>
                                </div>
                            </div>
                            <h5 class="text-value"
                                id="revisedPelaporan">12</h5>
                            <p class="text-label mb-0">Penginputan Berjalan</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 ">
                    <div class="card dashboard-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="section-title mb-0">
                                <span class="section-icon"><i class="bi bi-bar-chart"></i></span>
                                Realisasi PBBKB
                            </h5>
                            <div class="d-flex align-items-center">
                                <label class="me-2"
                                       for="yearFilter">Tahun:</label>
                                <select class="form-select form-select-sm"
                                        id="yearFilter"
                                        style="width: 100px;">
                                    <!-- Years will be dynamically populated -->
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container"
                                 id="pbbkbChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        $(document).ready(function() {
            // Populate year dropdown (current year and 5 years back)
            populateYearDropdown();

            // Initialize chart
            initPbbkbChart();

            // Fetch dashboard statistics with default year (current year)
            fetchDashboardData(new Date().getFullYear());

            // Add event listener for year change
            $('#yearFilter').change(function() {
                const selectedYear = $(this).val();
                fetchDashboardData(selectedYear);
            });
        });

        function populateYearDropdown() {
            const currentYear = new Date().getFullYear();
            const select = $('#yearFilter');

            // Add past 5 years and current year
            for (let year = currentYear; year >= currentYear - 5; year--) {
                select.append($('<option>', {
                    value: year,
                    text: year
                }));
            }
        }

        function initPbbkbChart() {
            // Chart options
            var options = {
                series: [{
                    name: 'Total PBBKB',
                    data: [] // Will be populated from AJAX
                }],
                chart: {
                    type: 'line',
                    height: 350,
                    zoom: {
                        enabled: true
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                colors: ['#435ebe'],
                grid: {
                    borderColor: '#e0e0e0',
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    },
                },
                markers: {
                    size: 5,
                    hover: {
                        size: 7
                    }
                },
                xaxis: {
                    categories: [], // Will be populated from AJAX
                    title: {
                        text: 'Bulan'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Total PBBKB (Rp)'
                    },
                    labels: {
                        formatter: function(val) {
                            return 'Rp ' + formatCurrency(val);
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + formatCurrency(val);
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                },
                theme: {
                    mode: 'light'
                }
            };

            // Create chart instance
            var chart = new ApexCharts(document.querySelector("#pbbkbChart"), options);
            chart.render();

            // Store chart instance for later updates
            window.pbbkbChart = chart;
        }

        function fetchDashboardData(year) {
            $.ajax({
                url: '/api/dashboard/stats',
                method: 'GET',
                data: {
                    year: year
                },
                success: function(response) {
                    // Update summary cards
                    $('#totalPBBKB').text('Rp ' + response.formattedPbbkb);
                    $('#totalPelaporan').text(response.totalWapu);
                    $('#pendingPelaporan').text(response.verifiedInputs);
                    $('#revisedPelaporan').text(response.ongoingInputs);

                    // Update chart data
                    updatePbbkbChart(response.chartData);
                },
                error: function(xhr) {
                    console.error('Error fetching dashboard data:', xhr);
                }
            });
        }

        function updatePbbkbChart(chartData) {
            if (!chartData || !chartData.months || !chartData.values) return;

            window.pbbkbChart.updateOptions({
                xaxis: {
                    categories: chartData.months
                },
                series: [{
                    name: 'Total PBBKB',
                    data: chartData.values
                }]
            });
        }

        function formatCurrency(value) {
            // Format number to Indonesian currency format
            return new Intl.NumberFormat('id-ID').format(value);
        }
    </script>
@endpush
