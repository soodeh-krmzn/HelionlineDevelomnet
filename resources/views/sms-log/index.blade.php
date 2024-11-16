@extends('parts.master')

@section('title', __('گزارش ارسال پیامک'))

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
                                    <label class="form-label">@lang('موبایل')</label>
                                    <input type="text" id="s_mobile" class="form-control just-numbers checkMobile"
                                        placeholder="@lang('موبایل')...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('دسته پیامک')</label>
                                    <select id="s_category" class="form-select">
                                        <option value="">@lang('همه')</option>
                                        @foreach (\App\Models\Admin\SmsPatternCategory::getSelect() as $category)
                                            <option>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('از تاریخ')</label>
                                    <input type="text" id="s_from_date" class="form-control date-mask "
                                        placeholder="1400/01/01...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('تا تاریخ')</label>
                                    <input type="text" id="s_to_date" class="form-control date-mask "
                                        placeholder="1400/01/01...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-end">
                            <div class="form-group">
                                <button class="btn btn-success" id="search-logs">@lang('جستجو')</button>
                                <button class="btn btn-info" id="show-all">@lang('نمایش همه')</button>
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
                            <i class='bx bx-search me-1'></i> <span class="d-none d-lg-inline-block">@lang('جستجو')</span>
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
            </div>
        </div>
    </div>
@stop
@section('footer-scripts')
    <script type="text/javascript">
        let table = `<div class="table-responsive ">
        <table id="smsLog-table" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>@lang('ردیف')</th>
                    <th>@lang('تاریخ')</th>
                    <th>@lang('گیرنده')</th>
                    <th>@lang('ردیف')</th>
                    <th>@lang('تعداد')</th>
                    <th >@lang('پیام')</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#smsLog-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableSmsLog') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false, searchable: false
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'recipient'
                    },
                    {
                        data: 'category_name'
                    },
                    {
                        data: 'parts'
                    },
                    {
                        data: 'message',
                        orderable: false
                    }
                ]
            });
        }
        $(document.body).on("click", "#show-all", function() {
            var data = {
                'all': true
            }
            window['data'] = data;
            window.data_table = makeTable(data);
        });
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let loading = `
            <div class="col text-center">
                <span class="spinner-grow" role="status" aria-hidden="true"></span>
            </div>`;

            makeTable();

            let data;

            $(document.body).on("click", "#search-logs", function() {
                var m = checkMobile();
                if (m) {
                    Swal.fire({
                        title: "@lang('اخطار')",
                        icon: 'error',
                        text: "@lang('فرمت شماره موبایل معتبر نمیباشد.')"
                    });
                    return;
                }
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
                var mobile = $("#s_mobile").val();
                var category = $("#s_category").val();
                var from_date = $("#s_from_date").val();
                var to_date = $("#s_to_date").val();
                var data = {
                    "mobile": mobile,
                    "category": category,
                    "from_date": from_date,
                    "to_date": to_date,
                    "filter_search": true
                }
                window['data'] = data;
                makeTable(data);
                $("#loading").fadeOut();
            });

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportSmsLog') }}",
                    type: "POST",
                    data: window['data'],
                    success: function() {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title:"@lang('موفق')",
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
