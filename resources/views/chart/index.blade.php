@extends('parts.master')

@section('title', __('نمودارها'))

@section('head-styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="row gy-4">
            <div class="col">
                <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample"
                    aria-expanded="false" aria-controls="collapseExample">
                    <i class='bx bx-search me-1'></i> {{ __('جستجو') }}
                </button>
            </div>
            <div class="collapse" id="collapseExample">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('از تاریخ') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="from-date" class="form-control date-mask "
                                            placeholder="1400/01/01">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('تا تاریخ') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="to-date" class="form-control date-mask "
                                            placeholder="1400/01/01">
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end justify-content-start">
                                    <div class="form-group">
                                        <button class="btn btn-success" id="search">{{ __('جستجو') }}</button>
                                        <a href="{{ route('chart') }}" class="btn btn-info"
                                            id="show-all">{{ __('نمایش یک ماه اخیر') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Line Area Chart -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-1">{{ __('مراجعه مشتریان') }}</h5>
                            <small class="text-muted primary-font">{{ __('بر اساس تعداد در هر روز') }}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart" id="lineAreaChart"></div>
                    </div>
                </div>
            </div>
            <!-- /Line Area Chart -->

            <!-- Line Chart -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-1">{{ __('پرداخت') }}</h5>
                            <small class="text-muted primary-font">{{ __('بر اساس مبلغ در هر روز و نوع پرداخت') }}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart" id="lineChart"></div>
                    </div>
                </div>
            </div>
            <!-- /Line Chart -->

            <!-- Donut Chart -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title mb-1">{{ __('نسبت هزینه') }}</h5>
                            <small class="text-muted primary-font">{{ __('هزینه در دسته‌های مختلف') }}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart" id="costChart"></div>
                    </div>
                </div>
            </div>
            <!-- /Donut Chart -->

        </div>
    </div>
    <!--/ Content -->
@stop

@section('footer-scripts')
    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <script type="text/javascript">
        'use strict';

        function makeCharts(from_date = null, to_date = null) {

            $.ajax({
                url: '{{ route('dataChart') }}',
                type: 'GET',
                data: {
                    from_date: from_date,
                    to_date: to_date
                },
                success: function(data) {
                    let sections, costCategories, paymentTypes, categories;
                    sections = data.sections;
                    costCategories = data.costCategories;
                    paymentTypes = data.paymentTypes;
                    categories = data.categories;
                    render(sections, costCategories, paymentTypes, categories);
                },
                error: function(data) {
                    Swal.fire({
                        title: "{{ __('اخطار') }}",
                        icon: "error",
                        text: data.responseJSON.message
                    });
                }
            });

        }

        function render(sections, costCategories, paymentTypes, categories) {
            $(".chart").html('');
            let cardColor, headingColor, labelColor, borderColor, legendColor, radialTrackColor;
            if (isDarkStyle) {
                cardColor = config.colors_dark.cardColor;
                headingColor = config.colors_dark.headingColor;
                labelColor = config.colors_dark.textMuted;
                legendColor = config.colors_dark.bodyColor;
                borderColor = config.colors_dark.borderColor;
                radialTrackColor = '#36435C';
            } else {
                cardColor = config.colors.cardColor;
                headingColor = config.colors.headingColor;
                labelColor = config.colors.textMuted;
                legendColor = config.colors.bodyColor;
                borderColor = config.colors.borderColor;
                radialTrackColor = config.colors_label.secondary;
            }

            Apex.chart = {
                fontFamily: 'inherit',

                locales: [{
                    "name": "fa",
                    "options": {
                        "months": ["ژانویه", "فوریه", "مارس", "آوریل", "می", "ژوئن", "جولای", "آگوست",
                            "سپتامبر", "اکتبر", "نوامبر", "دسامبر"
                        ],
                        "shortMonths": ["ژانویه", "فوریه", "مارس", "آوریل", "می", "ژوئن", "جولای", "آگوست",
                            "سپتامبر", "اکتبر", "نوامبر", "دسامبر"
                        ],
                        "days": ["یکشنبه", "دوشنبه", "سه‌شنبه", "چهارشنبه", "پنجشنبه", "جمعه", "شنبه"],
                        "shortDays": ["ی", "د", "س", "چ", "پ", "ج", "ش"],
                        "toolbar": {
                            "exportToSVG": "دریافت SVG",
                            "exportToPNG": "دریافت PNG",
                            "menu": "فهرست",
                            "selection": "انتخاب",
                            "selectionZoom": "بزرگنمایی قسمت انتخاب شده",
                            "zoomIn": "بزرگ نمایی",
                            "zoomOut": "کوچک نمایی",
                            "pan": "جا به جایی",
                            "reset": "بازنشانی بزرگ نمایی"
                        }
                    }
                }],
                defaultLocale: "fa"

            }

            // Color constant
            const chartColors = {
                column: {
                    series1: '#826af9',
                    series2: '#d2b0ff',
                    bg: '#f8d3ff'
                },
                donut: {
                    series1: '#fee802',
                    series2: '#3fd0bd',
                    series3: '#826bf8',
                    series4: '#2b9bf4'
                },
                area: {
                    series1: '#29dac7',
                    series2: '#60f2ca',
                    series3: '#a5f8cd'
                },
                line: {
                    series1: '#29dac7',
                    series2: '#60f2ca',
                    series3: '#a5f8cd'
                }
            };

            // Line Area Chart
            // --------------------------------------------------------------------
            const areaChartEl = document.querySelector('#lineAreaChart'),
                areaChartConfig = {
                    chart: {
                        height: 400,
                        type: 'area',
                        parentHeightOffset: 0,
                        toolbar: {
                            show: false
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: false,
                        curve: 'straight'
                    },
                    legend: {
                        show: true,
                        position: 'top',
                        horizontalAlign: 'start',
                        labels: {
                            colors: legendColor,
                            useSeriesColors: false
                        }
                    },
                    grid: {
                        borderColor: borderColor,
                        xaxis: {
                            lines: {
                                show: true
                            }
                        }
                    },
                    colors: [chartColors.area.series3, chartColors.area.series2, chartColors.area.series1],
                    series: sections,
                    xaxis: {
                        categories: categories,
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '13px',
                                rotate: 45
                            }
                        },
                        title: {
                            text: "@lang('تاریخ')"
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '13px'
                            },
                            formatter: function(value) {
                                return addCommas(value);
                            }
                        },
                        title: {
                            text: "{{ __('تعداد ورود ثبت شده') }}"
                        }
                    },
                    fill: {
                        opacity: 1,
                        type: 'solid'
                    },
                    tooltip: {
                        shared: false,
                        y: {
                            formatter: function(val) {
                                return val + " {{ __('ثبت ورود') }} ";
                            }
                        }
                    }
                };
            if (typeof areaChartEl !== undefined && areaChartEl !== null) {
                const areaChart = new ApexCharts(areaChartEl, areaChartConfig);
                areaChart.render();
            }

            // Line Chart
            // --------------------------------------------------------------------
            const lineChartEl = document.querySelector('#lineChart'),
                lineChartConfig = {
                    chart: {
                        height: 400,
                        type: 'line',
                        parentHeightOffset: 0,
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false
                        }
                    },
                    colors: [chartColors.line.series3, chartColors.line.series2, chartColors.line.series1],
                    series: paymentTypes,
                    markers: {
                        strokeWidth: 7,
                        strokeOpacity: 1,
                        strokeColors: [config.colors.white],
                        colors: [config.colors.warning]
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'straight'
                    },
                    grid: {
                        borderColor: borderColor,
                        xaxis: {
                            lines: {
                                show: true
                            }
                        },
                        padding: {
                            top: -20
                        }
                    },
                    tooltip: {
                        custom: function({
                            series,
                            seriesIndex,
                            dataPointIndex,
                            w
                        }) {
                            var value = series[seriesIndex][dataPointIndex].toString();
                            return '<div class="px-3 py-2">' + '<span>' + addCommas(value) + '</span>' + '</div>';
                        }
                    },
                    xaxis: {
                        categories: categories,
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '13px',
                                rotate: 45
                            }
                        },
                        title: {
                            text: "{{ __('تاریخ') }}"
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '13px'
                            },
                            formatter: function(value) {
                                return addCommas(value);
                            }
                        },
                        title: {
                            text: '{{ __('مجموع پرداخت') }}'
                        }
                    },
                    legend: {
                        show: true,
                        position: 'top',
                        horizontalAlign: 'start',
                        labels: {
                            colors: legendColor,
                            useSeriesColors: false
                        }
                    }
                };
            if (typeof lineChartEl !== undefined && lineChartEl !== null) {
                const lineChart = new ApexCharts(lineChartEl, lineChartConfig);
                lineChart.render();
            }

            // Line Chart
            // --------------------------------------------------------------------
            const costChart = document.querySelector('#costChart'),
                costChartConfig = {
                    chart: {
                        height: 400,
                        type: 'line',
                        parentHeightOffset: 0,
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false
                        }
                    },
                    colors: [chartColors.line.series3, chartColors.line.series2, chartColors.line.series1],
                    series: costCategories,
                    markers: {
                        strokeWidth: 7,
                        strokeOpacity: 1,
                        strokeColors: [config.colors.white],
                        colors: [config.colors.warning]
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'straight'
                    },
                    grid: {
                        borderColor: borderColor,
                        xaxis: {
                            lines: {
                                show: true
                            }
                        },
                        padding: {
                            top: -20
                        }
                    },
                    tooltip: {
                        custom: function({
                            series,
                            seriesIndex,
                            dataPointIndex,
                            w
                        }) {
                            return '<div class="px-3 py-2">' + '<span>' + addCommas(series[seriesIndex][
                                dataPointIndex
                            ]) + '</span>' + '</div>';
                        }
                    },
                    xaxis: {
                        categories: categories,
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '13px',
                                rotate: 45
                            }
                        },
                        title: {
                            text: "{{ __('تاریخ') }}"
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '13px'
                            },
                            formatter: function(value) {
                                return addCommas(value);
                            }
                        },
                        title: {
                            text: "{{ __('مجموع هزینه ها') }}"
                        }
                    },
                    legend: {
                        show: true,
                        position: 'top',
                        horizontalAlign: 'start',
                        labels: {
                            colors: legendColor,
                            useSeriesColors: false
                        }
                    }
                };
            if (typeof costChart !== undefined && costChart !== null) {
                const _costChart = new ApexCharts(costChart, costChartConfig);
                _costChart.render();
            }
        }

        $(document).ready(function() {
            makeCharts();

            $(document.body).on("click", "#search", function() {
                var d = checkDate();
                if (d) {
                    Swal.fire({
                        title: "@lang('اخطار')",
                        icon: 'error',
                        text: 'تاریخ وارد شده معتبر نمیباشد. (نمونه معتبر 1401/01/01)'
                    });
                    return;
                }
                $("#loading").fadeIn();
                var from_date = $("#from-date").val();
                var to_date = $("#to-date").val();
                if (from_date == '' || to_date == '') {
                    $("#loading").fadeOut();
                    Swal.fire({
                        title: "{{ __('اخطار') }}",
                        icon: "error",
                        text: "{{ __('لطفا کادرهای ستاره دار را تکمیل نمایید.') }}"
                    });
                } else {
                    makeCharts(from_date, to_date);
                    $("#loading").fadeOut();
                }
            });
        });
    </script>
@stop
