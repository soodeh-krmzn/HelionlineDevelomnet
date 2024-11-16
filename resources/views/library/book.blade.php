@extends('parts.master')

@section('title', __('کتاب'))

@section('head-styles')

@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="card">
            <div class="card-datatable table-responsive">
                <div class="row mx-1">
                    <div class="col-12">
                        <div
                            class="mt-3 mb-3 dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center flex-wrap me-1">
                            <div class="dt-buttons btn-group flex-wrap">
                                <div class="btn-group">
                                    <button id="export" class="btn btn-success">
                                        <span>
                                            <i class="bx bxs-file-export"></i>
                                            <span class="d-none d-lg-inline-block">@lang('خروجی اکسل')</span>
                                        </span>
                                    </button>
                                    <a href="/book-loan" class="btn btn-info rounded ms-2 mb-sm-0">
                                        <span>
                                            <i class="bx bxs-purchase-tag"></i>
                                            <span class="d-none d-lg-inline-block">@lang('امانت')</span>
                                        </span>
                                    </a>
                                    @can('create')
                                        <button type="button" class="btn btn-primary ms-2 mb-sm-0 crud"
                                                data-action="create" data-id="0" data-bs-toggle="modal"
                                                data-bs-target="#crud">
                                        <span>
                                            <i class="bx bx-plus"></i>
                                            <span class="d-none d-lg-inline-block">@lang('کتاب جدید')</span>
                                        </span>
                                        </button>
                                    @endcan
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
        <table id="bookTable" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>@lang('ردیف')</th>
                    <th>@lang('نام کتاب')</th>
                    <th>@lang('نام قفسه')</th>
                    <th>@lang('محل قفسه')</th>
                    <th>@lang('نام نویسنده')</th>
                    <th>@lang('نام ناشر')</th>
                    <th>@lang('توضیحات')</th>
                    <th>@lang('مدیریت')</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#bookTable").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableBook') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name'},
                    {data: 'cage'},
                    {data: 'cage_location'},
                    {data: 'author'},
                    {data: 'publisher'},
                    {data: 'details'},
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        }

        $(document).ready(function () {

            makeTable();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let loading = `
            <div class="col text-center">
                <span class="spinner-grow" role="status" aria-hidden="true"></span>
            </div>`;

            $(document.body).on("click", ".crud", function () {
                $("#loading").fadeIn();

                var action = $(this).data("action");
                var id = $(this).data("id");

                $.ajax({
                    type: "POST",
                    url: "{{ route('crudBook') }}",
                    data: {
                        id: id,
                        action: action
                    },
                    success: function (data) {
                        $("#crud-result").html(data);
                        $("#loading").fadeOut();
                    },
                    error: function (data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('خطا')",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", "#store-book", function () {
                var e = checkEmpty();
                if (!e) {
                    $("#loading").fadeIn();
                    var action = $(this).data("action");
                    var id = $(this).data("id");
                    var name = $("#name").val();
                    var cage = $("#cage").val();
                    var cage_location = $("#cage-location").val();
                    var author = $("#author").val();
                    var publisher = $("#publisher").val();
                    var details = $("#details").val();

                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeBook') }}",
                        data: {
                            action: action,
                            id: id,
                            name: name,
                            cage: cage,
                            cage_location: cage_location,
                            author: author,
                            publisher: publisher,
                            details: details
                        },
                        success: function (data) {
                            if (action == "create") {
                                $("#name").val("");
                                $("#details").val("");
                                $("#cage").val("");
                                $("#cage-location").val("");
                                $("#author").val("");
                                $("#publisher").val("");
                            }
                            makeTable();
                            $("#loading").fadeOut();
                            if(action=='create'){
                                var text="@lang('اطلاعات با موفقیت ثبت شد').";
                            }else{
                                var text="@lang('اطلاعات شما با موفقیت ویرایش شد').";
                            }
                            Swal.fire({
                                title: "@lang('موفق')",
                                text: text,
                                icon: "success"
                            });
                        },
                        error: function (data) {
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

            $(document.body).on("click", ".delete-book", function () {
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
                            url: "{{ route('deleteBook') }}",
                            data: {
                                id: id
                            },
                            success: function (data) {
                                makeTable();
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "@lang('حذف شد')!",
                                    text: "@lang('کتاب با موفقیت حذف شد.')",
                                    icon: "success"
                                });
                            },
                            error: function (data) {
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

            $(document.body).on("click", "#export", function () {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportBook') }}",
                    type: "POST",
                    success: function () {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('موفق')",
                            icon: 'success',
                            text: "@lang('گزارش گیری انجام شد. از صفحه گزارشات اکسل میتوانید این گزارش را دانلود کنید.')"
                        });
                    },
                    error: function (data) {
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
