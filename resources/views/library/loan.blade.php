@extends('parts.master')

@section('title', __('کتابخانه'))

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
                                    <a href="/loan-report" class="btn btn-info ms-2 rounded">
                                        <span>
                                            <i class="bx bx-history"></i>
                                            <span class="d-none d-lg-inline-block">@lang('اصلاحات')</span>
                                        </span>
                                    </a>
                                </div>
                                @can('create')
                                    <button type="button" class="btn btn-primary ms-2 mb-3 mb-sm-0 crud" data-action="create"
                                        data-id="0" data-bs-toggle="modal" data-bs-target="#crud">
                                        <span>
                                            <i class="bx bx-plus"></i>
                                            <span class="d-none d-lg-inline-block">@lang('امانت جدید')</span>
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
        function searchPersonL() {
            // $(".searchPersonL").select2({
            //     dropdownParent: $('#crud .modal-content')
            // });
            // $(document.body).on('shown.bs.modal','#crud', function() {
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
            // $('.searchPersonL').parent().find(".select2-container:not(:first)").remove();
            // });
        }
        let table = `<div class="table-responsive">
        <table id="loanTable" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>@lang('ردیف')</th>
                    <th>@lang('نام کاربر')</th>
                    <th>@lang('نام مشتری')</th>
                    <th>@lang('نام کتاب')</th>
                    <th>@lang('تاریخ امانت')</th>
                    <th>@lang('تاریخ بازگشت')</th>
                    <th>@lang('وضعیت')</th>
                    <th>@lang('توضیحات')</th>
                    <th>@lang('مدیریت')</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#loanTable").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableBookLoan') }}",
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
                        data: 'user'
                    },
                    {
                        data: 'person'
                    },
                    {
                        data: 'book'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'return_date'
                    },
                    {
                        data: 'status'
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
                    url: "{{ route('crudBookLoan') }}",
                    data: {
                        id: id,
                        action: action
                    },
                    success: function(data) {
                        $("#crud-result").html(data);
                        dateMask();
                        $(".select2-b").select2({
                            dropdownParent: $('#crud .modal-content')
                        });
                        searchPersonL();
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

            $(document.body).on("click", "#store-loan", function() {
                var e = checkEmpty();
                if (!e) {
                    $("#loading").fadeIn();
                    var action = $(this).data("action");
                    var id = $(this).data("id");
                    var person_id = $("#person").val();
                    var book_id = $("#book").val();
                    var return_date = $("#return_date").val();
                    var status = $("#status").val();
                    var details = $("#details").val();

                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeBookLoan') }}",
                        data: {
                            action: action,
                            id: id,
                            person_id: person_id,
                            book_id: book_id,
                            return_date: return_date,
                            status: status,
                            details: details
                        },
                        success: function(data) {
                            makeTable();
                            $("#loading").fadeOut();
                            $("#name").val("");
                            $("#details").val("");
                            if (action == 'create') {
                                var text = "@lang('اطلاعات با موفقیت ثبت شد').";
                            } else {
                                var text = "@lang('اطلاعات شما با موفقیت ویرایش شد').";
                            }
                            Swal.fire({
                                title: "@lang('موفق')",
                                text: text,
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

            $(document.body).on("click", ".delete-loan", function() {
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
                            url: "{{ route('deleteBookLoan') }}",
                            data: {
                                id: id
                            },
                            success: function(data) {
                                makeTable();
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "@lang('حذف شد')!",
                                    text: "@lang('امانت با موفقیت حذف شد.')",
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
                    url: "{{ route('exportBookLoan') }}",
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

        });
    </script>
@stop
