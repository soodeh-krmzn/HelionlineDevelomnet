@extends('parts.master')
@section('title', __('تعرفه'))
@section('head-styles')
@stop
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="row">
            <div class="col-md-12">
                @include('setting.nav-bar', ['page' => 'price'])
                <div class="card my-4">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('لیست تعرفه') }}</label>
                                @if ($section->getSelect()->count() > 0)
                                    <select id="section-selector" class="form-select">
                                        <option value="">{{ __('انتخاب بخش') }}</option>
                                        @foreach ($section->getSelect() as $sectionRow)
                                            <option value="{{ $sectionRow->id }}">{{ $sectionRow->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="col-md-6 section-type" style="display: none">
                                <label class="form-label">{{ __('نوع تعرفه') }}</label>
                                <select id="section-type" class="form-select">
                                    <option value="waterfall">{{ __('آبشاری') }}</option>
                                    <option value="stair">{{ __('پله ای') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            @if ($section->getSelect()->count() > 0)
                                <div class="col-12" id="prices" style="display:none;">
                                    @can('create')
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr class="table-warning">
                                                        <th>{{ __('از دقیقه') }} <span class="text-danger">*</span></th>
                                                        <th>{{ __('تا دقیقه') }} <span class="text-danger">*</span></th>
                                                        <th>{{ __('مبلغ ورودی') }}</th>
                                                        <th>{{ __('نوع محاسبه') }}</th>
                                                        <th>{{ __('نرخ') }} <span class="text-danger">*</span></th>
                                                        <th>{{ __('نوع نرخ') }}</th>
                                                        <th>{{ __('عملیات') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <input type="text" data-id="from"
                                                                class="form-control just-numbers money-filter"
                                                                placeholder="{{ __('از دقیقه') }}...">
                                                            <input type="hidden" id="from"
                                                                class="form-control just-numbers">
                                                        </td>
                                                        <td>
                                                            <input type="text" data-id="to"
                                                                class="form-control just-numbers money-filter"
                                                                placeholder="{{ __('تا دقیقه') }}...">
                                                            <input type="hidden" id="to"
                                                                class="form-control just-numbers">
                                                        </td>
                                                        <td>
                                                            <input type="text" data-id="entrance-price"
                                                                class="form-control just-numbers money-filter"
                                                                placeholder="{{ __('مبلغ ورودی') }}...">
                                                            <input type="hidden" id="entrance-price"
                                                                class="form-control just-numbers">
                                                        </td>
                                                        <td>
                                                            <select id="calc-type" class="form-select ">
                                                                <option value="min">{{ __('دقیقه ای') }}</option>
                                                                <option value="all">{{ __('کلی') }}</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" data-id="price"
                                                                class="form-control just-numbers money-filter"
                                                                placeholder="{{ __('نرخ') }}...">
                                                            <input type="hidden" id="price"
                                                                class="form-control just-numbers">
                                                        </td>
                                                        <td>
                                                            <select id="price-type" class="form-select ">
                                                                <option value="normal">{{ __('عادی') }}</option>
                                                                <option value="vip">{{ __('ویژه') }}</option>
                                                                <option value="extra">{{ __('مازاد') }}</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <button type="button" id="store-price"
                                                                class="btn btn-success btn-sm"><i
                                                                    class="bx bx-plus"></i></button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    @endcan
                                    <div id="price-form-result" class="mt-2 table-responsive"></div>
                                </div>
                                <div class="col-12" id="no-price">
                                    <div class="alert alert-danger text-center m-0">
                                        {{ __('لطفا بخش را انتخاب کنید.') }}
                                    </div>
                                </div>
                            @else
                                <div class="col-12" id="no-price">
                                    <div class="alert alert-danger text-center m-0">
                                        {{ __('لطفا ابتدا بخش های مجموعه خود را تعریف نمایید.') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h5>{{ __('تنظیمات تعرفه') }}</h5>
                        <div class="row">
                            <div class="col-auto">
                                <label class="form-label">{{ __('وضعیت رندسازی') }}</label>
                                <select class="form-control form-select" id="round-status" style="min-width: 140px">
                                    <option @selected($setting->getSetting('round-status') == 0) value="0">{{ __('غیرفعال') }}</option>
                                    <option @selected($setting->getSetting('round-status') == 1) value="1">{{ __('فعال') }}</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-label">{{ __('نوع رندسازی') }}</label>
                                <select class=" form-control form-select" style="min-width: 140px" id="round-type">
                                    <option @selected($setting->getSetting('round-type') == 'top') value="top">{{ __('به سمت بالا') }}</option>
                                    <option @selected($setting->getSetting('round-type') == 'down') value="down">{{ __('به سمت پایین') }}</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-label">{{ __('ضریب رندسازی') }}</label>
                                <select class="form-control form-select" id="round-odd" style="min-width: 140px">
                                    <option @selected($setting->getSetting('round-odd') == 1000) value="1000">{{ cnf(1000) }}</option>
                                    <option @selected($setting->getSetting('round-odd') == 5000) value="5000">{{ cnf(5000) }}</option>
                                    <option @selected($setting->getSetting('round-odd') == 10000) value="10000">{{ cnf(10000) }}</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="form-label">{{ __('مالیات بر ارزش افزوده') }}</label>
                                <input type="text" id="vat" class="form-control just-numbers"
                                    placeholder="{{ __('نرخ مالیات') }}..." value="{{ $setting->getSetting('vat') }}">
                            </div>
                            <div class="col-auto">
                                <label class="form-label">{{ __('انتخاب واحد پولی') }}</label>
                                @php
                                    $curr = $setting->getSetting('curr');
                                @endphp
                                <select id="curr" class="form-select" style="min-width: 165px;">
                                    <option value="">{{ __('عدم نمایش') }}</option>
                                    <option @selected($curr == 'ریال') value="ریال">ریال</option>
                                    <option @selected($curr == 'تومان') value="تومان">تومان</option>
                                    <option @selected($curr == 'RO') value="RO">RO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary" id="round-btn">{{ __('ذخیره تنظیمات') }}</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop
@section('footer-scripts')
    <script src="{{ asset('assets/js/RMain.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#round-btn').on('click', function() {
                let roundStatus = $('#round-status').val();
                let roundType = $('#round-type').val();
                let roundOdd = $('#round-odd').val();
                let vat = $('#vat').val();
                let curr = $('#curr').val();
                let global_offer = $('#global_offer').val();
                let data = {
                    roundStatus: roundStatus,
                    roundOdd: roundOdd,
                    roundType: roundType,
                    vat: vat,
                    curr: curr,
                    global_offer: global_offer,
                }
                actionAjax(window.location.href, data, "@lang('موفق')", "@lang('تغییرات شما اعمال شد.')");
            });

            if ($(this).val() == "") {
                $("#no-price").show();
                $("#prices").hide();
            } else {
                $("#prices").show();
                $("#no-price").hide();
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document.body).on("click", "#store-price", function() {
                var section_id = $("#section-selector").find("option:selected").val();
                var entrance_price = $("#entrance-price").val();
                var from = $("#from").val();
                var to = $("#to").val();
                var calc_type = $("#calc-type").val();
                var price = $("#price").val();
                var price_type = $("#price-type").find("option:selected").val();

                if (from != "" && to != "" && price != "") {
                    $("#loading").fadeIn();

                    $.ajax({
                        type: "POST",
                        url: "{{ route('storePrice') }}",
                        data: {
                            action: "create",
                            id: 0,
                            section_id: section_id,
                            entrance_price: entrance_price,
                            from: from,
                            to: to,
                            calc_type: calc_type,
                            price: price,
                            price_type: price_type
                        },
                        success: function(data) {
                            $("#price-form-result").html(data);
                            $("#from").val("");
                            $("input[data-id=from]").val("");
                            $("#to").val("");
                            $("input[data-id=to]").val("");
                            $("#price").val("");
                            $("input[data-id=price]").val("");
                            $("#entrance-price").val("");
                            $("input[data-id=entrance-price]").val("");
                            $("#loading").fadeOut();
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
                    $("#loading").fadeOut();
                    Swal.fire({
                        title: "@lang('خطا')",
                        text: "@lang('لطفا کادرهای ستاره دار را تکمیل نمایید.')",
                        icon: "error"
                    });
                }
            });

            $(document.body).on("click", ".update-price", function() {
                $("#loading").fadeIn();
                var id = $(this).data("id");
                var section_id = $("#section-selector").find("option:selected").val();
                var entrance_price = $("#entrance-price" + id).val();
                var from = $("#from" + id).val();
                var to = $("#to" + id).val();
                var calc_type = $("#calc-type" + id).val();
                var price = $("#price" + id).val();
                var price_type = $("#price-type" + id).find("option:selected").val();

                $.ajax({
                    type: "POST",
                    url: "{{ route('storePrice') }}",
                    data: {
                        action: "update",
                        id: id,
                        section_id: section_id,
                        entrance_price: entrance_price,
                        from: from,
                        to: to,
                        calc_type: calc_type,
                        price: price,
                        price_type: price_type
                    },
                    success: function(data) {
                        $("#price-form-result").html(data);
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('موفق')",
                            text: "@lang('با موفقیت ویرایش شد.')",
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
            });

            $(document.body).on("click", ".delete-price", function() {
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
                        var section_id = $("#section-selector").find("option:selected").val();

                        $.ajax({
                            type: "POST",
                            url: "{{ route('deletePrice') }}",
                            data: {
                                id: id,
                                section_id: section_id
                            },
                            success: function(data) {
                                $("#price-form-result").html(data);
                                $("#loading").fadeOut();
                                Swal.fire({
                                    title: "@lang('موفق')",
                                    text: "@lang('با موفقیت حذف شد.')",
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
            $(document.body).on("change", "#section-type", function() {
                data={
                    id:$(this).data('id'),
                    type:$(this).val(),
                    target:'change-type'
                }
                actionAjax(window.location,data,'موفق','نوع تعرفه تغییر کرد');
            });
            $(document.body).on("change", "#section-selector", function() {
                if ($(this).val() == "") {
                    $("#no-price").show();
                    $("#prices").hide();
                } else {
                    $("#prices").show();
                    $("#no-price").hide();
                    $(".section-type").show();
                }
                $("#loading").fadeIn();
                var id = $(this).find("option:selected").val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('priceForm') }}",
                    data: {
                        id: id
                    },
                    success: function(response) {
                        console.log(response.section);

                        $('#section-type').val(response.section.type);
                        $('#section-type').data('id',response.section.id)
                        $("#price-form-result").html(response.form);
                        $("#loading").fadeOut();
                        moneyFilter();
                        $('.money-filter').trigger('keyup');
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
