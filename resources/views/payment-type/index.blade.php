@extends('parts.master')

@section('title', __('روش های پرداخت'))

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
                            <div class="col-md-6" id="selectable">
                                {{ $paymentType->select() }}
                            </div>
                            <div class="col-md-6 d-flex align-items-end justify-content-start">
                                <div class="form-group">
                                    <button class="btn btn-success" id="save-setting">@lang('ثبت')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <div class="row mx-1">
                    <div class="col my-3">
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            <i class='bx bx-money-withdraw me-1'></i>
                            <span class="d-none d-lg-inline-block">
                                @lang('روش پرداخت پیشفرض')
                            </span>
                        </button>
                    </div>
                    <div class="col">
                        <div class="mt-3 mb-3 dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center flex-wrap me-1">
                            <div class="dt-buttons btn-group flex-wrap">
                                <div class="btn-group">
                                    <button id="export" class="btn btn-success">
                                        <span>
                                            <i class="bx bxs-file-export"></i>
                                            <span class="d-none d-lg-inline-block">@lang("خروجی اکسل")</span>
                                        </span>
                                    </button>
                                </div>
                                @can('create')
                                    <button type="button" class="btn add-new btn-primary ms-2  mb-sm-0 crud" data-action="create" data-id="0" data-bs-toggle="modal" data-bs-target="#crud">
                                        <span>
                                            <i class="bx bx-plus"></i>
                                            <span class="d-none d-lg-inline-block">@lang("روش پرداخت جدید")</span>
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
        <table id="payment-type-table" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>@lang("ردیف")</th>
                    <th>@lang("نام")</th>
                    <th>@lang("برچسب")</th>
                    <th>@lang("توضیحات")</th>
                    <th>@lang("وضعیت")</th>
                    <th>@lang("مشمول امتیاز باشگاه")</th>
                    <th>@lang("مدیریت")</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#payment-type-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tablePaymentType') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false, searchable: false
                    },
                    {data: 'name'},
                    {data: 'label'},
                    {data: 'details'},
                    {data: 'status'},
                    {data: 'club'},
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

            makeTable();

            $(document.body).on("click", ".crud", function() {
                $("#loading").fadeIn();
                var action = $(this).data("action");
                var id = $(this).data("id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudPaymentType') }}",
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

            $(document.body).on("click", "#store-payment-type", function() {
                var e = checkEmpty();
                if (!e) {
                    $("#loading").fadeIn();
                    var name = $("#name").val();
                    var label = $("#label").val();
                    var details = $("#details").val();
                    var status = $("#status").find("option:selected").val();
                    var club = $("#club").find("option:selected").val();
                    var action = $(this).data("action");
                    var id = $(this).data("id");
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storePaymentType') }}",
                        data: {
                            id: id,
                            action: action,
                            name: name,
                            label: label,
                            details: details,
                            status: status,
                            club: club
                        },
                        success: function(data) {
                            $("#selectable").html(data);
                            makeTable()
                            $("#name").val("");
                            $("#label").val("");
                            $("#details").val("");
                            $("#loading").fadeOut();
                            if (action == "create") {
                                var text= "@lang('اطلاعات با موفقیت ثبت شد.')";
                            }else{
                                var text= "@lang('اطلاعات با موفقیت ویرایش شد').";
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

            $(document.body).on("click", ".delete-payment-type", function() {
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
                            url: "{{ route('deletePaymentType') }}",
                            data:{
                                id: id
                            },
                            success:function(data) {
                                $("#selectable").html(data);
                                makeTable();
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title:"@lang('حذف شد')!",
                                    text: "@lang('روش پرداخت با موفقیت حذف شد.')",
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

            $(document.body).on("click", "#save-setting", function() {
                Swal.fire({
                    title: "@lang('لطفا صبر نمایید')...",
                    text: "@lang('در حال پردازش هستیم.')",
                    icon: "info",
                    showCancelButton: false,
                    showConfirmButton: false,
                    allowOutsideClick: false
                });
                var list = $(".setting").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ route('updateSetting') }}",
                    data: {
                        list: list
                    },
                    success:function(data) {
                        Swal.fire({
                            title: "@lang('ذخیره شد.')",
                            text: "@lang('تنظیمات با موفقیت ذخیره شد.')",
                            icon: "success"
                        });
                    }
                });
            });

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportPaymentType') }}",
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
