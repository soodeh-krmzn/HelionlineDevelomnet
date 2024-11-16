@extends('parts.master')

@section('title', __('مرور حساب'))

@section('head-styles')

@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="card">
            <div class="collapse " id="collapseExample">
                <div class="card-header border-bottom">
                    <div id="filter-box" class="py-3 primary-font">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group select-box">
                                    <label class="form-label">@lang('شخص')</label>
                                    <select name="s-person-id" id="s-person-id" class="searchPerson">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('نوع')</label>
                                    <select name="s-type" id="s-type" class="form-select">
                                        <option value="">@lang('همه')</option>
                                        @foreach ($payment->types as $key => $value)
                                            <option value="{{ $key }}">{{ __($value) }}</option>
                                        @endforeach
                                        @foreach ($payment_types as $payment_type)
                                            <option value="{{ $payment_type->name }}">{{ $payment_type->label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('از مبلغ')</label>
                                    <input type="text" data-id="s-from-price"
                                        class="form-control money-filter just-numbers" placeholder="@lang('از مبلغ')...">
                                    <input type="hidden" id="s-from-price" class="form-control just-numbers">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('تا مبلغ')</label>
                                    <input type="text" data-id="s-to-price"
                                        class="form-control money-filter just-numbers" placeholder="@lang('تا مبلغ')...">
                                    <input type="hidden" id="s-to-price" class="form-control just-numbers">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('از تاریخ')</label>
                                    <input type="text" id="s-from-date" class="form-control date-mask "
                                        placeholder="1400/01/01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('تا تاریخ')</label>
                                    <input type="text" id="s-to-date" class="form-control date-mask "
                                        placeholder="1400/01/01">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-end">
                                <div class="form-group">
                                    <button class="btn btn-success" id="search-payment">@lang('جستجو')</button>
                                    <button class="btn btn-info" id="show-all">@lang('نمایش همه')</button>
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
                            <i class='bx bx-search me-1'></i> <span class="d-none d-lg-inline-block">@lang('جستجو')</span>
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
                                            <span class="d-none d-lg-inline-block">@lang('خروجی اکسل')</span>
                                        </span>
                                    </button>
                                    <a href="/payment-report" class="btn btn-info ms-2 rounded">
                                        <span>
                                            <i class="bx bx-history"></i>
                                            <span class="d-none d-lg-inline-block">@lang('اصلاحات')</span>
                                        </span>
                                    </a>
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
                </div>
                <div class="row mx-1">
                    <div id="result" class="col"></div>
                </div>
                <div class="row mx-1">
                    <div class="col">
                        <div id="sum-container" class="alert alert-warning my-3 text-center">@lang('تراز'): <span
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

    <!-- Include Select2 JS -->
    <script type="text/javascript">
        window.confirmTitle = "{{ __('اطمینان دارید؟') }}";
        window.confirmButtonText = "{{ __('بله، مطمئنم.') }}";
        window.cancelButtonText = "{{ __('نه، پشیمون شدم.') }}";
        let table = `<div class="table-responsive">
        <table id="payment-table" class="table table-hover border-top">
            <thead>
                <tr>
                    <td>@lang('ردیف')</td>
                    <td>@lang('نام شخص')</td>
                    <td>@lang('مبلغ')</td>
                    <td>@lang('نوع پرداخت')</td>
                    <td>@lang('تاریخ')</td>
                    <td>@lang('توضیحات')</td>
                    <td>@lang('مدیریت')</td>
                </tr>
            </thead>
        </table></div>`;




        function sumPayment(fields) {
            $.ajax({
                url: "{{ route('sumPayment') }}",
                type: "GET",
                data: fields ?? {},
                success: function(data) {
                    console.log(fields);
                    $("#sum").html(addCommas(data));
                }
            });
        }

        function makeTable(fields = null, refresh = false) {
            $("#result").html(table);
            var data_table = $("#payment-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tablePayment') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                order: [
                    [4, 'desc'],
                    [2, 'desc']
                ],
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
                        data: 'type'
                    },
                    {
                        data: 'created_at'
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
            sumPayment(fields)
            return data_table;
        }


        $(document).ready(function() {

            let data;

            window.data_table = makeTable();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $(document.body).on("click", ".remove-payment", function() {
                let self = $(this);
                confirmAction("@lang('عملیات حذف پرداخت.')", function() {
                    actionAjax("{{ route('removePayment') }}", {
                            payment_id: self.data('id')
                        }, '{{ __('موفق') }}', "@lang('مورد خواسته شده حذف شد')",
                        function() {
                            window.data_table.ajax.reload(function() {
                                sumPayment(window['data'])
                            }, false);
                        });
                });
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
                    var doctype = $("#doctype").val();
                    var paymentTypeSelect = $("#paymentTypeSelect").val();
                    // console.log(paymentTypeSelect);
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storePayment') }}",
                        data: {
                            id: id,
                            action: action,
                            prices: prices,
                            details: details,
                            person_id: person_id,
                            paymentTypeSelect: paymentTypeSelect,
                            doctype: doctype
                        },
                        success: function(data) {
                            $(".payment-price").val('');
                            $(".payment-details").val('');
                            $("#person-id").val('');
                            // makeTable(null, true);
                            window.data_table.ajax.reload(function() {
                                sumPayment(window['data'])
                            }, false);
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

            $(document.body).on("click", "#search-payment", function() {
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
                var type = $("#s-type").val();
                var from_price = $("#s-from-price").val();
                var to_price = $("#s-to-price").val();
                var from_date = $("#s-from-date").val();
                var to_date = $("#s-to-date").val();
                var data = {
                    "person_id": person_id,
                    "type": type,
                    "from_price": from_price,
                    "to_price": to_price,
                    "from_date": from_date,
                    "to_date": to_date,
                    "filter_search": true
                }
                window['data'] = data;

                window.data_table = makeTable(data);
                $("#loading").fadeOut();
            });

            $(document.body).on("change", ".payment-price", function() {
                totalPayment();
            });
            $(document.body).on("click", "#show-all", function() {
                var data = {
                    'all': true
                }
                window['data'] = data;
                window.data_table = makeTable(data);
            });
            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportPayment') }}",
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
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js" integrity="sha512-RtZU3AyMVArmHLiW0suEZ9McadTdegwbgtiQl5Qqo9kunkVg1ofwueXD8/8wv3Af8jkME3DDe3yLfR8HSJfT2g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
    <script>
        searchPerson()

        // $('#crud').on('shown.bs.modal', function() {
        //     searchPersonM();
        // });
    </script>
@stop
