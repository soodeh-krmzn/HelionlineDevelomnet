@extends('parts.master')

@section('title', __('تردد پرسنل'))

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
                                    <label class="form-label">@lang('کاربر')</label>
                                    <select name="s_user" id="s-user" class="form-select select2-2">
                                        <option value="">@lang('همه')</option>
                                        @foreach (\App\Models\User::getUsers() as $user)
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
                                    <button class="btn btn-success" id="search-user-activity">@lang('جستجو')</button>
                                    <a href="{{ route('userActivity') }}" class="btn btn-info"
                                        id="show-all">@lang('نمایش همه')</a>
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
                            class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                            <div class="dt-buttons btn-group flex-wrap">
                                <div class="btn-group">
                                    <button id="export" class="btn btn-success">
                                        <span>
                                            <i class="bx bxs-file-export"></i>
                                            <span class="d-none d-lg-inline-block">@lang('خروجی اکسل')</span>
                                        </span>
                                    </button>
                                </div>
                                @can('create')
                                    <button class="btn btn-secondary add-new btn-primary crud ms-2" data-action="create"
                                        data-id="0" tabindex="0" aria-controls="DataTables_Table_0" type="button"
                                        data-bs-toggle="modal" data-bs-target="#crud">
                                        <span>
                                            <i class="bx bx-plus"></i>
                                            <span class="d-none d-lg-inline-block">@lang('تردد جدید')</span>
                                        </span>
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mx-1">
                    <div id="result" class="col"></div>
                </div>
                <div class="row mx-1">
                    <div class="col">
                        <div id="minutes" class="alert alert-warning my-3 text-center"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="crud" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3 p-md-5">
                    <div id="crud-result"></div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer-scripts')
    <script type="text/javascript">
        let table = `<div class="table-responsive">
        <table id="userTable" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>@lang('ردیف')</th>
                    <th>@lang('نام کاربر')</th>
                    <th>@lang('ورود')</th>
                    <th>@lang('خروج')</th>
                    <th>@lang('مدت زمان')</th>
                    <th>@lang('عملیات')</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#userTable").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableUserActivity') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                order: [2, 'desc'],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'in'
                    },
                    {
                        data: 'out'
                    },
                    {
                        data: 'minutes'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            $.ajax({
                url: "{{ route('minutesUserActivity') }}",
                type: "GET",
                data: fields ?? {},
                success: function(data) {
                    if (data == '0') {
                        $("#minutes").hide();
                    } else {
                        $("#minutes").show();
                    }
                    $("#minutes").html(data);
                }
            });
        }
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            makeTable();

            $(document.body).on("click", "#store-activity", function() {
                var e = checkEmpty();
                if (!e) {
                    $("#loading").fadeIn();
                    var id = $(this).data("id");
                    var action = $(this).data("action");
                    var user_id = $("#user-id").find("option:selected").val();
                    var i = $("#in").val();
                    var out = $("#out").val();

                    $.ajax({
                        type: "POST",
                        url: "{{ route('store2UserActivity') }}",
                        data: {
                            id: id,
                            action: action,
                            user_id: user_id,
                            in: i,
                            out: out
                        },
                        success: function(data) {
                            makeTable();
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "@lang('موفق')",
                                text: "@lang('اطلاعات با موفقیت ثبت شد.')",
                                icon: "success"
                            });
                            if (action == "create") {
                                $("#in").val("");
                                $("#out").val("");
                            }
                        },
                        error: function(data) {
                            makeTable();
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "@lang('خطا')",
                                text: data.responseJSON.message,
                                icon: "error"
                            });
                        }
                    });
                }
            });

            $(document.body).on("click", ".crud", function() {
                $("#loading").fadeIn();
                var id = $(this).data("id");
                var action = $(this).data("action");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crud2UserActivity') }}",
                    data: {
                        id: id,
                        action: action
                    },
                    success: function(data) {
                        $("#crud-result").html(data);
                        $(".select2-p").select2({
                            dropdownParent: $('#crud .modal-content')
                        });
                        dateTimeMask();
                        $("#loading").fadeOut();
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

            $(document.body).on("click", ".delete-activity", function() {
                Swal.fire({
                    title: "@lang('اطمینان دارید؟')",
                    text: "@lang('آیا از حذف این مورد اطمینان دارید؟')",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "@lang('بله، مطمئنم.')",
                    cancelButtonText: "@lang('نه، پشیمون شدم.')"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading").fadeIn();
                        var id = $(this).data("id");

                        $.ajax({
                            type: "POST",
                            url: "{{ route('deleteUserActivity') }}",
                            data: {
                                id: id,
                            },
                            success: function(data) {
                                makeTable();
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "@lang('موفق')",
                                    text: "@lang('مورد با موفقیت حذف شد.')",
                                    icon: "success"
                                });
                            },
                            error: function(data) {
                                makeTable();
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "@lang('خطا')",
                                    text: data.responseJSON.message,
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });

            let data;

            $(document.body).on("click", "#search-user-activity", function() {
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
                    user_id: user_id,
                    from_date: from_date,
                    to_date: to_date,
                    filter_search: true
                }
                window['data'] = data;
                makeTable(data);
                $("#loading").fadeOut();
            });

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportUserActivity') }}",
                    type: "POST",
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

            $(".select2-2").select2({
                placeholder: "@lang('انتخاب')"
            });
        });
    </script>
@stop
