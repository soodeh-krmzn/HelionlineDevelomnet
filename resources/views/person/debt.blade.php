@extends('parts.master')

@section('title', __('اشخاص'))

@section('head-styles')

@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="card">
            <div class="card-datatable table-responsive">
                <div class="collapse" id="collapseExample">
                    <div class="card-header border-bottom">
                        <div class="py-3 primary-font">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">@lang('نام شخص')</label>
                                        <select name="s_person" id="s-person" class="searchPerson">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">@lang('از مبلغ بدهی')</label>
                                        <input type="text" id="s-from-price" class="form-control just-numbers"
                                            placeholder="@lang('از مبلغ بدهی')...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">@lang('تا مبلغ بدهی')</label>
                                        <input type="text" id="s-to-price" class="form-control just-numbers"
                                            placeholder="@lang('تا مبلغ بدهی')...">
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex align-items-end justify-content-start">
                                    <div class="form-group">
                                        <button class="btn btn-success" id="search-debt">@lang('جستجو')</button>
                                        <a href="{{ route('debtPerson') }}" class="btn btn-info" id="show-all">
                                            @lang('نمایش همه')
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mx-1 my-3">
                    <div class="col">
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            <i class='bx bx-search me-1'></i> <span
                                class="d-none d-lg-inline-block">@lang('جستجو')</span>
                        </button>
                    </div>
                    <div class="col">
                        <div
                            class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                            <div class="dt-buttons btn-group flex-wrap">
                                <div class="btn-group">
                                    <button id="export" class="btn btn-success">
                                        <span>
                                            <i class="bx bxs-file-export"></i>
                                            <span class="d-none d-lg-inline-block">@lang('خروجی اکسل')</span>
                                        </span>
                                    </button>
                                </div>
                                @can('unlimited')
                                    <button class="btn btn-warning remove-debt ms-2" data-id="0" data-action="all">
                                        @lang('صفر کردن همه')</button>
                                @endcan
                                @can('create')
                                    <button type="button" class="btn add-new btn-primary ms-2 crud" data-action="create"
                                        data-id="0" data-bs-toggle="modal" data-bs-target="#crud">
                                        <span>
                                            <i class="bx bx-plus"></i>
                                            <span class="d-none d-lg-inline-block">@lang('پرداختی جدید')</span>
                                        </span>
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mx-1">
                    <div id="result"></div>
                </div>
                <div class="row mx-1">
                    <div class="col">
                        <div id="sum-container" class="alert alert-warning my-3 text-center">@lang('مجموع کل'): <span
                                id="sum"></span></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="crud" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-3 p-md-5">
                <div id="crud-result"></div>
            </div>
        </div>
    </div>
@stop
@section('footer-scripts')
    <script type="text/javascript">
        searchPerson();
        let table = `<div class="table-responsive">
        <table id="debtTable" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>@lang('ردیف')</th>
                    <th>@lang('شماره عضویت')</th>
                    <th>@lang('نام و نام خانوادگی')</th>
                    <th>@lang('موبایل')</th>
                    <th>@lang('مبلغ')</th>
                    <th>@lang('مدیریت')</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#debtTable").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableDebt') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        searchable: false
                    },
                    {
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'mobile'
                    },
                    {
                        data: 'balance'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
            $.ajax({
                url: "{{ route('sumDebt') }}",
                type: "GET",
                data: fields ?? {},
                success: function(data) {
                    $("#sum").html(addCommas(data));
                }
            });
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            makeTable();

            let data;

            $(document.body).on("click", "#search-debt", function() {
                $("#loading").fadeIn();
                var person_id = $("#s-person").val();
                var from_price = $("#s-from-price").val();
                var to_price = $("#s-to-price").val();
                var data = {
                    "person_id": person_id,
                    "from_price": from_price,
                    "to_price": to_price,
                    "filter_search": true
                }
                makeTable(data);
                window['data'] = data;
                $("#loading").fadeOut();
            });

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportDebt') }}",
                    type: "POST",
                    data: window['data'],
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

            $(document.body).on("click", ".remove-debt", function() {

                var action = $(this).data("action");
                if (action == 'all') {
                    Swal.fire({
                        title: "@lang('اطمینان دارید؟')",
                        text: "@lang('بدهی تمام کاربران، بدون پرداخت هزینه صفر میشود.')",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "@lang('بله، مطمئنم.')",
                        cancelButtonText: "@lang('نه، پشیمون شدم.')"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $("#loading").fadeIn();
                            var id = $(this).data("id");
                            var action = $(this).data("action");
                            $.ajax({
                                type: "POST",
                                url: "{{ route('removeDebt') }}",
                                data: {
                                    id: id,
                                    action: action
                                },
                                success: function(data) {
                                    $("#result").html(data);
                                    $("#loading").fadeOut();
                                    Swal.fire({
                                        title: "@lang('موفق')",
                                        icon: "success",
                                        text: "@lang('با موفقیت انجام شد.')"
                                    });
                                    location.reload();
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
                } else {
                    $("#loading").fadeIn();
                    var action = 'remove-debt';
                    var id = 0;
                    $.ajax({
                        type: "POST",
                        url: "{{ route('crudPayment') }}",
                        data: {
                            id: id,
                            action: action,
                            debt: {
                                price: $(this).attr('data-balance'),
                                person_id: $(this).attr('data-id'),
                                person_name: $(this).attr('data-person_name'),
                            }
                        },
                        success: function(data) {
                            $("#crud-result").html(data);
                            $(".select2-p").select2({
                                dropdownParent: $('#crud .modal-content')
                            });
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
                }

            });

            $(document.body).on("click", "#store-payment", function(event) {
                event.preventDefault();
                var e = checkEmpty("store-payment-form");
                if (!e) {
                    $("#loading").fadeIn();
                    var action = $(this).data("action");
                    var id = $(this).data("id");
                    var prices = $(".payment-price").serialize();
                    var details = $(".payment-details").serialize();
                    var person_id = $("#person-id").val();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storePayment') }}",
                        data: {
                            id: id,
                            action: action,
                            prices: prices,
                            details: details,
                            person_id: person_id
                        },
                        success: function(data) {
                            console.log(data);
                            $(".payment-price").val('');
                            $(".payment-details").val('');
                            $("#person-id").val('');
                            makeTable();
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "@lang('موفق')",
                                text: "@lang('اطلاعات با موفقیت ثبت شد.')",
                                icon: "success"
                            });
                        },
                        error: function(data) {
                            console.log(data);
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
                var action = $(this).data("action");
                var id = $(this).data("id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudPayment') }}",
                    data: {
                        id: id,
                        action: action
                    },
                    success: function(data) {
                        $("#crud-result").html(data);
                        $(".select2-p").select2({
                            dropdownParent: $('#crud .modal-content')
                        });
                        searchPersonM();
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

        });
    </script>
@stop
