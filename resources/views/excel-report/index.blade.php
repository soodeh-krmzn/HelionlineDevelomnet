@extends('parts.master')

@section('title', __('گزارشات اکسل'))

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
                                    <select id="s-user" class="form-select select2-2">
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
                                    <input type="text" id="s-from-date" class="form-control date-mask "
                                        placeholder="1401/01/01...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('تا تاریخ')</label>
                                    <input type="text" id="s-to-date" class="form-control date-mask "
                                        placeholder="1401/01/01...">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end justify-content-start">
                                <div class="form-group">
                                    <button class="btn btn-success" id="search-report">@lang('جستجو')</button>
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
                        <th>@lang('نام')</th>
                        <th>@lang('جدول')</th>
                        <th>@lang('تاریخ')</th>
                        <th>@lang('دانلود')</th>
                    </tr>
                </thead>
            </table></div>`;
        $(document.body).on("click", "#show-all", function() {
            var data = {
                'all': true
            }
            window['data'] = data;
            window.data_table = makeTable(data);
        });

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#report-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableExcelReport') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user_id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'table'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'link',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        }

        $(document).ready(function() {
            $(".select2-2").select2({
                placeholder: "@lang('انتخاب')"
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
                    "filter_search": true
                }
                makeTable(data);
                $("#loading").fadeOut();
            });
        });
    </script>
@stop
