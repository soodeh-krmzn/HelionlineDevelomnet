@extends('parts.master')

@section('title', 'گزارش اصلاحات')

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
                                    <label class="form-label">@lang('نام کاربر')</label>
                                    <select id="s-user" class="form-select select2">
                                        <option value="">@lang('انتخاب')</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->getFullName() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('از تاریخ')</label>
                                    <input type="text" id="s-from-date" class="form-control date-mask " placeholder="1401/01/01...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('تا تاریخ')</label>
                                    <input type="text" id="s-to-date" class="form-control date-mask " placeholder="1401/01/01...">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end justify-content-start">
                                <div class="form-group">
                                    <button class="btn btn-success" id="search-report">@lang('جستجو')</button>
                                    <a href="{{ route("editReport", $model) }}" class="btn btn-info" id="show-all">@lang('نمایش همه')</a>
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
                            <i class='bx bx-search me-1'></i> @lang('جستجو')
                        </button>
                    </div>
                    <div class="col">
                        <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
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
        let table = `<div class="table-responsive">
            <table id="report-table" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th>@lang('ردیف')</th>
                        <th>@lang('نام کاربر')</th>
                        <th>{{ $report->getEditedNameLabel($report->models[$model]) }}</th>
                        <th>@lang('کد')</th>
                        <th>@lang('تاریخ')</th>
                        <th style="min-width:300px">@lang('توضیحات')</th>
                    </tr>
                </thead>
            </table></div>`;

            function makeTable(fields = null) {
                $("#result").html(table);
                $("#report-table").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        "url": "{{ route('tableEditReport') }}",
                        "type": "GET",
                        "data": fields ?? {
                            model: "{{ $model }}"
                        }
                    },
                    order:[4,'desc'],
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'user'},
                        {data: 'name'},
                        {data: 'edited_id'},
                        {data: 'created_at'},
                        {data: 'details'}
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

                $(document.body).on("click", "#search-report", function() {
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
                    var user_id = $("#s-user").val();
                    var from_date = $("#s-from-date").val();
                    var to_date = $("#s-to-date").val();
                    var data = {
                            "user_id": user_id,
                            "from_date": from_date,
                            "to_date": to_date,
                            "filter_search": true,
                            "model": "{{ $model }}"
                        }
                    window['data'] = data;
                    makeTable(data);
                    $("#loading").fadeOut();
                });
            });

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportEditReport') }}",
                    type: "POST",
                    data: window['data'] ?? {
                        filter_search: true,
                        model: "{{ $model }}"
                    },
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
    </script>
@stop
