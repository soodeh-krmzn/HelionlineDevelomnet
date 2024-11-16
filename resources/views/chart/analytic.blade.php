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
                <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    <i class='bx bx-search me-1'></i> {{ __('جستجو') }}
                </button>
            </div>
            <div class="collapse" id="collapseExample">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('سال') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="year" class="form-control just-numbers"
                                            placeholder="{{ __('سال') }}...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('ماه') }} <span class="text-danger">*</span></label>
                                        <select name="" id="month" class="form-select">
                                            <option value="01">{{ __('فروردین') }}</option>
                                            <option value="02">{{ __('اردیبهشت') }}</option>
                                            <option value="03">{{ __('خرداد') }}</option>
                                            <option value="04">{{ __('تیر') }}</option>
                                            <option value="05">{{ __('مرداد') }}</option>
                                            <option value="06">{{ __('شهریور') }}</option>
                                            <option value="07">{{ __('مهر') }}</option>
                                            <option value="08">{{ __('آبان') }}</option>
                                            <option value="09">{{ __('آذر') }}</option>
                                            <option value="10">{{ __('دی') }}</option>
                                            <option value="11">{{ __('بهمن') }}</option>
                                            <option value="12">{{ __('اسفند') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('روز') }} <span class="text-danger">*</span></label>
                                        <select id="day" class="form-select">
                                            <option value="01">1</option>
                                            <option value="02">2</option>
                                            <option value="03">3</option>
                                            <option value="04">4</option>
                                            <option value="05">5</option>
                                            <option value="06">6</option>
                                            <option value="07">7</option>
                                            <option value="08">8</option>
                                            <option value="09">9</option>
                                            <option>10</option>
                                            <option>11</option>
                                            <option>12</option>
                                            <option>13</option>
                                            <option>14</option>
                                            <option>15</option>
                                            <option>16</option>
                                            <option>17</option>
                                            <option>18</option>
                                            <option>19</option>
                                            <option>20</option>
                                            <option>21</option>
                                            <option>22</option>
                                            <option>23</option>
                                            <option>24</option>
                                            <option>25</option>
                                            <option>26</option>
                                            <option>27</option>
                                            <option>28</option>
                                            <option>29</option>
                                            <option>30</option>
                                            <option>31</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex align-items-end justify-content-start">
                                    <div class="form-group">
                                        <button class="btn btn-success" id="search">{{ __('جستجو') }}</button>
                                        <a href="{{ route("analyticChart") }}" class="btn btn-info" id="show-all">{{ __('نمایش امروز') }}</a>
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
                            <small class="text-muted primary-font">{{ __('بر اساس ساعات روز') }}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart" id="lineAreaChart"></div>
                    </div>
                </div>
            </div>
            <!-- /Line Area Chart -->

            <!-- Line Area Chart -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-1">{{ __('حضور مشتریان') }}</h5>
                            <small class="text-muted primary-font">{{ __('بر اساس تعداد ساعات روز') }}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart" id="scatterChart"></div>
                    </div>
                </div>
            </div>
            <!-- /Line Area Chart -->

        </div>
    </div>
    <!--/ Content -->
@stop

@section('footer-scripts')
    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <script type="text/javascript">
        'use strict';

        function makeCharts(day, month, year) {
            console.log(day)
            console.log(month)
            console.log(year)

            $.ajax({
                url: '{{ route('dataAnalytic') }}',
                type: 'GET',
                data: {
                    day: day,
                    month: month,
                    year: year
                },
                success: function(data) {
                    console.log(data);
                    let sections, sections2, categories;
                    sections = data.sections;
                    sections2 = data.sections2;
                    categories = data.categories;
                    render(sections, sections2, categories);
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

        function render(sections, sections2, categories) {
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
                                fontSize: '13px'
                            }
                        },
                        title: {
                            text: '{{ __('ساعات شبانه روز') }}'
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '13px'
                            },
                            formatter: function(val) {
                                return parseInt(val);
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
                        x: {
                            formatter: function(val) {
                                return "@lang('ساعت') " + val + " @lang('تا') " + (val+1);
                            }
                        },
                        y: {
                            formatter: function(val) {
                                return val + " @lang('نفر') ";
                            }
                        }
                    }
                };
            if (typeof areaChartEl !== undefined && areaChartEl !== null) {
                const areaChart = new ApexCharts(areaChartEl, areaChartConfig);
                areaChart.render();
            }

            const scatterChartEl = document.querySelector('#scatterChart'),
                scatterChartConfig = {
                    chart: {
                        height: 400,
                        type: 'scatter',
                        zoom: {
                            enabled: true,
                            type: 'xy'
                        },
                        parentHeightOffset: 0,
                        toolbar: {
                            show: false
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
                    legend: {
                        show: true,
                        position: 'top',
                        horizontalAlign: 'start',
                        labels: {
                            colors: legendColor,
                            useSeriesColors: false
                        }
                    },
                    colors: [config.colors.warning, config.colors.primary, config.colors.success],
                    series: sections2,
                    xaxis: {
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            formatter: function(val) {
                                return parseInt(val);
                            },
                            style: {
                                colors: labelColor,
                                fontSize: '13px'
                            }
                        },
                        title: {
                            text: "{{ __('ساعت ورود') }}"
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '13px'
                            }
                        },
                        title: {
                            text: "{{ __('میزان حضور فرد (دقیقه)') }}"
                        }
                    },
                    tooltip: {
                        x: {
                            formatter: function(val) {
                                return "{{ __('ساعت ورود ') }}" + val + " {{ __('تا') }} " + (val + 1);
                            }
                        },
                        y: {
                            formatter: function(val) {
                                return val + "{{ __('دقیقه حضور ') }}";
                            }
                        }
                    }
                };
            if (typeof scatterChartEl !== undefined && scatterChartEl !== null) {
                const scatterChart = new ApexCharts(scatterChartEl, scatterChartConfig);
                scatterChart.render();
            }

        }

        $(document).ready(function() {
            makeCharts();

            $(document.body).on("click", "#search", function() {
                $("#loading").fadeIn();
                var day = $("#day").val();
                var month = $("#month").val();
                var year = $("#year").val();
                if (day == '' || month == '' || year == '') {
                    $("#loading").fadeOut();
                    Swal.fire({
                        title: "{{ __('اخطار') }}",
                        icon: "error",
                        text: "{{ __('لطفا همه کادر ها را پر کنید.') }}"
                    });
                } else {
                    if (year.length != 4) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            icon: "error",
                            text: "{{ __('لطفا سال را به صورت 4 رقمی وارد نمایید.') }}"
                        });
                    } else {
                        makeCharts(day, month, year);
                        $("#loading").fadeOut();
                    }
                }
            });
        });
    </script>
@stop
