@extends('parts.master')

@section('title', __('نظرسنجی'))

@section('head-styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row my-3">
                            <div class="col">
                                <label class="form-label">@lang('انتخاب نظرسنجی')</label>
                            </div>
                            <div class="col">
                                @if (\App\Models\Vote::all()->count() > 0)
                                    <select id="vote-selector" class="form-select">
                                        <option value="">@lang('انتخاب نظرسنجی')</option>
                                        @foreach(\App\Models\Vote::all() as $vote)
                                            <option value="{{ $vote->id }}">{{ $vote->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <span class="badge bg-danger">@lang('هیچ نظرسنجی تعریف نشده است.')</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="result" class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row my-3">
                            <div class="col">
                                <div class="alert alert-danger text-center">
                                    @lang('لطفا نظرسنجی را انتخاب کنید.')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer-scripts')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <script type="text/javascript">
        'use strict';

        function render(id, data) {
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

            const horizontalBarChartEl = document.querySelector('#question-'+id),
                horizontalBarChartConfig = {
                    chart: {
                        height: 400,
                        type: 'bar',
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            barHeight: '30%',
                            columnWidth: '30%',
                            startingShape: 'rounded',
                            borderRadius: 8
                        }
                    },
                    grid: {
                        borderColor: borderColor,
                        xaxis: {
                            lines: {
                                show: false
                            }
                        },
                        padding: {
                            top: -20,
                            bottom: -12
                        }
                    },
                    colors: config.colors.info,
                    dataLabels: {
                        enabled: false
                    },
                    series: [data],
                    xaxis: {
                        categories: [
                            "@lang('خیلی ضعیف')", "@lang('ضعیف')", "@lang('متوسط')", "@lang('خوب')", "@lang('عالی')"
                        ],
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
                            text: "@lang('تعداد نظر')"
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val + " @lang('نظر') ";
                            }
                        }
                    }
                };
            if (typeof horizontalBarChartEl !== undefined && horizontalBarChartEl !== null) {
                const horizontalBarChart = new ApexCharts(horizontalBarChartEl, horizontalBarChartConfig);
                horizontalBarChart.render();
            }
        }

        function resultQuestion(question)
        {
            var question_id = $(question).data("id");
            $.ajax({
                url: "{{ route('resultQuestion') }}",
                type: "POST",
                data: {
                    question_id: question_id
                },
                success: function(data) {
                    render(question_id, data);
                }
            });
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let no_vote = `
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row my-3">
                            <div class="col">
                                <div class="alert alert-danger text-center">
                                    @lang('لطفا نظرسنجی را انتخاب کنید.')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `;

            $(document.body).on("change", "#vote-selector", function() {
                var vote_id = $(this).val();
                if (vote_id == '') {
                    $("#result").html(no_vote);
                    return;
                }
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('voteResponse') }}",
                    type: "POST",
                    data: {
                        vote_id: vote_id
                    },
                    success: function(data) {
                        $("#result").html(data);
                        $(".chart").each(function() {
                            var question = $(this);
                            resultQuestion(question);
                        });
                        $(".response-table").each(function() {
                            $(this).DataTable();
                        });
                        $("#loading").fadeOut();
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('اخطار')",
                            icon: 'error',
                            text: data.responseJSON.message
                        });
                    }
                });
            });
        });

    </script>
@stop
