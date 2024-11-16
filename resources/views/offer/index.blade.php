@extends('parts.master')

@section('title', __('کدهای تخفیف'))

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
                                <label class="form-label">{{ __('کد تخفیف') }}</label>
                                @php
                                    $global_offer = $setting->getSetting('global_offer');
                                @endphp
                                <select id="global_offer" class="form-select" style="min-width: 165px;">
                                    <option value="">{{ __('بدون تخفیف') }}</option>
                                    @foreach ($offers as $offer)
                                        <option @selected($global_offer == $offer->id) value="{{ $offer->id }}">{{ $offer->name }}
                                        </option>
                                    @endforeach
                                </select>
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
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            <i class='bx bx-money-withdraw me-1'></i>
                            <span class="d-none d-lg-inline-block">
                                @lang('تخفیف پیشفرض')
                            </span>
                        </button>
                    </div>
                    <div class="col">
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
                                    <a href="/offer-report" class="btn btn-info ms-2 rounded">
                                        <span>
                                            <i class="bx bx-history"></i>
                                            <span class="d-none d-lg-inline-block">{{ __('اصلاحات') }}</span>
                                        </span>
                                    </a>
                                </div>
                                @can('create')
                                    <button type="button" class="btn add-new btn-primary ms-2 mb-sm-0 crud"
                                        data-action="create" data-bs-toggle="modal" data-bs-target="#crud">
                                        <span>
                                            <i class="bx bx-plus"></i>
                                            <span class="d-none d-lg-inline-block">{{ __('کد تخفیف جدید') }}</span>
                                        </span>
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
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

    </div>
@stop
@section('footer-scripts')
<script src="{{asset('assets/js/RMain.js')}}"></script>
    <script type="text/javascript">
        let table = `<div class="table-responsive">
        <table id="offerTable" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>{{ __('ردیف') }}</th>
                    <th>{{ __('نام کد') }}</th>
                    <th>{{ __('نوع تخفیف') }}</th>
                    <th>{{ __('مقدار تخفیف') }}</th>
                    <th>{{ __('حداقل مبلغ') }}</th>
                    <th>{{ __('توضیحات') }}</th>
                    <th>{{ __('تعداد دفعات استفاده') }}</th>
                    <th>{{ __('مدیریت') }}</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#offerTable").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableOffer') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false, searchable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'type'
                    },
                    {
                        data: 'per'
                    },
                    {
                        data: 'min_price'
                    },
                    {
                        data: 'details'
                    },
                    {
                        data: 'times_used'
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
                    url: "{{ route('crudOffer') }}",
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
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", "#store-offer", function() {
                var e = checkEmpty();
                if (!e) {
                    $("#loading").fadeIn();

                    var action = $(this).data("action");
                    var id = $(this).data("id");
                    var name = $("#name").val();
                    var type = $("#type").val();
                    var per = $("#per").val();
                    var min_price = $("#min-price").val();
                    var calc = $("#calc").val();
                    var details = $("#details").val();

                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeOffer') }}",
                        data: {
                            action: action,
                            id: id,
                            name: name,
                            type: type,
                            per: per,
                            min_price: min_price,
                            calc: calc,
                            details: details
                        },
                        success: function(data) {
                            makeTable();
                            if (action == "create") {
                                $("#name").val("");
                                $("#type").val("");
                                $("#per").val("");
                                $("#min-price").val("");
                                $("#details").val("");
                            }
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

            $(document.body).on("click", ".delete-offer", function() {
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
                            url: "{{ route('deleteOffer') }}",
                            data: {
                                id: id
                            },
                            success: function(data) {
                                makeTable();
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "{{ __('حذف شد') }}",
                                    text: "{{ __('کد با موفقیت حذف شد.') }}",
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
            $(document.body).on("click", "#save-setting", function() {
                actionAjax(window.location.href,{
                    global_offer:$('#global_offer').val()
                },'موفق','تخفیف پیش فرض انتخاب شد');
            });

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportOffer') }}",
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
