@extends('parts.master')

@section('title', __('شمارنده'))

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
                                            <span class="d-none d-lg-inline-block">{{ __('خروجی اکسل') }}</span>
                                        </span>
                                    </button>
                                </div>
                                @can('create')
                                    <button type="button" class="btn add-new btn-primary ms-2  mb-sm-0 crud" data-action="create" data-id="0" data-bs-toggle="modal" data-bs-target="#crud">
                                        <span>
                                            <i class="bx bx-plus"></i>
                                            <span class="d-none d-lg-inline-block">{{ __('شمارنده جدید') }}</span>
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
        <table id="counter-table" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>{{ __('ردیف') }}</th>
                    <th>{{ __('نام شمارنده') }}</th>
                    <th>{{ __('مقدار به دقیقه') }}</th>
                    <th>{{ __('مدیریت') }}</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#counter-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableCounter') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name'},
                    {data: 'min'},
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
                    url: "{{ route('crudCounter') }}",
                    data:{
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
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", "#store-counter", function() {
                var e = checkEmpty();
                if (!e) {
                    $("#loading").fadeIn();
                    var action = $(this).data("action");
                    var id = $(this).data("id");
                    var name = $("#name").val();
                    var minute = $("#minute").val();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeCounter') }}",
                        data:{
                            action: action,
                            id: id,
                            name: name,
                            minute: minute
                        },
                        success: function(data) {
                            makeTable();
                            $("#name").val("");
                            $("#minute").val("");
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "{{ __('عملیات موفق') }}",
                                text: `اطلاعات با موفقیت ${action == "create" ? "ثبت" : "ویرایش"} شد.`,
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

            $(document.body).on("click", ".delete-counter", function() {
                Swal.fire({
                    title: "{{ __('اطمینان دارید؟') }}",
                    text: "{{ __('آیا از حذف این مورد اطمینان دارید؟') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "{{ __('بله، مطمئنم.') }}",
                    cancelButtonText: "{{ __('نه، پشیمون شدم.') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading").fadeIn();
                        var id = $(this).data("id");
                        $.ajax({
                            type: "POST",
                            url: "{{ route('deleteCounter') }}",
                            data:{
                                id: id
                            },
                            success:function(data) {
                                makeTable();
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "{{ __('حذف شد') }}",
                                    text: "{{ __('شمارنده با موفقیت حذف شد.') }}",
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

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportCounter') }}",
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
