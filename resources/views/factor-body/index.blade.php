@extends('parts.master')

@section('title', __('گزارش فروش محصول'))

@section('head-styles')

@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="card">
            <div class="collapse" id="collapseExample">
                <div class="card-header border-bottom">

                    <div class="py-3 primary-font">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('محصول')</label>
                                    <select id="s-product-id" class="form-select select2-2">
                                        <option value="">@lang('انتخاب')...</option>
                                        @foreach (\App\Models\Product::latest()->get() as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('از تاریخ')</label>
                                    <input type="text" id="s-from-date" class="form-control date-mask "
                                        placeholder="1400/01/01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('تا تاریخ')</label>
                                    <input type="text" id="s-to-date" class="form-control date-mask "
                                        placeholder="1400/01/01">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end justify-content-start">
                                <div class="form-group">
                                    <button class="btn btn-success" id="search">@lang('جستجو')</button>
                                    <button class="btn btn-info" id="show-all">@lang('نمایش همه')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <div class="row mx-1 my-3">
                    <div class="col">
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            <i class='bx bx-search me-1'></i> <span
                                class="d-none d-lg-inline-block">@lang('جستجو')</span>
                        </button>
                    </div>
                    <div class="col">
                        <div
                            class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center flex-wrap me-1">
                            <div class="dt-buttons btn-group flex-wrap">
                                <div class="btn-group">
                                    <button id="export" class="btn btn-success">
                                        <span>
                                            <i class="bx bxs-file-export"></i>
                                            <span class="d-none d-lg-inline-block">@lang('خروجی اکسل')</span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mx-1">
                    <div id="result" class="col"></div>
                </div>
                <div class="row mx-1">
                    <div class="row">
                        <div id="sum-container" class="alert alert-warning my-md-3 col-md-6 text-center">@lang('جمع کل'):
                            <span id="total"></span></div>
                        <div id="sum-container" class="alert alert-warning my-md-3 col-md-6 text-center">@lang('سود'):
                            <span id="sum"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('footer-scripts')
    <script type="text/javascript">
        let table = `<div class="table-responsive">
        <table id="payment-table" class="table table-hover border-top">
            <thead>
                <tr>
                    <td>@lang('ردیف')</td>
                    <td>@lang('نام محصول')</td>
                    <td>@lang('قیمت واحد')</td>
                    <td>@lang('قیمت خرید واحد')</td>
                    <td>@lang('تعداد')</td>
                    <td>@lang('قیمت کل')</td>
                    <td>@lang('قیمت خرید کل')</td>
                    <td>@lang('تاریخ')</td>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#payment-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableFactorBody') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                order: [7, 'desc'],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'product_id'
                    },
                    {
                        data: 'product_price'
                    },
                    {
                        data: 'product_buy_price'
                    },
                    {
                        data: 'count'
                    },
                    {
                        data: 'body_price'
                    },
                    {
                        data: 'body_buy_price'
                    },
                    {
                        data: 'created_at'
                    }
                ]
            });
            $.ajax({
                url: "{{ route('sumFactorBody') }}",
                type: "GET",
                data: fields ?? {},
                success: function(data) {
                    $("#sum").html(addCommas(data.sum));
                    $("#total").html(addCommas(data.total));
                }
            });
        }

        $(document).ready(function() {
            $(".select2-2").select2({
                placeholder: "@lang('انتخاب')"
            });
            makeTable();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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
                var product_id = $("#s-product-id").val();
                var from_date = $("#s-from-date").val();
                var to_date = $("#s-to-date").val();
                var data = {
                    "product_id": product_id,
                    "from_date": from_date,
                    "to_date": to_date,
                    "filter_search": true
                }
                window['data'] = data;
                makeTable(data);
                $("#loading").fadeOut();
            });
            $(document.body).on("click", "#show-all", function() {
                var data = {
                    'all': true
                }
                window['data'] = data;
                window.data_table = makeTable(data);
            });
            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportFactorBody') }}",
                    type: "POST",
                    data: window['data'],
                    success: function() {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('موفق')",
                            icon: 'success',
                            text: "@lang('گزارش گیری انجام شد. از صفحه گزارشات اکسل میتوانید این گزارش را دانلود کنید.')"
                        });
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('خطا')",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });


        });
    </script>

@stop
