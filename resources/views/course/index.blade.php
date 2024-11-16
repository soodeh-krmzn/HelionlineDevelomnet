@extends('parts.master')

@section('title', __('کلاس ها'))

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
                                            <span class="d-none d-lg-inline-block">{{ __('خروجی اکسل') }}</span>
                                        </span>
                                    </button>
                                </div>
                                @can('create')
                                    <button type="button" class="btn add-new btn-primary ms-2 mb-sm-0 crud"
                                        data-action="create" data-bs-toggle="modal" data-bs-target="#crud">
                                        <span>
                                            <i class="bx bx-plus"></i>
                                            <span class="d-none d-lg-inline-block">{{ __('کلاس جدید') }}</span>
                                        </span>
                                    </button>
                                @endcan
                            </div>
                        </div>
                        <div class="row mx-1">
                            <div id="result" class="col"></div>
                        </div>
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

        <div class="modal fade" id="people" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content p-3 p-md-5">
                    <div id="people-result"></div>
                </div>
            </div>
        </div>

    </div>
@stop
@section('footer-scripts')
    <script type="text/javascript">
        let table = `<div class="table-responsive">
        <table id="courseTable" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>@lang('ردیف')</th>
                    <th>@lang('نام')</th>
                    <th>@lang('مربی')</th>
                    <th>@lang('قیمت')</th>
                    <th>@lang('تاریخ شروع')</th>
                    <th>@lang('تعداد جلسات')</th>
                    <th>@lang('ظرفیت')</th>
                    <th>@lang('توضیحات')</th>
                    <th>@lang('مدیریت')</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#courseTable").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableCourse') }}",
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
                        data: 'user'
                    },
                    {
                        data: 'price'
                    },
                    {
                        data: 'start'
                    },
                    {
                        data: 'sessions'
                    },
                    {
                        data: 'capacity'
                    },
                    {
                        data: 'details'
                    },
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
                    url: "{{ route('crudCourse') }}",
                    data: {
                        id: id,
                        action: action
                    },
                    success: function(data) {
                        $("#crud-result").html(data);
                        dateMask();
                        $("#loading").fadeOut();
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

            $(document.body).on("click", "#store-course", function() {
                var e = checkEmpty();
                var d = checkDate();
                if (!e && !d) {
                    $("#loading").fadeIn();

                    var action = $(this).data("action");
                    var id = $(this).data("id");
                    var name = $("#name").val();
                    var user_id = $("#user_id").val();
                    var price = $("#price").val();
                    var start = $("#start").val();
                    var sessions = $("#sessions").val();
                    var capacity = $("#capacity").val();
                    var details = $("#details").val();

                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeCourse') }}",
                        data: {
                            action: action,
                            id: id,
                            name: name,
                            user_id: user_id,
                            price: price,
                            start: start,
                            sessions: sessions,
                            capacity: capacity,
                            details: details
                        },
                        success: function(data) {
                            makeTable();
                            if (action == "create") {
                                $("#name").val("");
                                $("#user_id").val("");
                                $("#price").val("");
                                $("#start").val("");
                                $("#capacity").val("");
                                $("#sessions").val("");
                                $("#details").val("");
                            }
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
                }
            });

            $(document.body).on("click", ".delete-course", function() {
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
                            url: "{{ route('deleteCourse') }}",
                            data: {
                                id: id
                            },
                            success: function(data) {
                                makeTable();
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "{{ __('حذف شد') }}",
                                    text: "{{ __('کلاس با موفقیت حذف شد.') }}",
                                    icon: "success"
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
                    }
                });
            });

            function searchPersonL() {
                $(".searchPersonL").select2({
                    minimumInputLength: 3, // Minimum characters to start searching
                    placeholder: "@lang('انتخاب کنید')",
                    dropdownParent: $('#crud .modal-content'),
                    @if (app()->getLocale() == 'fa')
                        language: {

                            inputTooShort: function() {
                                return "لطفاً حداقل " + 3 + " کاراکتر وارد کنید";
                            },
                            noResults: function() {
                                return "نتیجه‌ای یافت نشد";
                            },
                            searching: function() {
                                return "در حال جستجو...";
                            },
                        },
                    @endif
                    allowClear: true,
                    ajax: {
                        url: "{{ route('sps') }}",
                        dataType: "json",
                        delay: 500,
                        data: function(params) {
                            return {
                                search: params.term, // Send the search term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data[0].map(function(item) {
                                    return {
                                        id: item.id, // The value to be stored
                                        text: item.name +
                                            " " +
                                            item.family +
                                            `(${item.id})`, // The text to be displayed
                                    };
                                }),
                            };
                        },
                        cache: true,
                    },
                });
            }
            $(document.body).on("click", ".register-course", function() {
                $("#loading").fadeIn();
                var id = $(this).data("id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('registerCourse') }}",
                    data: {
                        id: id
                    },
                    success: function(data) {
                        $("#crud-result").html(data);
                        $(".select2-rc").select2({
                            dropdownParent: $('#crud .modal-content')
                        });
                        searchPersonL();
                        $("#loading").fadeOut();
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", ".sync-person", function() {
                $("#loading").fadeIn();
                var action = $(this).data("action");
                var id = $(this).data("id");
                var payments = $(".payment-price").serialize();
                var details = $(".payment-details").serialize();
                if (action == "remove") {
                    var person_id = $(this).data("person_id");
                    $("#loading").fadeOut();
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
                            $.ajax({
                                type: "POST",
                                url: "{{ route('personCourse') }}",
                                data: {
                                    action: action,
                                    id: id,
                                    person_id: person_id,
                                    payments: payments,
                                    details: details
                                },
                                success: function(data) {
                                    $("#people-result").html(data);
                                    $("#loading").fadeOut();
                                    Swal.fire({
                                        title: "{{ __('حذف شد') }}",
                                        text: "{{ __('شخص با موفقیت حذف شد.') }}",
                                        icon: "success"
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
                        }
                    });
                } else if (action == "add") {
                    var e = checkEmpty("sync-person-form");
                    if (!e) {
                        if ($("#total-pay").html() == 0) {
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "@lang('اطمینان دارید؟')",
                                text: "@lang('آیا از ثبت بدون پرداخت اطمینان دارید؟')",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonText: "@lang('بله، مطمئنم.')",
                                cancelButtonText: "@lang('نه، پشیمون شدم.')"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    sync();
                                }
                            });
                        } else {
                            sync(action, id, payments, details);
                        }
                    } else {
                        $("#loading").fadeOut();
                    }
                }
            });

            function sync(action, id, payments, details) {
                var person_id = $("#person-id").val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('personCourse') }}",
                    data: {
                        action: action,
                        id: id,
                        person_id: person_id,
                        payments: payments,
                        details: details
                    },
                    success: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "{{ __('عملیات موفق') }}",
                            text: "{{ __('ثبت نام با موفقیت انجام شد.') }}",
                            icon: "success"
                        });
                        $("#sync-person-form")[0].reset();
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
            }

            $(document.body).on("click", ".people-course", function() {
                $("#loading").fadeIn();
                var id = $(this).data("id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('peopleCourse') }}",
                    data: {
                        id: id
                    },
                    success: function(data) {
                        $("#people-result").html(data);
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

            $(document.body).on("change", ".payment-price", function() {
                totalPayment();
            });

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportCourse') }}",
                    type: "POST",
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
