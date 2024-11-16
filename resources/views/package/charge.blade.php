@extends('parts.master')

@section('title', __('مدیریت شارژ'))

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
                                    <label class="form-label">{{ __('نام مشتری') }}</label>
                                    <select name="s_person" id="s-person" class="form-select searchPerson">

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('نام بسته') }}</label>
                                    <select name="s_package" id="s-package" class="form-select select2">
                                        <option value="">{{ __('انتخاب') }}</option>
                                        @foreach ($packages as $package)
                                            <option value="{{ $package->id }}">{{ $package->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('شارژ') }}</label>
                                    <select name="s_charge" id="s-charge" class="form-select">
                                        <option value="">{{ __('همه') }}</option>
                                        <option value="yes">{{ __('دارد') }}</option>
                                        <option value="no">{{ __('ندارد') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end justify-content-start">
                                <div class="form-group">
                                    <button class="btn btn-success" id="search-charge">{{ __('جستجو') }}</button>
                                    <a href="{{ route('chargePackage') }}" class="btn btn-info"
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
                                            <span class="d-none d-lg-inline-block">{{ __('خروجی اکسل') }}</span>
                                        </span>
                                    </button>
                                    <a href="/charge-report" class="btn btn-info ms-2 rounded">
                                        <span>
                                            <i class="bx bx-history"></i>
                                            <span class="d-none d-lg-inline-block">{{ __('اصلاحات') }}</span>
                                        </span>
                                    </a>
                                    @can('create')
                                        <button type="button" class="btn add-new btn-primary mb-sm-0 ms-2 crud"
                                            data-action="create" data-id="0" data-bs-toggle="modal"
                                            data-bs-target="#crud-modal">
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
    <div class="modal fade" id="crud-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
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
        <table id="charge-table" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>{{ __('ردیف') }}</th>
                    <th>{{ __('نام مشتری') }}</th>
                    <th>{{ __('نام بسته') }}</th>
                    <th>{{ __('نوع بسته') }}</th>
                    <th>{{ __('شارژ باقیمانده (دقیقه/مرتبه)') }}</th>
                    <th>{{ __('شارژ باقیمانده (ساعت/مرتبه)') }}</th>
                    <th>{{ __('وضعیت') }}</th>
                    <th>{{ __('تاریخ انقضاء') }}</th>
                    <th>{{ __('مدیریت') }}</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#charge-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableCharge') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                order:[7,'desc'],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false, searchable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'pack'
                    },
                    {
                        data: 'sharj_type',

                    },
                    {
                        data: 'sharj',
                        orderable: false, searchable: false
                    },
                    {
                        data: 'sharj-hour',
                        orderable: false, searchable: false
                    },
                    {
                        data: 'status',
                        orderable: false, searchable: false
                    },
                    {
                        data: 'expire'
                    },
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
                    url: "{{ route('crudCharge') }}",
                    data: {
                        id: id,
                        action: action
                    },
                    success: function(data) {
                        $("#crud-result").html(data);
                        $(".select2-c").select2({
                            dropdownParent: $('#crud-modal .modal-content')
                        });
                        searchPersonL();
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
            function searchPersonL() {
                $(".searchPersonL").select2({
                    minimumInputLength: 3, // Minimum characters to start searching
                    placeholder: "@lang('انتخاب کنید')",
                    dropdownParent: $('#crud-modal .modal-content'),
                    @if (app()->getLocale()=='fa')
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
            $(document.body).on("change", ".charge-field", function() {
                $("#loading").fadeIn();
                var action = "create";
                var id = $("#person").val();
                var text = $("#person option:selected").text();
                var p_id = $("#package").val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudCharge') }}",
                    data: {
                        id: id,
                        p_id: p_id,
                        action: action
                    },
                    success: function(data) {
                        $("#crud-result").html(data);
                        $(".select2-c").select2({
                            dropdownParent: $('#crud-modal .modal-content')
                        });
                        if (id) {
                            $('.searchPersonL').append(`<option selected value=${id}> ${text} </option>`)
                        }
                        searchPersonL();
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

            function store(charge) {


                var id = $(charge).data("id");
                var action = $(charge).data("action");
                var person = $("#person").val();
                var package = $("#package").val();
                var sharj = $("#sharj").val();
                var expire = $("#expire").val();
                var prices = $(".payment-price").serialize();
                var details = $(".payment-details").serialize();
                if (action == 'create') {
                    actionAjax("{{ route('storeCharge') }}", {
                        action: action,
                        id: id,
                        check: true,
                        person: person,
                        package: package,
                        sharj: sharj,
                        expire: expire,
                        prices: prices,
                        details: details
                    }, null, null, function(response) {
                        if (response.diffPack) {
                            confirmAction(response.message, function() {
                                $.ajax({
                                    type: "POST",
                                    url: "{{ route('storeCharge') }}",
                                    data: {
                                        action: action,
                                        id: id,
                                        person: person,
                                        package: package,
                                        sharj: sharj,
                                        expire: expire,
                                        prices: prices,
                                        details: details
                                    },
                                    success: function(data) {
                                        makeTable();
                                        $("#person").val("");
                                        $("#package").val("");
                                        $(".payment-price").val("");
                                        $(".payment-details").val("");
                                        // $("#loading").fadeOut();
                                        Swal.fire({
                                            title: "{{ __('موفق') }}",
                                            text: "{{ __('اطلاعات با موفقیت ثبت شد.') }}",
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
                            })
                        } else {
                            makeTable();
                            $("#person").val("");
                            $("#package").val("");
                            $(".payment-price").val("");
                            $(".payment-details").val("");
                            // $("#loading").fadeOut();
                            Swal.fire({
                                title: "{{ __('موفق') }}",
                                text: "{{ __('اطلاعات با موفقیت ثبت شد.') }}",
                                icon: "success"
                            });
                        }
                    })
                } else {

                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeCharge') }}",
                        data: {
                            action: action,
                            id: id,
                            person: person,
                            package: package,
                            sharj: sharj,
                            expire: expire,
                            prices: prices,
                            details: details
                        },
                        success: function(data) {
                            makeTable();
                            $("#person").val("");
                            $("#package").val("");
                            $(".payment-price").val("");
                            $(".payment-details").val("");
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "{{ __('موفق') }}",
                                text: "{{ __('اطلاعات با موفقیت ثبت شد.') }}",
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

            }
            window.confirmTitle = "{{ __('اطمینان دارید؟') }}";
            window.confirmButtonText = "{{ __('بله، مطمئنم.') }}";
            window.cancelButtonText = "{{ __('نه، پشیمون شدم.') }}";

            $(document.body).on("click", ".store-charge", function() {
                var sharj = $("#sharj").val();
                var expire = $("#expire").val();
                if (sharj != "" && expire != "") {
                    $("#loading").fadeIn();
                    var action = $(this).data("action");
                    var e = checkEmpty();
                    if (e && action == "create") {
                        $("#loading").fadeOut();
                        return;
                    } else {
                        if ($("#total-pay").html() == 0) {
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "{{ __('اطمینان دارید؟') }}",
                                text: "{{ __('آیا از ثبت بدون پرداخت اطمینان دارید؟') }}",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonText: "{{ __('بله، مطمئنم.') }}",
                                cancelButtonText: "{{ __('نه، پشیمون شدم.') }}"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    store($(this));
                                }
                            });
                        } else {
                            $("#loading").fadeOut();
                            store($(this));
                        }
                    }
                } else {
                    Swal.fire({
                        title: "{{ __('اخطار') }}",
                        text: "{{ __('لطفا موارد ستاره دار را تکمیل نمایید.') }}",
                        icon: "error"
                    });
                }
            });

            $(document.body).on("click", ".delete-charge", function() {
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
                            url: "{{ route('deleteCharge') }}",
                            data: {
                                id: id
                            },
                            success: function(data) {
                                makeTable();
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "{{ __('حذف شد') }}",
                                    text: "{{ __('با موفقیت حذف شد.') }}",
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

            $(document.body).on("click", "#toggle-search", function() {
                $(".select2").select2();
            });

            $(document.body).on("click", "#search-charge", function() {
                $("#loading").fadeIn();
                var person_id = $("#s-person").val();
                var package_id = $("#s-package").val();
                var charge = $("#s-charge").find("option:selected").val();
                var data = {
                    person_id: person_id,
                    package_id: package_id,
                    charge: charge,
                    filter_search: true
                }
                window['data'] = data;
                makeTable(data);
                $("#loading").fadeOut();
            });

            $(document.body).on("change", ".payment-price", function() {
                totalPayment();
            });

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportCharge') }}",
                    type: "POST",
                    data: window['data'],
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
                        console.log(data);
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
