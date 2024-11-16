@extends('parts.master')

@section('title', __('نظرسنجی'))

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
                            class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center flex-wrap me-1">
                            <div class="dt-buttons btn-group flex-wrap">
                                @can('create')
                                    <button type="button" class="btn add-new btn-primary mt-3 mb-3 crud" data-action="create"
                                        data-id="0" data-bs-toggle="modal" data-bs-target="#crud">
                                        <span>
                                            <i class="bx bx-plus"></i>
                                            <span class="d-none d-lg-inline-block">@lang('نظرسنجی جدید')</span>
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
            <div class="modal-dialog modal-lg modal-dialog-centered">
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
        <table id="vote-table" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>@lang('ردیف')</th>
                    <th>@lang('عنوان')</th>
                    <th>@lang('توضیحات')</th>
                    <th>@lang('وضعیت')</th>
                    <th>@lang('مدیریت')</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#vote-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableVote') }}",
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
                        data: 'name'
                    },
                    {
                        data: 'details'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        }

        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Start Vote

            makeTable();

            $(document.body).on("click", ".crud", function() {
                $("#loading").fadeIn();
                var action = $(this).data("action");
                var id = $(this).data("id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudVote') }}",
                    data: {
                        id: id,
                        action: action
                    },
                    success: function(data) {
                        $("#crud-result").html(data);
                        $("#loading").fadeOut();
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('اخطار')",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", "#store-vote", function() {
                $("#loading").fadeIn();
                var name = $("#name").val();
                var status = $("#status").val();
                var details = $("#details").val();
                var action = $(this).data("action");
                var id = $(this).data("id");
                if (name == "") {
                    $("#loading").fadeOut();
                    Swal.fire({
                        title: "@lang('اخطار')",
                        text: "@lang('لطفا فیلد های ستاره دار را پر کنید.')",
                        icon: "error"
                    });
                } else {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeVote') }}",
                        data: {
                            action: action,
                            id: id,
                            name: name,
                            status: status,
                            details: details
                        },
                        success: function(data) {
                            makeTable();
                            $("#name").val("");
                            $("#details").val("");
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "@lang('ثبت شد.')",
                                text: "@lang('اطلاعات با موفقیت ثبت شد.')",
                                icon: "success"
                            });
                        },
                        error: function(data) {
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "@lang('اخطار')",
                                text: data.responseJSON.message,
                                icon: "error"
                            });
                        }
                    });
                }
            });

            $(document.body).on("click", ".delete-vote", function() {
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
                            url: "{{ route('deleteVote') }}",
                            data: {
                                id: id
                            },
                            success: function(data) {
                                makeTable();
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "@lang('حذف شد')!",
                                    text: "@lang('فرم نظرسنجی با موفقیت حذف شد.')",
                                    icon: "success"
                                });
                            },
                            error: function(data) {
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "@lang('اخطار')",
                                    text: data.responseJSON.message,
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });

            // End Vote

            // Start Question

            $(document.body).on("click", ".crud-questions", function() {
                $("#loading").fadeIn();
                var vote_id = $(this).data("vote_id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudQuestion') }}",
                    data: {
                        vote_id: vote_id
                    },
                    success: function(data) {
                        $("#crud-result").html(data);
                        $("#loading").fadeOut();
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('اخطار')",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", "#store-question", function() {
                $("#loading").fadeIn();
                var action = $(this).data("action");
                var id = $(this).data("id");
                var vote_id = $(this).data("vote_id");
                var title = $("#title").val();
                var type = $("#type").find("option:selected").val();
                var display_order = $("#display-order").find("option:selected").val();

                if (title == "") {
                    $("#loading").fadeOut();
                    Swal.fire({
                        title: "@lang('اخطار')",
                        text: "@lang('لطفا فیلد های ستاره دار را پر کنید.')",
                        icon: "error"
                    });
                } else {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeQuestion') }}",
                        data: {
                            action: action,
                            id: id,
                            vote_id: vote_id,
                            title: title,
                            type: type,
                            display_order: display_order
                        },
                        success: function(data) {
                            $("#crud-result").html(data);
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "@lang('ثبت شد.')",
                                text: "@lang('اطلاعات با موفقیت ثبت شد.')",
                                icon: "success"
                            });
                        },
                        error: function(data) {
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "@lang('اخطار')",
                                text: data.responseJSON.message,
                                icon: "error"
                            });
                        }
                    });
                }
            });

            $(document.body).on("click", ".edit-question", function() {
                $("#loading").fadeIn();
                $("#store-question").attr("data-id", $(this).data("id"));
                $("#title").val($(this).data("title"));
                $("#type").val($(this).data("type"));
                $("#display-order").val($(this).data("display_order"));
                $("#store-question").attr("data-action", "update");
                $("#store-question").removeClass("btn-success");
                $("#store-question").addClass("btn-warning");
                $("#store-question").html("@lang('ویرایش اطلاعات')");
                $("#loading").fadeOut();
            });

            $(document.body).on("click", "#ignore-question", function() {
                $("#loading").fadeIn();
                $("#title").val("");
                $("#type").val("");
                $("#display-order").val("");
                $("#store-question").attr("data-action", "create");
                $("#store-question").removeClass("btn-warning");
                $("#store-question").addClass("btn-success");
                $("#store-question").html("@lang('ثبت اطلاعات')");
                $("#loading").fadeOut();
            });

            $(document.body).on("click", ".delete-question", function() {
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
                            url: "{{ route('deleteQuestion') }}",
                            data: {
                                id: id
                            },
                            success: function(data) {
                                $("#crud-result").html(data);
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "@lang('حذف شد')!",
                                    text: "@lang('سوال نظرسنجی با موفقیت حذف شد.')",
                                    icon: "success"
                                });
                            },
                            error: function(data) {
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "@lang('اخطار')",
                                    text: data.responseJSON.message,
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });

            // End Question

        });
    </script>
@stop
