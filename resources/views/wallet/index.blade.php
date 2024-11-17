@extends('parts.master')

@section('title', __('کیف پول'))

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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('از مبلغ') }}</label>
                                    <input type="text" data-id="s-from-price"
                                        class="form-control money-filter just-numbers" placeholder="{{ __('از مبلغ') }}...">
                                    <input type="hidden" id="s-from-price" class="form-control just-numbers">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('تا مبلغ') }}</label>
                                    <input type="text" data-id="s-to-price"
                                        class="form-control money-filter just-numbers" placeholder="{{ __('تا مبلغ') }}...">
                                    <input type="hidden" id="s-to-price" class="form-control just-numbers">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('از تاریخ') }}</label>
                                    <input type="text" id="s-from-date" class="form-control date-mask"
                                        placeholder="1400/01/01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('تا تاریخ') }}</label>
                                    <input type="text" id="s-to-date" class="form-control date-mask"
                                        placeholder="1400/01/01">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('شخص') }}</label>
                                    <select name="s-person-id" id="s-person-id" class="searchPerson">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-end">
                                <div class="form-group">
                                    <button class="btn btn-success" id="search-wallet">{{ __('جستجو') }}</button>
                                    <a href="{{ route('wallet') }}" class="btn btn-info"
                                        id="show-all">{{ __('نمایش همه') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <div class="row mx-1 my-3">
                    <div class="col">
                        <button id="toggle-search" class="btn btn-primary me-1" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            <i class='bx bx-search me-1'></i> <span
                                class="d-none d-lg-inline-block">{{ __('جستجو') }}</span>
                        </button>
                    </div>
                    <div class="col">
                        <div
                            class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center flex-wrap me-1">
                            <div class="dt-buttons btn-group flex-wrap">
                                <div class="btn-group">
                                    <button id="export" class="btn btn-success">
                                        <span>
                                            <i class="bx bxs-file-export"></i>
                                            <span class="d-none d-lg-inline-block">{{ __('خروجی اکسل') }}</span>
                                        </span>
                                    </button>
                                    <a href="/wallet-setting" class="btn btn-info rounded ms-2">
                                        <span>
                                            <i class="bx bxs-coupon"></i>
                                            <span class="d-none d-lg-inline-block">{{ __('تنظیمات') }}</span>
                                        </span>
                                    </a>
                                    @can('create')
                                        <button type="button" class="btn add-new btn-primary ms-2 crud" data-action="create"
                                            data-id="0" data-bs-toggle="modal" data-bs-target="#crud">
                                            <span>
                                                <i class="bx bx-plus"></i>
                                                <span class="d-none d-lg-inline-block">{{ __('شارژ جدید') }}</span>
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
        <table id="wallet-table" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>@lang('ردیف')</th>
                    <th>@lang('نام شخص')</th>
                    <th>@lang('مبلغ')</th>
                    <th>@lang('ضریب هدیه')</th>
                    <th>
                        @lang('مبلغ نهایی')
                        <br/>
                        <small>(@lang('با احتساب ضریب هدیه'))</small>
                    </th>
                    <th>@lang('موجودی')</th>
                    <th>@lang('تاریخ شارژ')</th>
                    <th>@lang('تاریخ انقضاء')</th>
                    <th>@lang('توضیحات')</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#wallet-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableWallet') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                order: [6, 'desc'],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'person'
                    },
                    {
                        data: 'price'
                    },
                    {
                        data: 'gift_percent'
                    },
                    {
                        data: 'final_price'
                    },
                    {
                        data: 'balance'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'expire'
                    },
                    {
                        data: 'description'
                    }
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

            function store(wallet) {
                $("#loading").fadeIn();
                var action = $(wallet).data("action");
                var id = $(wallet).data("id");
                var prices = $(".payment-price").serialize();
                var details = $(".payment-details").serialize();
                var person_id = $("#person-id").val();
                var description = $("#description").val();
                var price = $("#price").val();
                var gift = $("#gift").is(":checked");
                $.ajax({
                    type: "POST",
                    url: "{{ route('storeWallet') }}",
                    data: {
                        id: id,
                        action: action,
                        prices: prices,
                        details: details,
                        person_id: person_id,
                        price: price,
                        description: description,
                        gift: gift
                    },
                    success: function(data) {
                        makeTable();
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
            }

            $(document.body).on("click", "#store-wallet", function(event) {
                event.preventDefault();
                var e = checkEmpty("store-wallet-form");
                if (!e) {
                    if ($("#total-pay").html() == 0) {
                        Swal.fire({
                            title: "@lang('اطمینان دارید؟')",
                            text: "@lang('آیا از ثبت بدون پرداخت اطمینان دارید؟')",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: "@lang('بله، مطمئنم.')",
                            cancelButtonText: "@lang('نه، پشیمون شدم.')"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                store($(this));
                            }
                        });
                    } else {
                        store($(this));
                    }
                }
            });

            $(document.body).on("click", "#toggle-search", function() {
                $(".select2").select2();
            });

            $(document.body).on("click", ".crud", function() {
                $("#loading").fadeIn();
                var action = $(this).data("action");
                var id = $(this).data("id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudWallet') }}",
                    data: {
                        id: id,
                        action: action
                    },
                    success: function(data) {
                        $("#crud-result").html(data);
                        $(".select2-w").select2({
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

            $(document.body).on("change", "#price", function() {

                let price=$(this).val();
                // $('.default-payment-input').val(price);
                $('.default-payment-price').val(price);
                $('#defaultPrice').val(price);
            });
            $(document.body).on("click", "#search-wallet", function() {
                var d = checkDate();
                if (d) {
                    Swal.fire({
                        title: "@lang('اخطار')",
                        icon: 'error',
                        text: 'تاریخ وارد شده معتبر نمیباشد. (نمونه معتبر 1401/01/01)'
                    });
                    return;
                }
                $("#loading").fadeIn();
                var person_id = $("#s-person-id").val();
                var from_price = $("#s-from-price").val();
                var to_price = $("#s-to-price").val();
                var from_date = $("#s-from-date").val();
                var to_date = $("#s-to-date").val();
                var data = {
                    'person_id': person_id,
                    'from_price': from_price,
                    'to_price': to_price,
                    'from_date': from_date,
                    'to_date': to_date,
                    'filter_search': true
                }
                window["data"] = data;
                makeTable(data);
                $("#loading").fadeOut();
            });

            $(document.body).on("change", ".payment-price", function() {
                totalPayment();
            });

            $(document.body).on("keyup", "#price-input", function() {
                var def = '{{ $defaultPaymentType }}';
                var value = $(this).val();
                $(`input[data-id=${def}-pay]`).val(value);
                var value2 = value.replace(/[^0-9]/g, '');
                $(`#${def}-pay`).val(value2);
                totalPayment();
            });

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportWallet') }}",
                    type: "POST",
                    data: window['data'],
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
