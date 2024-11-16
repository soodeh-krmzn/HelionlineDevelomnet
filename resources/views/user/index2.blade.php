@extends('parts.master')

@section('title', __('کاربران'))

@section('head-styles')

@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="card">
            <div class="card-datatable table-responsive">
                <div class="row mx-1 my-3">
                    <div class="col-12">
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
                                @can('create')
                                    <button class="btn btn-secondary add-new btn-primary crud ms-2" data-action="create" data-id="0" tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal" data-bs-target="#crud">
                                        <span>
                                            <i class="bx bx-plus"></i>
                                            <span class="d-none d-lg-inline-block">@lang('کاربر جدید')</span>
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
                    <th>@lang('نام')</th>
                    <th>@lang('نام خانوادگی')</th>
                    <th>@lang('نام کاربری')</th>
                    <th>@lang('وضعیت')</th>
                    <th>@lang('نقش')</th>
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
                    "url": "{{ route('tableUser') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name'},
                    {data: 'family'},
                    {data: 'username'},
                    {data: 'status'},
                    {data: 'group'},
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        }

        $(document).ready(function() {

            makeTable();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document.body).on('change','.ch-user-status',function(){
                let data={
                    id:$(this).data('id'),
                    status:$(this).val()
                };
                actionAjax("{{route('user.change.status')}}", data, "موفق", "وضعیت با موفقیت تغییر کرد", success = null, error = null)
            });

            $(document.body).on("click", "#store-user", function() {
                var e = checkEmpty();
                var m = checkMobile();
                if (!e && !m) {
                    $("#loading").fadeIn();
                    var id = $(this).data("id");
                    var action = $(this).data("action");
                    var name = $("#name").val();
                    var family = $("#family").val();
                    var mobile = $("#mobile").val();
                    var group_id = $("#group-id").val();
                    var username = $("#s-username").val() ?? "";
                    var password = $("#s-password").val() ?? "";
                    var access = $("#access").val();

                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeUser') }}",
                        data: {
                            id: id,
                            action:action,
                            name: name,
                            family: family,
                            mobile: mobile,
                            group_id: group_id,
                            username: username,
                            password: password,
                            access: access
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
                                $("#name").val("");
                                $("#family").val("");
                                $("#mobile").val("");
                                $("#username").val("");
                                $("#password").val("");
                                $("#group-id").val(0);
                                $("#access").val(0);
                            }
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
                }
            });

            $(document.body).on("click", ".crud", function() {
                $("#loading").fadeIn();
                var id = $(this).data("id");
                var action = $(this).data("action");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudUser') }}",
                    data:{
                        id: id,
                        action: action
                    },
                    success:function(data) {
                        $("#crud-result").html(data);
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

            $(document.body).on("click", ".delete-user", function() {
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
                            url: "{{ route('deleteUser') }}",
                            data:{
                                id: id
                            },
                            success:function(data) {
                                makeTable();
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title:"@lang('حذف شد')!",
                                    text: "@lang('کاربر با موفقیت حذف شد.')",
                                    icon: "success"
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
                    }
                });
            });

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportUser') }}",
                    type: "POST",
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
