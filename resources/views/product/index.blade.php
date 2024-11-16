@extends('parts.master')

@section('title', 'محصولات')

@section('head-styles')
    <link rel="stylesheet" href="{{ asset('assets/css/my-style.css') }}">
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
                                    <input type="text" id="s_name" class="form-control" placeholder="{{ __('نام') }}...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('موجودی') }}</label>
                                    <select id="s_stock" class="form-select">
                                        <option value="">{{ __('همه') }}</option>
                                        <option value="1">{{ __('موجود') }}</option>
                                        <option value="0">{{ __('ناموجود') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('از قیمت خرید') }}</label>
                                    <input type="text" id="s_from_buy" class="form-control just-numbers" placeholder="{{ __('از قیمت خرید') }}...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('تا قیمت خرید') }}</label>
                                    <input type="text" id="s_to_buy" class="form-control just-numbers" placeholder="{{ __('تا قیمت خرید') }}...">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('از قیمت فروش') }}</label>
                                    <input type="text" id="s_from_sale" class="form-control just-numbers" placeholder="{{ __('از قیمت فروش') }}...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('تا قیمت فروش') }}</label>
                                    <input type="text" id="s_to_sale" class="form-control just-numbers" placeholder="{{ __('تا قیمت فروش') }}...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('دسته') }}</label>
                                    <select name="s_category" id="s_category" class="form-select select2">
                                        <option value="">{{ __('همه') }}</option>
                                        @foreach (\App\Models\Category::all() as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end justify-content-start">
                                <div class="form-group">
                                    <button class="btn btn-success" id="search-product">{{ __('جستجو') }}</button>
                                    <a href="{{ route('product') }}" class="btn btn-info" id="show-all">{{ __('نمایش همه') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <div class="row mx-1 my-3">
                    <div class="col">
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            <i class='bx bx-search me-1'></i> <span class="d-none d-lg-inline-block">{{ __('جستجو') }}</span>
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
                                    <a href="/product-report" class="btn btn-info ms-2 rounded">
                                        <span>
                                            <i class="bx bx-history"></i>
                                            <span class="d-none d-lg-inline-block">{{ __('اصلاحات') }}</span>
                                        </span>
                                    </a>
                                    @can('create')
                                        <button class="btn add-new btn-primary crud ms-2" data-action="create" data-id="0" tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser">
                                            <span>
                                                <i class="bx bx-plus"></i>
                                                <span class="d-none d-lg-inline-block">{{ __('محصول جدید') }}</span>
                                            </span>
                                        </button>
                                    @endcan
                                    <button class="btn btn-info ms-2" id="crud-category" tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal" data-bs-target="#category-modal">
                                        <span>
                                            <i class="bx bx-category"></i>
                                            <span class="d-none d-lg-inline-block">{{ __('دسته بندی') }}</span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mx-1">
                    <div id="result" class="col"></div>
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
                        <div id="crud-category-result"></div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="stock-modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content p-3 p-md-5">
                        <div id="crud-stock-result"></div>
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
        <table id="productTable" class="table table-hover border-top">
            <thead>
                <tr>
                    <th>{{ __('ردیف') }}</th>
                    <th>{{ __('نام کالا') }}</th>
                    <th>{{ __('موجودی') }}</th>
                    <th>{{ __('قیمت خرید') }}</th>
                    <th>{{ __('قیمت فروش') }}</th>
                    <th>{{ __('تصویر محصول') }}</th>
                    <th>{{ __('وضعیت نمایش') }}</th>
                    <th>{{ __('مدیریت') }}</th>
                </tr>
            </thead>
        </table></div>`;

        function makeTable(fields = null) {
            $("#result").html(table);
            $("#productTable").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('tableProduct') }}",
                    "type": "GET",
                    "data": fields ?? {}
                },
                order:[1,'asc'],
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name'},
                    {data: 'stock'},
                    {data: 'buy'},
                    {data: 'sale'},
                    {data: 'image', searchable: false},
                    {data: 'status'},
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

            $(document.body).on("click", ".crud", function () {
                $("#loading").fadeIn();
                var action = $(this).data("action");
                var id = $(this).data("id");
                if (action == "create") {
                    var title = '@lang("محصول جدید")';
                } else {
                    var title = '@lang("ویرایش محصول")';
                }
                $("#offcanvas-title").html(title);
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudProduct') }}",
                    data: {
                        id: id,
                        action: action
                    },
                    success: function (data) {
                        $("#crud-result").html(data);
                        $("#categories").select2({
                            placeholder:"@lang('جهت انتخاب دسته ها کلیک کنید')",
                            dropdownParent: $('#offcanvasAddUser .offcanvas-body')
                        });
                        $("#loading").fadeOut();
                    },
                    error: function (data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "{{ __('اخطار') }}",
                            text: data.responseJSON.message,
                            icon: "error"
                        });
                    }
                });
            });

            $(document.body).on("click", "#store-product", function() {
                var e = checkEmpty("addNewUserForm");
                if (!e) {
                    $("#loading").fadeIn();
                    var action = $(this).data("action");
                    var id = $("#id").val();
                    var name = $("#name").val();
                    var stock = $("#stock").val();
                    var buy = $("#buy").val();
                    var sale = $("#sale").val();
                    var status = $("#status").find("option:selected").val();
                    var categories = $("#categories").val();
                    var image = $("#image").prop('files')[0] ?? '';
                    var form = $("#addNewUserForm");
                    var formData = new FormData(form[0]);
                    formData.append("image", image);
                    formData.append("categories", categories);
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeProduct') }}",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            makeTable();
                            $("#loading").fadeOut();
                            if (action == "create") {
                                var text="@lang('اطلاعات شما با موفقیت ثبت شد')";
                            }else{
                                var text="@lang('اطلاعات شما با موفقیت ویرایش شد')";
                            }
                            Swal.fire({
                                title: "@lang('موفق')",
                                text: text,
                                icon: "success"
                            });
                            if (action == "create") {
                                $("#id").val(0);
                                $("#name").val('');
                                $("#stock").val('');
                                $("#buy").val('');
                                $("#sale").val('');
                                $("#image").val('');
                                $("#status").val(1);
                                $("#categories").val('').trigger('change');
                            }
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

            $(document.body).on("click", ".delete-product", function() {
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
                            url: "{{ route('deleteProduct') }}",
                            data:{
                                id: id
                            },
                            success:function(data) {
                                makeTable();
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "{{ __('حذف شد') }}",
                                    text: "{{ __('محصول با موفقیت حذف شد.') }}",
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

            let data;

            $(document.body).on("click", "#search-product", function() {
                $("#loading").fadeIn();
                var name = $("#s_name").val();
                var stock = $("#s_stock").val();
                var from_buy = $("#s_from_buy").val();
                var to_buy = $("#s_to_buy").val();
                var from_sale = $("#s_from_sale").val();
                var to_sale = $("#s_to_sale").val();
                var category = $("#s_category").find("option:selected").val();
                var data = {
                    "name": name,
                    "stock": stock,
                    "from_buy": from_buy,
                    "to_buy": to_buy,
                    'from_sale': from_sale,
                    "to_sale": to_sale,
                    "category": category,
                    "filter_search": true
                }
                window['data'] = data;
                makeTable(data);
                $("#loading").fadeOut();
            });

            $(document.body).on("click", "#crud-category", function() {
                $("#loading").fadeIn();
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudCategory') }}",
                    success: function(data) {
                        $("#crud-category-result").html(data);
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

            $(document.body).on("click", "#store-category", function() {
                var e = checkEmpty("category-form")
                if (!e) {
                    $("#loading").fadeIn();
                    var id = $("#c-id").val();
                    var action = $("#c-action").val();
                    var name = $("#c-name").val();
                    var parent_id = $("#c-parent-id").val();
                    var status = $("#c-status").val();
                    var order = $("#c-order").val();
                    var details = $("#c-details").val();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeCategory') }}",
                        data: {
                            id: id,
                            action: action,
                            name: name,
                            parent_id: parent_id,
                            status: status,
                            order: order,
                            details: details
                        },
                        success: function(data) {
                            $("#crud-category-result").html(data);
                            $("#loading").fadeOut();
                            if (action == "create") {
                                var text="@lang('اطلاعات شما با موفقیت ثبت شد')";
                            }else{
                                var text="@lang('اطلاعات شما با موفقیت ویرایش شد')";
                            }
                            Swal.fire({
                                title: "{{ __('عملیات موفق') }}",
                                text: text,
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

            $(document.body).on("click", ".update-category", function() {
                $("#loading").fadeIn();
                var id = $(this).data("id");
                var action = $(this).data("action");
                var name = $(this).data("name");
                var parent_id = $(this).data("parent_id");
                var status = $(this).data("status");
                var order = $(this).data("order");
                var details = $(this).data("details");

                $("#store-category").html("ویرایش اطلاعات");
                $("#store-category").addClass("btn-warning");

                $("#c-id").val(id);
                $("#c-action").val(action);
                $("#c-name").val(name);
                $("#c-parent-id").val(parent_id);
                $("#c-status").val(status);
                $("#c-order").val(order);
                $("#c-details").val(details);
                $("#loading").fadeOut();
            });

            $(document.body).on("click", ".delete-category", function() {
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
                            url: "{{ route('deleteCategory') }}",
                            data:{
                                id: id
                            },
                            success:function(data) {
                                $("#crud-category-result").html(data);
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "{{ __('موفق') }}",
                                    text: "{{ __('دسته با موفقیت حذف شد.') }}",
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

            $(document.body).on("click", ".crud-stock", function() {
                $("#loading").fadeIn();
                var id = $(this).data("id");
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudStock') }}",
                    data: {
                        id: id
                    },
                    success: function(data) {
                        $("#crud-stock-result").html(data);
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

            $(document.body).on("click", ".update-stock", function() {

                var e = checkEmpty("stock-form");
                if (!e) {
                    $("#loading").fadeIn();
                    var id = $(this).data("id");
                    var sign = $(this).data("sign");
                    var change = $("#change").val();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('updateStock') }}",
                        data: {
                            id: id,
                            sign: sign,
                            change: change
                        },
                        success: function(data) {
                            $("#loading").fadeOut();
                            $("#result").html(data);
                            // $("#product-table").DataTable();
                            makeTable();
                            Swal.fire({
                                title: "{{ __('عملیات موفق') }}",
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
            });

            $(document.body).on("change", "#image", function() {
                var image = $("#image").prop('files')[0];
                var src = URL.createObjectURL(image);
                $("#new-image").attr("src", src);
                $("#old-image-container").hide();
                $("#new-image-container").show();
            });

            $(document.body).on("click", "#delete-old-image", function() {
                $("#old-image-container").hide();
                $("#no-image").val("true");
            });

            $(document.body).on("click", "#delete-new-image", function() {
                $("#new-image-container").hide();
                $("#image").val("");
                $("#old-image-container").show();
            });

            $(document.body).on("click", "#export", function() {
                $("#loading").fadeIn();
                $.ajax({
                    url: "{{ route('exportProduct') }}",
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
