@extends('parts.master')

@section('title', __('باشگاه مشتریان'))

@section('head-styles')

@stop
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col">
                                <label class="form-label">@lang('تبدیل مبلغ پرداختی به امتیاز برای') :</label>
                            </div>
                            <div class="col">
                                <select id="type-selector" class="form-select">
                                    <option value="">@lang('انتخاب')...</option>
                                    <option value="game">@lang('پرداخت بازی')</option>
                                    <option value="factor">@lang('فاکتور فروشگاه')</option>
                                    <option value="payment">@lang('ثبت پرداختی')</option>
                                    <option value="package">@lang('بسته اشتراکی')</option>
                                </select>
                            </div>
                        </div>
                        <div id="club-result">
                            <div class="row m-0">
                                <div class="col-md-12 alert alert-danger text-center">
                                    @lang('لطفا یکی از گزینه ها را انتخاب کنید.')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Content -->
@stop
@section('footer-scripts')
    <script type="text/javascript">
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document.body).on("click", "#store-club", function() {
                var price = $("#price").val();
                var rate = $("#rate").val();
                if (price != "" && rate != "") {
                    $("#loading").fadeIn();
                    var type = $("#type-selector").find("option:selected").val();
                    var price = $("#price").val();
                    if (price <= 0) {
                        Swal.fire({
                            title: "@lang('خطا')",
                            text: "@lang('مبلغ پرداختی جهت دریافت یک امتیاز نمی تواند صفر باشد')",
                            icon: "error"
                        });
                        $("#loading").fadeOut();
                        return;
                    }
                    var rate = $("#rate").val();
                    var min_price = $("#min-price").val();
                    var expire = $("#expire").val();
                    var people = $("#people").find("option:selected").val();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('storeClub') }}",
                        data: {
                            type: type,
                            price: price,
                            rate: rate,
                            min_price: min_price,
                            expire: expire,
                            people: people
                        },
                        success: function(data) {
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "@lang('موفق')",
                                text: "با موفقیت ثبت شد.",
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
                } else {
                    Swal.fire({
                        title: "@lang('خطا')",
                        text: "لطفا مقادیر ستاره دار را تکمیل نمایید.",
                        icon: "error"
                    });
                }
            });

            $(document.body).on("click", "#delete-club", function() {
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
                        var type = $("#type-selector").find("option:selected").val();
                        $.ajax({
                            type: "POST",
                            url: "{{ route('deleteClub') }}",
                            data: {
                                type: type
                            },
                            success: function(data) {
                                $("#club-result").html(data);
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "@lang('موفق')",
                                    text: "با موفقیت حذف شد.",
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

            $(document.body).on("change", "#type-selector", function() {
                $("#loading").fadeIn();
                var type = $(this).find("option:selected").val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('crudClub') }}",
                    data: {
                        type: type
                    },
                    success: function(data) {
                        $("#club-result").html(data);
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
