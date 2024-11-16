@extends('parts.master')

@section('title', 'سوابق پرداخت')

@section('head-styles')

@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="card">
            <div class="card-datatable table-responsive">
                <div class="row mx-1">
                    <div class="col-12">
                        <div class="mt-3 mb-3 dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center flex-wrap me-1">
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
                        <div class="row mx-1">
                            <div id="result" class="col"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer-scripts')
    <script type="text/javascript">

        let table = `<div class="table-responsive">
        <table id="payment-table" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>@lang('ردیف')</th>
                    <th>@lang('تاریخ')</th>
                    <th>@lang('وضعیت')</th>
                    <th>رسید دیجیتال</th>
                    <th>@lang('مبلغ')</th>
                    <th>@lang('نوع پرداخت')</th>
                    <th>درگاه پرداخت</th>
                    <th>@lang('نام کاربری')</th>
                    <th>شماره کارت</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#payment-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableMyPayment') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                order:[1,'desc'],
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'created_at'},
                    {data: 'status'},
                    {data: 'ref_id'},
                    {data: 'price'},
                    {data: 'type'},
                    {data: 'driver'},
                    {data: 'username'},
                    {data: 'card'}
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

            $(document.body).on("click", "#update", function() {
                $("#loading").fadeIn();
                $.ajax({
                    type: "GET",
                    url: "{{ route('updateMyPayment') }}",
                    success: function(data) {
                        makeTable();
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('موفق')",
                            text: "با موفقیت بروزرسانی شد.",
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

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportMyPayment') }}",
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



