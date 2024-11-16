@extends('parts.master')

@section('title', __('هزینه ها'))

@section('head-styles')

@stop

@section('content')
    <!-- Content -->
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
                                    <input type="text" data-id="s-from-price" class="form-control money-filter just-numbers" placeholder="{{ __('از مبلغ') }}...">
                                    <input type="hidden" id="s-from-price" class="form-control just-numbers">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('تا مبلغ') }}</label>
                                    <input type="text" data-id="s-to-price" class="form-control money-filter just-numbers" placeholder="{{ __('تا مبلغ') }}...">
                                    <input type="hidden" id="s-to-price" class="form-control just-numbers">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('از تاریخ') }}</label>
                                    <input type="text" id="s-from-date" class="form-control date-mask " placeholder="1400/01/01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('تا تاریخ') }}</label>
                                    <input type="text" id="s-to-date" class="form-control date-mask " placeholder="1400/01/01">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('دسته') }}</label>
                                    <select id="s-category-id" class="form-select select2">
                                        <option value="">{{ __('همه') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('کاربر') }}</label>
                                    <select id="s-user-id" class="form-select select2">
                                        <option value="">{{ __('همه') }}</option>
                                        @foreach (\App\Models\User::getUsers() as $user)
                                            <option value="{{ $user->id }}">{{ $user->getFullName() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end justify-content-start">
                                <div class="form-group">
                                    <button class="btn btn-success" id="search-cost">{{ __('جستجو') }}</button>
                                    <a href="{{ route("cost") }}" class="btn btn-info" id="show-all">{{ __('نمایش همه') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <div class="row mx-1 my-3">
                    <div class="col">
                        <button id="toggle-search" class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            <i class='bx bx-search me-1'></i> {{ __('جستجو') }}
                        </button>
                    </div>
                    <div class="col">
                        <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                            <div class="dt-buttons btn-group flex-wrap">
                                <div class="btn-group">
                                    <button id="export" class="btn btn-success">
                                        <span>
                                            <i class="bx bxs-file-export"></i>
                                            <span class="d-none d-lg-inline-block">{{ __('خروجی اکسل') }}</span>
                                        </span>
                                    </button>
                                    @can('create')
                                        <button class="btn add-new btn-primary crud ms-2" data-action="create" data-id="0" tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser">
                                            <span>
                                                <i class="bx bx-plus"></i>
                                                <span class="d-none d-lg-inline-block">{{ __('هزینه جدید') }}</span>
                                            </span>
                                        </button>
                                        <button class="btn btn-info ms-2" id="crud-cost-category" tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal" data-bs-target="#category-modal">
                                            <span>
                                                <i class="bx bx-category"></i>
                                                <span class="d-none d-lg-inline-block">{{ __('دسته بندی') }}</span>
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
                        <div id="sum-container" class="alert alert-warning my-3 text-center">
                            {{ __('مجموع') }}: <span id="sum"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Offcanvas to add new user -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser">
                <div class="offcanvas-header border-bottom">
                    <h6 class="offcanvas-title" id="offcanvas-title"></h6>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0">
                    <div id="crud-result"></div>
                </div>
            </div>

            <div class="modal fade" id="category-modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content p-3 p-md-5">
                        <div id="crud-cost-category-result"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--/ Content -->
@stop

@section('footer-scripts')
    <script type="text/javascript">

        let table = `<div class="table-responsive">
        <table id="cost-table" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>@lang('ردیف')</th>
                    <th>@lang('دسته')</th>
                    <th>@lang('مبلغ')</th>
                    <th>@lang('توضیحات')</th>
                    <th>@lang('تاریخ')</th>
                    <th>@lang('کاربر')</th>
                    <th>@lang('مدیریت')</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#cost-table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableCost') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                order:[4,'desc'],
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'categories'},
                    {data: 'price'},
                    {data: 'details'},
                    {data: 'date'},
                    {data: 'created_by'},
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            $.ajax({
                url: "{{ route('sumCost') }}",
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

            $(document.body).on("click", "#toggle-search", function() {
                $(".select2").select2();
            });

            let loading = `
            <div class="col text-center">
                <span class="spinner-grow" role="status" aria-hidden="true"></span>
            </div>`;

            makeTable();

            //Start Cost
            $(document.body).on("click", ".crud", function () {
                $("#loading").fadeIn();
                var action = $(this).data("action");
                var id = $(this).data("id");
                if (action == "create") {
                    var title = "@lang('هزینه جدید')";
                } else {
                    var title = "@lang('ویرایش هزینه')";
                }
                $("#offcanvas-title").html(title);
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudCost') }}",
                    data: {
                        id: id,
                        action: action
                    },
                    success: function (data) {
                        $("#crud-result").html(data);
                        $("#categories").select2({
                            placeholder: "@lang('جهت انتخاب دسته ها کلیک کنید')",
                            dropdownParent: $('#offcanvasAddUser .offcanvas-body')
                        });
                        $("#loading").fadeOut();
                    },
                    error: function (data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('خطا')",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", "#store-cost", function() {
                var e = checkEmpty('cost-form');
                if (!e) {
                    $("#loading").fadeIn();
                    var action = $(this).data("action");
                    var id = $("#id").val();
                    var price = $("#price").val();
                    var details = $("#details").val();
                    var date = $("#date").val();
                    var categories = $("#categories").val();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeCost') }}",
                        data: {
                            action: action,
                            id: id,
                            price: price,
                            details: details,
                            categories: categories,
                            date:date
                        },
                        success: function(data) {
                            makeTable();
                            $("#loading").fadeOut();
                            if (action == "create") {
                                var text= "@lang('اطلاعات با موفقیت ثبت شد.')";
                            }else{
                                var text= "@lang('اطلاعات با موفقیت ویرایش شد').";
                            }
                            Swal.fire({
                                title: "@lang('موفق')",
                                text:text,
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
            });

            $(document.body).on("click", ".delete-cost", function() {
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
                            url: "{{ route('deleteCost') }}",
                            data:{
                                id: id
                            },
                            success:function(data) {
                                makeTable();
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title:"@lang('حذف شد')!",
                                    text: "@lang('هزینه با موفقیت حذف شد.')",
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
                });
            });

            $(document.body).on("click", "#search-cost", function() {
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
                var category_id = $("#s-category-id").val();
                var user_id = $("#s-user-id").val();
                var from_date = $("#s-from-date").val();
                var to_date = $("#s-to-date").val();
                var from_price = $("#s-from-price").val();
                var to_price = $("#s-to-price").val();
                var data = {
                    category_id: category_id,
                    user_id: user_id,
                    from_date: from_date,
                    to_date: to_date,
                    from_price: from_price,
                    to_price: to_price,
                    filter_search: true
                }
                window["data"] = data;
                makeTable(data);
                $("#loading").fadeOut();
            });

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportCost') }}",
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

            //End Cost

            //Start Cost Category
            $(document.body).on("click", "#crud-cost-category", function() {
                $("#loading").fadeIn();
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudCostCategory') }}",
                    success: function(data) {
                        $(".cc-select2").select2({
                            dropdownParent: $('#category-modal .modal-content')
                        });
                        $("#crud-cost-category-result").html(data);
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

            $(document.body).on("click", "#store-cost-category", function() {
                var e = checkEmpty('category-form');
                if (!e) {
                    $("#loading").fadeIn();
                    var id = $("#c-id").val();
                    var action = $("#c-action").val();
                    var name = $("#c-name").val();
                    var parent_id = $("#c-parent-id").val();
                    var details = $("#c-details").val();
                    var code = $("#c-code").val();
                    var display_order = $("#c-order").val();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeCostCategory') }}",
                        data: {
                            id: id,
                            action: action,
                            name: name,
                            parent_id: parent_id,
                            details: details,
                            code: code,
                            display_order: display_order
                        },
                        success: function(data) {
                            $("#cost-category-result").html(data);
                            $("#c-id").val(0);
                            $("#c-action").val("create");
                            $("#c-name").val('');
                            $("#c-parent-id").val('');
                            $("#c-details").val('');
                            $("#c-code").val('');
                            $("#c-order").val('');
                            $("#loading").fadeOut();
                            if (action == "create") {
                                var text= "@lang('اطلاعات با موفقیت ثبت شد.')";
                            }else{
                                var text= "@lang('اطلاعات با موفقیت ویرایش شد').";
                            }
                            Swal.fire({
                                title: "@lang('موفق')",
                                text: text,
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
            });

            $(document.body).on("click", ".update-cost-category", function() {
                var id = $(this).data("id");
                var action = $(this).data("action");
                var name = $(this).data("name");
                var parent_id = $(this).data("parent_id");
                var details = $(this).data("details");
                var code = $(this).data("code");

                $("#store-cost-category").html("ویرایش اطلاعات");
                $("#store-cost-category").addClass("btn-warning");

                $("#c-id").val(id);
                $("#c-action").val(action);
                $("#c-name").val(name);
                $("#c-parent-id").val(parent_id);
                $("#c-details").val(details);
                $("#c-code").val(code);
            });

            $(document.body).on("click", ".delete-cost-category", function() {
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
                            url: "{{ route('deleteCostCategory') }}",
                            data:{
                                id: id
                            },
                            success:function(data) {
                                $("#cost-category-result").html(data);
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title:"@lang('حذف شد')!",
                                    text: "@lang('دسته با موفقیت حذف شد.')",
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
                });
            });

            //End Cost Category

        });
    </script>
@stop
