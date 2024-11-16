@extends('parts.master')

@section('title', __('گزارش جامع'))

@section('head-styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="row gy-4 mb-4">
            <div class="col">
                <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample"
                    aria-expanded="false" aria-controls="collapseExample">
                    <i class='bx bx-search me-1'></i> <span class="d-none d-lg-inline-block">@lang('جستجو')</span>
                </button>
            </div>
            <div class="collapse" id="collapseExample">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">@lang('از تاریخ')</label>
                                        <input type="text" id="from-date" class="form-control date-mask "
                                            placeholder="1400/01/01">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">@lang('تا تاریخ')</label>
                                        <input type="text" id="to-date" class="form-control date-mask "
                                            placeholder="1400/01/01">
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end justify-content-start">
                                    <div class="form-group">
                                        <button class="btn btn-success" id="search">@lang('جستجو')</button>
                                        <a href="{{ route('balance') }}" class="btn btn-secondary">@lang('نمایش روز')</a>
                                        <button class="btn btn-info" id="show_all">@lang('نمایش همه')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">@lang('فروش')</h5>
                    </div>
                    <div class="card-body">
                        <div id="payment"></div>
                    </div>
                    <div class="row mx-1">
                        <div class="col">
                            <div class="alert alert-success my-3 text-center">@lang('مجموع فروش'): <span id="payment-sum"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between mb-3">
                        <h5 class="card-title mb-0">@lang('هزینه')</h5>
                    </div>
                    <div class="card-body">
                        <div id="cost"></div>
                    </div>
                    <div class="row mx-1">
                        <div class="col">
                            <div class="alert alert-danger my-3 text-center">@lang('مجموع هزینه'): <span id="cost-sum"></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-warning my-3 text-center">@lang('تراز'): <span id="sum"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer-scripts')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/js/dashboards-ecommerce.js') }}"></script>

    <script type="text/javascript">
        let cost_table = `<div class="table-responsive">
        <table id="cost-table" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>@lang('ردیف')</th>
                    <th>@lang('دسته')</th>
                    <th>@lang('مبلغ')</th>
                    <th>@lang('توضیحات')</th>
                    <th>@lang('تاریخ')</th>
                    <th>@lang('کاربر')</th>
                </tr>
            </thead>
        </table></div>`;

        let payment_table = `<div class="table-responsive">
        <table id="payment-table" class="table table-hover border-top">
            <thead>
                <tr>
                    <td>@lang('ردیف')</td>
                    <td>@lang('نام شخص')</td>
                    <td>@lang('مبلغ')</td>
                    <td>@lang('نوع پرداخت')</td>
                    <td>@lang('تاریخ')</td>
                    <td>@lang('توضیحات')</td>
                </tr>
            </thead>
        </table></div>`;

        function paymentTable(fields = null) {
            $("#payment").html(payment_table);
            $("#payment-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tablePayment') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false, searchable: false
                    },
                    {
                        data: 'person'
                    },
                    {
                        data: 'price'
                    },
                    {
                        data: 'type'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'details'
                    }
                ]
            });
        }

        function costTable(fields = null) {
            $("#cost").html(cost_table);
            $("#cost-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableCost') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false, searchable: false
                    },
                    {
                        data: 'categories'
                    },
                    {
                        data: 'price'
                    },
                    {
                        data: 'details'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'created_by'
                    }
                ]
            });
        }

        function getSums(data) {
            $.ajax({
                url: "{{ route('sumBalance') }}",
                type: "GET",
                data: data ?? {},
                success: function(data) {
                    $("#payment-sum").html(data.payment);
                    $("#cost-sum").html(data.cost);
                    $("#sum").html(data.sum);
                }
            });
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var paymentData = {
                to_price: 0,
                from_date: '{{ DateFormat(now()) }}',
                to_date: '{{ DateFormat(now()) }}',
                filter_search: true,
                withOffers:true
            }
            var costData = {
                from_date: '{{ DateFormat(now()) }}',
                to_date: '{{ DateFormat(now()) }}',
                filter_search: true
            }
            paymentTable(paymentData);
            costTable(costData);
            getSums(costData);

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
                var costData = {
                    from_date: from_date,
                    to_date: to_date,
                    filter_search: true,

                }
                var paymentData = {
                    from_date: from_date,
                    to_date: to_date,
                    to_price: 0,
                    filter_search: true,
                    withOffers:true
                }
                costTable(costData);
                paymentTable(paymentData);
                getSums(costData);
                $("#loading").fadeOut();
            });

            $(document.body).on("click", "#show_all", function() {
                $("#loading").fadeIn();
                $("#from-date").val('');
                $("#to-date").val('');
                var from_date = null;
                var to_date = null;
                var costData = {
                    from_date: from_date,
                    to_date: to_date,
                    filter_search: true
                }
                var paymentData = {
                    from_date: from_date,
                    to_date: to_date,
                    to_price: 0,
                    filter_search: true
                }
                costTable(costData);
                paymentTable(paymentData);
                getSums(costData);
                $("#loading").fadeOut();
            });

        });
    </script>
@stop
