@extends('parts.master')

@section('title', 'اشخاص')

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
                                    <label class="form-label">{{ __('نام') }}</label>
                                    <input type="text" id="s_name" class="form-control"
                                        placeholder="{{ __('نام') }}...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('نام خانوادگی') }}</label>
                                    <input type="text" id="s_family" class="form-control"
                                        placeholder="{{ __('نام خانوادگی') }}...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('موبایل') }}</label>
                                    <input type="text" id="s_mobile" class="form-control just-numbers"
                                        placeholder="{{ __('موبایل') }}...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('کد ملی') }}</label>
                                    <input type="text" id="s_national_code" class="form-control just-numbers"
                                        placeholder="{{ __('کد ملی') }}...">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('جنسیت') }}</label>
                                    <select id="s_gender" class="form-select">
                                        <option value="">{{ __('همه') }}</option>
                                        <option value="0">{{ __('دختر') }}</option>
                                        <option value="1">{{ __('پسر') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('کد اشتراک') }}</label>
                                    <input type="text" id="s_reg_code" class="form-control"
                                        placeholder="{{ __('کد اشتراک') }}...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('از تاریخ تولد') }}</label>
                                    <input type="text" id="s_from_birth" class="form-control date-mask"
                                        placeholder="1400/01/01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('تا تاریخ تولد') }}</label>
                                    <input type="text" id="s_to_birth" class="form-control date-mask"
                                        placeholder="1400/01/01">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-end">
                            <div class="form-group">
                                <button class="btn btn-success" id="search-person">{{ __('جستجو') }}</button>
                                <a href="{{ route('person') }}" class="btn btn-info"
                                    id="show-all">{{ __('نمایش همه') }}</a>
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
                            <i class='bx bx-search me-1'></i>
                            <span class="d-none d-lg-inline-block">{{ __('جستجو') }}</span>
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
                                            <span class="d-none d-lg-inline-block">{{ __('خروجی اکسل') }}</span>
                                        </span>
                                    </button>
                                </div>
                                @can('create')
                                    <button class="btn btn-secondary add-new btn-primary crud ms-2" data-action="create"
                                        data-id="0" tabindex="0" aria-controls="DataTables_Table_0" type="button"
                                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser">
                                        <span>
                                            <i class="bx bx-plus"></i>
                                            <span class="d-none d-lg-inline-block">{{ __('مشتری جدید') }}</span>
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

            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser">
                <div class="offcanvas-header border-bottom">
                    <h6 class="offcanvas-title" id="offcanvas-title"></h6>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0">
                    <div id="crud-result"></div>
                </div>
            </div>

            <div class="modal fade" id="meta" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content p-3 p-md-5">
                        <div id="meta-result"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
@section('footer-scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let table = `<div class="table-responsive">
            <table id="testTable" class="table table-hover border-top">
                <thead>
                    <tr>
                        <th>{{ __('ردیف') }}</th>
                        <th>{{ __('شماره عضویت') }}</th>
                        <th>{{ __('ثبت نام') }}</th>
                        <th>{{ __('نام') }}</th>
                        <th>{{ __('نام خانوادگی') }}</th>
                        <th>{{ __('تاریخ تولد') }}</th>
                        <th>{{ __('کد اشتراک') }}</th>
                        <th>{{ __('جنسیت') }}</th>
                        <th>{{ __('موبایل') }}</th>
                        <th>{{ __('عملیات') }}</th>
                    </tr>
                </thead>
            </table></div>`;

            function makeTable(fields = null) {
                $("#result").html(table);
                $("#testTable").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        "url": "{{ route('tablePerson') }}",
                        "type": "GET",
                        "data": fields ?? {}
                    },
                    order: [1, 'desc'],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'id'
                        },
                        {
                            data: 'created_at'
                        },
                        {
                            data: 'name'
                        },
                        {
                            data: 'family'
                        },
                        {
                            data: 'birth'
                        },
                        {
                            data: 'reg_code'
                        },
                        {
                            data: 'gender'
                        },
                        {
                            data: 'mobile'
                        },
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            }

            makeTable();

            $(document.body).on("click", ".crud", function() {
                $("#loading").fadeIn();
                var action = $(this).data("action");
                var id = $(this).data("id");
                if (action == "create") {
                    var title = "{{ __('مشتری جدید') }}";
                } else {
                    var title = "{{ __('ویرایش مشتری') }}";
                }
                $("#offcanvas-title").html(title);
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudPerson') }}",
                    data: {
                        id: id,
                        action: action
                    },
                    success: function(data) {
                        $("#crud-result").html(data);
                        dateMask();
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

            $(document.body).on("click", "#store-person", function() {
                var e = checkEmpty();
                var m = checkMobile();
                var n = checkNationalCode();
                var d = checkDate();
                if (!e && !m && !n && !d) {
                    $("#loading").fadeIn();
                    var action = $(this).data("action");
                    var id = $("#id").val();
                    var name = $("#name").val();
                    var family = $("#family").val();
                    var mobile = $("#mobile").val();
                    var birth = $("#birth").val();
                    var gender = $("#gender").find("option:selected").val();
                    var address = $("#address").val();
                    var national_code = $("#national_code").val();
                    var reg_code = $("#reg_code").val();
                    var club = $("#club").find("option:selected").val();

                    $.ajax({
                        type: "POST",
                        url: "{{ route('storePerson') }}",
                        data: {
                            action: action,
                            id: id,
                            name: name,
                            family: family,
                            mobile: mobile,
                            birth: birth,
                            gender: gender,
                            address: address,
                            national_code: national_code,
                            reg_code: reg_code,
                            club: club
                        },
                        success: function(data) {
                            makeTable();
                            $("#loading").fadeOut();
                            if (action == "create") {
                                var text = "@lang('اطلاعات شما با موفقیت ثبت شد')";
                            } else {
                                var text = "@lang('اطلاعات شما با موفقیت ویرایش شد')";
                            }
                            Swal.fire({
                                title: "@lang('موفق')",
                                text: text,
                                icon: "success"
                            });
                            if (action == "create") {
                                $("#id").val(0);
                                $("#name").val('');
                                $("#family").val('');
                                $("#mobile").val('');
                                $("#birth").val('');
                                $("#gender").val('');
                                $("#address").val('');
                                $("#national_code").val('');
                                $("#reg_code").val('');
                            }
                        },
                        error: function(data) {
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: '@lang('خطا')',
                                text: data.responseJSON.message,
                                icon: "error"
                            });
                        }
                    });
                }
            });

            $(document.body).on("click", "#delete-person", function() {
                Swal.fire({
                    title: '@lang('اطمینان دارید؟')',
                    text: '@lang('آیا از حذف این مورد اطمینان دارید؟')',
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "@lang('بله، مطمئنم.')",
                    cancelButtonText: '@lang('نه، پشیمون شدم.')'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading").fadeIn();
                        var id = $(this).data("id");
                        $.ajax({
                            type: "POST",
                            url: "{{ route('deletePerson') }}",
                            data: {
                                id: id
                            },
                            success: function(data) {
                                makeTable();
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: '@lang('حذف شد')' + "!",
                                    text: '@lang('مشتری با موفقیت حذف شد').',
                                    icon: "success"
                                });
                            },
                            error: function(data) {
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: '@lang('خطا')',
                                    text: data.responseJSON.message,
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });

            let data;

            $(document.body).on("click", "#search-person", function() {
                var d = checkDate();
                if (d) {
                    Swal.fire({
                        title: '@lang('خطا')',
                        icon: 'error',
                        text: 'تاریخ وارد شده معتبر نمیباشد. (نمونه معتبر 1401/01/01)'
                    });
                    return;
                }
                $("#loading").fadeIn();
                var name = $("#s_name").val();
                var family = $("#s_family").val();
                var mobile = $("#s_mobile").val();
                var national_code = $("#s_national_code").val();
                var reg_code = $("#s_reg_code").val();
                var gender = $("#s_gender").val();
                var from_birthday = $("#s_from_birth").val();
                var to_birthday = $("#s_to_birth").val();
                var data = {
                    "name": name,
                    "family": family,
                    "mobile": mobile,
                    "national_code": national_code,
                    "reg_code": reg_code,
                    "gender": gender,
                    "from_birthday": from_birthday,
                    "to_birthday": to_birthday,
                    "filter_search": true
                }
                makeTable(data);
                window['data'] = data;
                $("#loading").fadeOut();
            });

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportPerson') }}",
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
                        if (data.responseJSON.message.indexOf('llowed memory size')) {
                            Swal.fire({
                                title: '@lang('خطا')',
                                text: "@lang('گزارش خواسته شده بیش از اندازه بزرگ است لطفا از فیلتر استفاده کنید')",
                                icon: "error"
                            });
                        } else {
                            Swal.fire({
                                title: '@lang('خطا')',
                                text: data.responseJSON.message,
                                icon: "error"
                            });
                        }
                    }
                });
            })

            $(document.body).on("click", ".meta", function() {
                $("#loading").fadeIn();
                var id = $(this).data("id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudPersonMeta') }}",
                    data: {
                        id: id,
                    },
                    success: function(data) {
                        $("#meta-result").html(data);
                        dateMask();
                        $("#loading").fadeOut();
                    },
                    error: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: '@lang('خطا')',
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", "#store-meta", function() {
                $("#loading").fadeIn();
                var d = checkDate();
                var m = checkMobile();
                if (!m && !d) {
                    var id = $(this).data("id");
                    var data = $("#meta-form").serialize();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storePersonMeta') }}",
                        data: {
                            id: id,
                            data: data,
                        },
                        success: function() {
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "@lang('موفق')",
                                text: "@lang('اطلاعات با موفقیت ثبت شد').",
                                icon: "success"
                            });
                        },
                        error: function(data) {
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: '@lang('خطا')',
                                text: data.responseJSON.message,
                                icon: "error"
                            });
                        }
                    });
                }
            })
        });
    </script>
@stop
