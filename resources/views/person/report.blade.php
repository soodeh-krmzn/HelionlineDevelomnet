@extends('parts.master')

@section('title', __('گزارش حساب اشخاص'))

@section('head-styles')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/page-faq.css') }}">
@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="card">
            <div class="py-3 primary-font">
                <div class="row mb-3 mx-1">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">@lang('شخص')</label>
                            <select id="person-id" class="form-select searchPerson-c">

                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">@lang('از تاریخ')</label>
                            <input type="text" id="from-date" class="form-control date-mask " placeholder="1400/01/01">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">@lang('تا تاریخ')</label>
                            <input type="text" id="to-date" class="form-control date-mask " placeholder="1400/01/01">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end justify-content-start">
                        <div class="form-group">
                            <button class="btn btn-success" id="search-reports">@lang('جستجو')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-3 col-md-4 col-12 mb-md-0 mb-3">
                <div class="d-flex justify-content-between flex-column mb-2 mb-md-0">
                    <ul class="nav nav-align-left nav-pills flex-column lh-1-85">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#payment">
                                <i class="bx bx-credit-card faq-nav-icon me-2"></i>
                                <span class="align-middle">@lang('پرداخت')</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#game">
                                <i class="bx bx-shopping-bag faq-nav-icon me-2"></i>
                                <span class="align-middle">@lang('بخش ها')</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#factor">
                                <i class="bx bx-rotate-left faq-nav-icon me-2"></i>
                                <span class="align-middle">@lang('فروشگاه')</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#wallet">
                                <i class="bx bx-cube faq-nav-icon me-2"></i>
                                <span class="align-middle">@lang('کیف پول')</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#club">
                                <i class="bx bx-cog faq-nav-icon me-2"></i>
                                <span class="align-middle">@lang('باشگاه')</span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-9 col-md-8 col-12">
                <div class="card">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="payment" data-route="{{ route('tablePayment') }}"
                            data-table="payment_table" data-columns="payment_columns"
                            data-sum-route="{{ route('sumPayment') }}" role="tabpanel">
                            <div id="payment-result"></div>
                            <div id="payment-sum-container" class="alert alert-warning my-3 text-center">@lang('تراز'): <span
                                    id="payment-sum"></span></div>
                        </div>
                        <div class="tab-pane fade" id="game" data-route="{{ route('tableGame') }}"
                            data-table="game_table" data-columns="game_columns" data-sum-route="{{ route('sumGame') }}"
                            role="tabpanel">
                            <div id="game-result"></div>
                            <div id="game-sum-container" class="alert alert-warning my-3 text-center">@lang('مجموع کل'): <span
                                    id="game-sum"></span></div>
                        </div>
                        <div class="tab-pane fade" id="factor" data-route="{{ route('tableFactor') }}"
                            data-table="factor_table" data-columns="factor_columns"
                            data-sum-route="{{ route('sumFactor') }}" role="tabpanel">
                            <div id="factor-result"></div>
                            <div id="factor-sum-container" class="alert alert-warning my-3 text-center">@lang('مجموع کل'): <span
                                    id="factor-sum"></span></div>
                        </div>
                        <div class="tab-pane fade" id="wallet" data-route="{{ route('tableWallet') }}"
                            data-table="wallet_table" data-columns="wallet_columns" role="tabpanel">
                            <div id="wallet-result"></div>
                        </div>
                        <div class="tab-pane fade" id="club" data-route="{{ route('tableClubLog') }}"
                            data-table="club_table" data-columns="club_columns" role="tabpanel">
                            <div id="club-result"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bodies-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-3 p-md-5">
                <div id="bodies-result" class="table-responsive table-responsive"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="meta-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-3 p-md-5">
                <div id="meta-result"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="accompany-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-3 p-md-5">
                <div id="accompany-result"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="crud" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-3 p-md-5">
                <div id="crud-result"></div>
            </div>
        </div>
    </div>
@stop

@section('footer-scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            searchPersonC();

            function searchPersonC() {

                $(".searchPerson-c").select2({
                    minimumInputLength: 3, // Minimum characters to start searching
                    placeholder: "@lang('انتخاب کنید')",
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
                $('.searchPerson-c').parent().find(".select2-container:not(:first)").remove();

            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let no_person = `
            <div class="my-3 text-center alert alert-danger">
                لطفا شخص را انتخاب کنید.
            </div>`;

            var vars = {
                payment_table: `
                <div class="row mb-3">
                    <div class="col text-end">
                        <button id="export" class="btn btn-success" data-route="{{ route('exportPayment') }}">
                            <span>
                                <i class="bx bxs-file-export"></i>
                                <span class="d-none d-lg-inline-block">@lang('خروجی اکسل')</span>
                            </span>
                        </button>
                    </div>
                </div><div class="table-responsive">
                <table id="payment_table" class="table table-hover border-top">
                    <thead>
                        <tr>
                            <td>@lang('ردیف')</td>
                            <td>@lang('نام شخص')</td>
                            <td>@lang('مبلغ')</td>
                            <td>@lang('نوع پرداخت')</td>
                            <td>@lang('تاریخ')</td>
                            <td>@lang('توضیحات')</td>
                        </tr>
                    </thead>
                </table></div>`,

                payment_columns: [{
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
                    }
                ],

                game_table: `
                <div class="row mb-3">
                    <div class="col text-end">
                        <button id="export" class="btn btn-success" data-route="{{ route('exportGame') }}">
                            <span>
                                <i class="bx bxs-file-export"></i>
                                <span class="d-none d-lg-inline-block">@lang('خروجی اکسل')</span>
                            </span>
                        </button>
                    </div>
                </div><div class="table-responsive">
                <table id="game_table" class="table table-hover border-top">
                    <thead>
                        <tr>
                            <th>@lang('ردیف')</th>
                            <th>@lang('شخص')</th>
                            <th>@lang('بخش')</th>
                            <th>@lang('ورود')</th>
                            <th>@lang('خروج')</th>
                            <th>@lang('فروشگاه')</th>
                            <th>@lang('بازی')</th>
                            <th>@lang('تخفیف')</th>
                            <th>@lang('جمع')</th>
                            <th>@lang('مدیریت')</th>
                        </tr>
                    </thead>
                </table></div>`,

                game_columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'person'
                    },
                    {
                        data: 'section_name'
                    },
                    {
                        data: 'in_time'
                    },
                    {
                        data: 'out_time'
                    },
                    {
                        data: 'total_shop'
                    },
                    {
                        data: 'game_price'
                    },
                    {
                        data: 'offer_price'
                    },
                    {
                        data: 'final_price'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],

                factor_table: `
                <div class="row mb-3">
                    <div class="col text-end">
                        <button id="export" class="btn btn-success" data-route="{{ route('exportFactor') }}">
                            <span>
                                <i class="bx bxs-file-export"></i>
                                <span class="d-none d-lg-inline-block">@lang('خروجی اکسل')</span>
                            </span>
                        </button>
                    </div>
                </div><div class="table-responsive">
                <table id="factor_table" class="table table-hover border-top">
                    <thead>
                        <tr>
                            <th>@lang('ردیف')</th>
                            <th>@lang('نام شخص')</th>
                            <th>@lang('تخفیف')</th>
                            <th>@lang('مبلغ بعد از تخفیف')</th>
                            <th>@lang('نوع')</th>
                            <th>@lang('تاریخ')</th>
                            <th>@lang('عملیات')</th>
                        </tr>
                    </thead>
                </table></div>`,

                factor_columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'person_fullname'
                    },
                    {
                        data: 'offer_price'
                    },
                    {
                        data: 'final_price'
                    },
                    {
                        data: 'game'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],

                wallet_table: `<div class="table-responsive">
                <table id="wallet_table" class="table table-hover border-top">
                    <thead>
                        <tr>
                            <th>@lang('ردیف')</th>
                            <th>@lang('نام شخص')</th>
                            <th>@lang('موجودی')</th>
                            <th>@lang('مبلغ')</th>
                            <th>@lang('ضریب هدیه')</th>
                            <th>@lang('مبلغ نهایی')</th>
                            <th>@lang('تاریخ')</th>
                            <th>@lang('توضیحات')</th>
                        </tr>
                    </thead>
                </table></div>`,

                wallet_columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'person'
                    },
                    {
                        data: 'balance'
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
                        data: 'created_at'
                    },
                    {
                        data: 'description'
                    }
                ],

                club_table: `
                <div class="row mb-3 text-end">
                    <div class="col">
                        <button id="export" class="btn btn-success" data-route="{{ route('exportClubLog') }}">
                            <span>
                                <i class="bx bxs-file-export"></i>
                                <span class="d-none d-lg-inline-block">@lang('خروجی اکسل')</span>
                            </span>
                        </button>
                    </div>
                </div><div class="table-responsive">
                <table id="club_table" class="table table-hover border-top">
                    <thead>
                        <tr>
                            <th>@lang('ردیف')</th>
                            <th>@lang('نام')</th>
                            <th>@lang('تاریخ')</th>
                            <th>@lang('مبلغ پرداختی')</th>
                            <th>@lang('امتیاز')</th>
                            <th>@lang('امتیاز کل')</th>
                            <th>@lang('توضیحات')</th>
                        </tr>
                    </thead>
                </table></div>`,

                club_columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'person'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'price'
                    },
                    {
                        data: 'rate'
                    },
                    {
                        data: 'balance_rate'
                    },
                    {
                        data: 'description'
                    }
                ]
            }

            let data;

            function search() {
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
                @if ($id=request('id'))
                var person_id={{$id}};
                @else
                var person_id = $("#person-id").val();
                @endif
                var from_date = $("#from-date").val();
                var to_date = $("#to-date").val();
                var tab = $(".tab-pane.active").attr("id");
                var route = $(".tab-pane.active").data("route");
                var sum_route = $(".tab-pane.active").data("sum-route");
                var t = $(".tab-pane.active").data("table");
                var c = $(".tab-pane.active").data("columns");
                var table = vars[t];
                var columns = vars[c];
                var data = {
                    person_id: person_id,
                    from_date: from_date,
                    to_date: to_date,
                    tab: tab,
                    filter_search: true
                }

                window['data'] = data;

                if (person_id != "") {
                    $(`#${tab}-result`).html(table);
                    $(`#${t}`).DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: route,
                            type: "GET",
                            data: data,
                            cache: false
                        },
                        columns: columns
                    });
                    if (sum_route) {
                        $.ajax({
                            url: sum_route,
                            type: "GET",
                            data: data,
                            success: function(data) {
                                $(`#${tab}-sum-container`).show();
                                $(`#${tab}-sum`).html(addCommas(data));
                            }
                        });
                    }

                } else {
                    $(`#${tab}-sum-container`).hide();
                    $(`#${tab}-result`).html(no_person);
                }
                $("#loading").fadeOut();
            }

            search();

            $(document.body).on("click", "#search-reports", function() {
                search();
            });

            $(document.body).on("click", ".nav-link", function() {
                search();
            });

            $(document.body).on("click", ".crud-bodies", function() {
                $("#loading").fadeIn();
                var f_id = $(this).data("f_id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudFactorBodies') }}",
                    data: {
                        f_id: f_id,
                    },
                    success: function(data) {
                        $("#bodies-result").html(data);
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

            $(document.body).on("click", ".crud", function() {
                $("#loading").fadeIn();
                var id = $(this).data("id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('editGame') }}",
                    data: {
                        id: id,
                    },
                    success: function(data) {
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

            $(document.body).on("click", "#update-game", function() {
                var e = checkEmpty();
                if (!e) {
                    $("#loading").fadeIn();
                    var id = $(this).data("id");
                    var i = $("#in").val();
                    var out = $("#out").val();
                    var offer_price = $("#offer-price").val();
                    var game_price = $("#game-price").val();
                    var factor_price = $("#factor-price").val();
                    var section_id = $("#section-id").val();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('updateGame') }}",
                        data: {
                            id: id,
                            in: i,
                            out: out,
                            offer_price: offer_price,
                            game_price: game_price,
                            factor_price: factor_price,
                            section_id: section_id
                        },
                        success: function(data) {
                            search();
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "@lang('موفق')",
                                text: "@lang('با موفقیت ثبت شد.')",
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
                            search();
                        }
                    });
                }
            });

            $(document.body).on("click", ".delete-game", function() {
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
                            url: "{{ route('deleteGame') }}",
                            data: {
                                id: id
                            },
                            success: function(data) {
                                search();
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title:"@lang('حذف شد')!",
                                   text: "@lang('مورد با موفقیت حذف شد.')",
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
                                search();
                            }
                        });
                    }
                });
            });

            $(document.body).on("click", ".crud-meta", function() {
                $("#loading").fadeIn();
                var g_id = $(this).data("g_id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudGameMeta') }}",
                    data: {
                        g_id: g_id,
                    },
                    success: function(data) {
                        $("#meta-result").html(data);
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

            $(document.body).on("click", ".accompany-modal", function() {
                $("#loading").fadeIn();
                var g_id = $(this).data("g_id");

                $.ajax({
                    type: "POST",
                    url: "{{ route('accompanyGame') }}",
                    data: {
                        g_id: g_id
                    },
                    success: function(data) {
                        $('#accompany-result').html(data);
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

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                var route = $(this).data("route");
                $.ajax({
                    url: route,
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
