@extends('parts.master')

@section('title', __('گزارشات باشگاه مشتریان'))

@section('head-styles')

@stop

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="card">
            <div class="collapse" id="collapseExample">
                <div class="card-header border-bottom">
                    <div class="py-3 primary-font">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('شخص') }}</label>
                                    <select name="s-person-id" id="s-person-id" class="searchPerson">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('از تاریخ') }}</label>
                                    <input type="text" id="s-from-date" class="form-control date-mask " placeholder="1400/01/01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('تا تاریخ') }}</label>
                                    <input type="text" id="s-to-date" class="form-control date-mask " placeholder="1400/01/01">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end justify-content-start">
                                <div class="form-group">
                                    <button class="btn btn-success" id="search-logs">{{ __('جستجو') }}</button>
                                    <a href="{{ route("clubLog") }}" class="btn btn-info" id="show-all">{{ __('نمایش همه') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <div class="row mx-1 my-3">
                    <div class="col">
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            <i class='bx bx-search me-1'></i> {{ __('جستجو') }}
                        </button>
                    </div>
                    <div class="col">
                        <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                            <div class="dt-buttons btn-group flex-wrap">
                                <div class="btn-group">
                                    <button id="export" class="btn btn-success">
                                        <span>
                                            <i class="bx bxs-file-export"></i>
                                            <span class="d-none d-lg-inline-block">{{ __('خروجی اکسل') }}</span>
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
    searchPerson();
        let table = `<div class="table-responsive">
        <table id="club-log-table" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>@lang('ردیف')</th>
                    <th>@lang('نام')</th>
                    <th>@lang('تاریخ')</th>
                    <th>@lang('مبلغ پرداختی')</th>
                    <th>@lang('امتیاز')</th>
                    <th>@lang('امتیاز کل')</th>
                    <th>@lang('توضیحات')</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#club-log-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableClubLog') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'person'},
                    {data: 'created_at'},
                    {data: 'price'},
                    {data: 'rate'},
                    {data: 'balance_rate'},
                    {data: 'description'}
                ]
            });
        }
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            makeTable();

            let loading = `
            <div class="col text-center">
                <span class="spinner-grow" role="status" aria-hidden="true"></span>
            </div>`;

            let data;

            $(document.body).on("click", "#search-logs", function() {
                var d = checkDate();
                if (d) {
                    Swal.fire({
                        title: "{{ __('اخطار') }}",
                        icon: "error",
                        text: "{{ __('تاریخ وارد شده معتبر نمی باشد. (نمونه معتبر 1401/01/01)') }}"
                    });
                    return;
                }
                $("#loading").fadeIn();
                var person_id = $("#s-person-id").val();
                var from_date = $("#s-from-date").val();
                var to_date = $("#s-to-date").val();
                var data = {
                    "person_id": person_id,
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
                    url: "{{ route('exportClubLog') }}",
                    type: "POST",
                    data: window['data'],
                    success: function() {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "{{ __('عملیات موفق') }}",
                            icon: "success",
                            text: "{{ __('گزارش گیری انجام شد. از صفحه گزارشات اکسل میتوانید این گزارش را دانلود کنید.') }}"
                        });
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

        });
    </script>
@stop
