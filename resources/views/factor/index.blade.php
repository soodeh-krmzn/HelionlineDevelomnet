@extends('parts.master')

@section('title', __('گزارش فروشگاه'))

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
                                        <option value="game">@lang('بازی')</option>
                                        <option value="factor">@lang('آزاد')</option>
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
                                    <button class="btn btn-success" id="search-factor">@lang('جستجو')</button>
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
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            <i class='bx bx-search me-1'></i> <span class="d-none d-lg-inline-block">@lang('جستجو')</span>
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
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mx-1">
                    <div id="result" class="col">

                    </div>
                </div>
                <div class="row mx-1">
                    <div class="col">
                        <div id="sum-container" class="alert alert-warning my-3 text-center">@lang('مجموع کل'): <span
                                id="sum"></span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="bodies-modal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content p-3 p-md-5">
                    <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body">
                        <h3 class="text-center">@lang('جزئیات کد') <span id="factor-id"></span></h3>
                        <div id="bodies-result"></div>
                    </div>
                </div>
            </div>
        </div>

    @stop

    @section('footer-scripts')
        <script type="text/javascript">
            searchPerson();
            let table = `<div class="table-responsive">
        <table id="factor-table" class="table table-hover border-top">
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
        </table></div>`;

            function sumFactor(fields) {
                $.ajax({
                    url: "{{ route('sumFactor') }}",
                    type: "GET",
                    data: fields ?? {},
                    success: function(data) {
                        $("#sum").html(addCommas(data));
                    }
                });
            }

            function makeTable(fields = null) {
                $("#result").html(table);
                $data_table = $("#factor-table").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        "url": "{{ route('tableFactor') }}",
                        "type": "GET",
                        "data": fields ?? {}
                    },
                    order: [5, 'desc'],
                    columns: [{
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
                            data: 'game',
                            searchable: false
                        },
                        {
                            data: 'created_at'
                        },
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
                sumFactor(fields);
                return $data_table;
            }



            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                let loading = `
            <div class="col text-center">
                <span class="spinner-grow" role="status" aria-hidden="true"></span>
            </div>`;

                window.data_table = makeTable();

                let data;

                $(document.body).on("click", "#search-factor", function() {
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
                        'person_id': person_id,
                        'type': type,
                        'from_price': from_price,
                        'to_price': to_price,
                        'from_date': from_date,
                        'to_date': to_date,
                        'filter_search': true
                    }
                    window['data'] = data;
                    window.data_table = makeTable(data);
                    $("#loading").fadeOut();
                });

                $(document.body).on("click", ".crud-bodies", function() {
                    $("#loading").fadeIn();
                    var f_id = $(this).data("f_id");
                    $("#factor-id").html(f_id);
                    $.ajax({
                        type: "POST",
                        url: "{{ route('crudFactorBodies') }}",
                        data: {
                            f_id: f_id,
                        },
                        success: function(data) {
                            $("#loading").fadeOut();
                            $("#bodies-result").html(data);
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
                window.confirmTitle = "{{ __('اطمینان دارید؟') }}";
                window.confirmButtonText = "{{ __('بله، مطمئنم.') }}";
                window.cancelButtonText = "{{ __('نه، پشیمون شدم.') }}";
                $(document.body).on("click", ".delete-factor", function() {
                    let id = $(this).data("id");
                    confirmAction("@lang('درحال حذف صورتحساب فروشگاه.')", function() {

                        actionAjax("{{ route('deleteWholeFactor') }}", {
                            id: id
                        }, "@lang('موفق')", "@lang('صورتحساب مورد نظر حذف شد')", function() {
                            window.data_table.ajax.reload(function() {
                                sumFactor(window['data']);
                            });
                        });
                    });

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
                        url: "{{ route('exportFactor') }}",
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
