@extends('parts.master')

@section('title', __('نقش های کاربری'))

@section('head-styles')

@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="card">
            <div class="card-datatable table-responsive">
                <div class="row mx-1">
                    <div class="col-12">
                        <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center flex-wrap me-1">
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
                                    <button type="button" class="btn add-new btn-primary ms-2 mt-3 mb-3 crud" data-action="create" data-id="0" data-bs-toggle="modal" data-bs-target="#crud">
                                        <span>
                                            <i class="bx bx-plus"></i>
                                            <span class="d-none d-lg-inline-block">@lang('نقش جدید')</span>
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

        <div class="modal fade" id="menu" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content p-3 p-md-5">
                    <div id="menu-result"></div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer-scripts')
    <script type="text/javascript">

    let table = `<div class="table-responsive">
        <table id="user-group-table" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>@lang('ردیف')</th>
                    <th>@lang('نقش')</th>
                    <th>@lang('توضیحات')</th>
                    <th>@lang('مدیریت')</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#user-group-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableUserGroup') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name'},
                    {data: 'description'},
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

            $(document.body).on("click", ".crud", function() {
                $("#loading").fadeIn();
                var action = $(this).data("action");
                var id = $(this).data("id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudUserGroup') }}",
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

            $(document.body).on("click", ".menu-group", function() {
                $("#loading").fadeIn();
                var id = $(this).data("id");
                $.ajax({
                    type: "GET",
                    url: "{{ route('menuUserGroup') }}",
                    data:{
                        id: id
                    },
                    success:function(data) {
                        $("#menu-result").html(data);
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

            $(document.body).on("click", "#store-group-menus", function() {
                $("#loading").fadeIn();
                var id = $(this).data("id");
                var menus = $(".menu").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ route('storeMenuUserGroup') }}",
                    data:{
                        id: id,
                        menus: menus
                    },
                    success:function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('موفق')",
                            text: "@lang('اطلاعات با موفقیت ثبت شد.')",
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
            });

            $(document.body).on("click", "#store-group", function() {
                var e = checkEmpty();
                if (!e) {
                    $("#loading").fadeIn();
                    var name = $("#name").val();
                    var details = $("#details").val();
                    var action = $(this).data("action");
                    var id = $(this).data("id");
                    var permissions = $(".permission").serialize();

                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeUserGroup') }}",
                        data: {
                            action: action,
                            id: id,
                            name: name,
                            details:details,
                            permissions: permissions
                        },
                        success: function(data) {
                            makeTable();
                            $("#loading").fadeOut();
                            if (action == "create") {
                                var text= "@lang('اطلاعات با موفقیت ثبت شد.')";
                            }else{
                                var text= "@lang('اطلاعات با موفقیت ویرایش شد').";
                            }
                            Swal.fire({
                                title: "{{ __('عملیات موفق') }}",
                                text:text,
                                icon: "success"
                            });
                            if (action == "create") {
                                $("#name").val("");
                                $("#details").val("");
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

            $(document.body).on("click", ".delete-group", function() {
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
                            url: "{{ route('deleteUserGroup') }}",
                            data:{
                                id: id
                            },
                            success:function(data) {
                                makeTable();
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title:"@lang('حذف شد')!",
                                    text: "@lang('نقش با موفقیت حذف شد.')",
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
                    url: "{{ route('exportUserGroup') }}",
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

            $(document.body).on("change", "#check-all", function() {
                if ($(this).is(":checked")) {
                    $(".menu-check").prop('checked', true);
                } else {
                    $(".menu-check").prop('checked', false);
                }
            });


        });
    </script>
@stop
