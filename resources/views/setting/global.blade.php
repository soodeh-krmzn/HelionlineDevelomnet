@extends('parts.master')

@section('title', __('عمومی'))

@section('head-styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}'">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor-fa.css') }}">
    <style>
        .dark-style .ck-content{
            background: #283144 !important;
        }
        .dark-style .ck-toolbar{
            background: #a2d59b !important;
        }
    </style>
@stop
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="row">
            <div class="col-md-12">
                @include('setting.nav-bar', ['page' => 'global'])
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <img src="{{ $setting->getSetting('avatar') != '' ? $setting->getSetting('avatar') : asset('assets/img/default-avatar.png') }}"
                                alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar"
                                style="object-fit: contain;">
                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                    <span class="d-none d-sm-block">{{ __('انتخاب تصویر جدید') }}</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                    <input type="file" id="upload" class="account-file-input" hidden
                                        accept="image/png, image/jpeg">
                                    <input type="hidden" name="no_image" id="no-image" value="false">
                                </label>
                                <button type="button" id="cancel" class="btn btn-label-danger account-image-reset mb-4">
                                    <i class="bx bx-reset d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">{{ __('حذف تصویر') }}</span>
                                </button>
                                <p class="mb-1">{{ __('تصویر را انتخاب و بر روی دکمه ذخیره تغییرات کنید.') }}</p>
                                <small class="mb-0">(JPG, PNG & Max Size 1MB)</small>
                            </div>
                        </div>
                    </div>
                    <hr class="my-0">
                    <div class="card-body">
                        <div id="setting-form">
                            <div class="row">
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">{{ __('نام مجموعه') }}</label>
                                    <input type="text" name="center_name" class="form-control setting-input"
                                        placeholder="{{ __('نام مجموعه') }}..."
                                        value="{{ $setting->getSetting('center_name') }}">
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">{{ __('آی پی ثابت') }}</label>
                                    <input type="text" name="static_ip" data-id="static-ip"
                                        class="form-control checkIp setting-input" placeholder="{{ __('آی پی ثابت') }}..."
                                        value="{{ $setting->getSetting('static_ip') }}">
                                    <div class="invalid-feedback" data-id="static-ip" data-error="checkIp"></div>
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">{{ __('نمایش موجودی محصول هنگام فروش') }}</label>
                                    <select name="show_product_stock" class="form-select setting-input">
                                        <option value="1"
                                            {{ $setting->getSetting('show_product_stock') == 1 ? 'selected' : '' }}>
                                            {{ __('فعال') }}</option>
                                        <option value="0"
                                            {{ $setting->getSetting('show_product_stock') == 0 ? 'selected' : '' }}>
                                            {{ __('غیر فعال') }}</option>
                                    </select>
                                    {{-- <label class="switch switch-success switch-lg">
                                        <input type="checkbox" class="switch-input" name="show_product_stock" {{ $setting->getSetting("show_product_stock") == "off" ? "checked" : ""}}>
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on">بله</span>
                                            <span class="switch-off">خیر</span>
                                        </span>
                                    </label> --}}
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">{{ __('نمایش فقط کالاهای موجود') }}</label>
                                    <select name="just_in_stock_product_status" class="form-select setting-input">
                                        <option value="0"
                                            {{ $setting->getSetting('just_in_stock_product_status') == 0 ? 'selected' : '' }}>
                                            {{ __('غیرفعال') }}</option>
                                        <option value="1"
                                            {{ $setting->getSetting('just_in_stock_product_status') == 1 ? 'selected' : '' }}>
                                            {{ __('فعال') }}</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="column"
                                        class="form-label">{{ __('ستون های قابل نمایش در داشبورد') }}</label>
                                    <select name="game_table_columns" id="columns" class="form-select select2" multiple>
                                        <option value="person_id"
                                            {{ in_array('person_id', explode(',', $setting->getSetting('game_table_columns'))) ? 'selected' : '' }}>
                                            {{ __('کد عضویت') }}</option>
                                        <option value="reg_code"
                                            {{ in_array('reg_code', explode(',', $setting->getSetting('game_table_columns'))) ? 'selected' : '' }}>
                                            {{ __('کد اشتراک') }}</option>
                                        <option value="count"
                                            {{ in_array('count', explode(',', $setting->getSetting('game_table_columns'))) ? 'selected' : '' }}>
                                            {{ __('تعداد') }}</option>
                                        <option value="counter_id"
                                            {{ in_array('counter_id', explode(',', $setting->getSetting('game_table_columns'))) ? 'selected' : '' }}>
                                            {{ __('شمارنده') }}</option>
                                        <option value="section_name"
                                            {{ in_array('section_name', explode(',', $setting->getSetting('game_table_columns'))) ? 'selected' : '' }}>
                                            {{ __('بخش') }}</option>
                                        <option value="station_name"
                                            {{ in_array('station_name', explode(',', $setting->getSetting('game_table_columns'))) ? 'selected' : '' }}>
                                            {{ __('ایستگاه') }}</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="lastName" class="form-label">{{ __('آدرس و تلفن') }}</label>
                                    <input type="text" name="center_address" class="form-control setting-input"
                                        placeholder="{{ __('آدرس و تلفن') }}..."
                                        value="{{ $setting->getSetting('center_address') }}">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('لینک دوربین مداربسته') }}</label>
                                    <input type="text" name="camera_link" style="direction: ltr"
                                        class="form-control text-left setting-input"
                                        placeholder="{{ __('لینک دوربین مداربسته') }}..."
                                        value="{{ $setting->getSetting('camera_link') }}">
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">{{ __('نوع استعلام') }}</label>
                                    <select name="load_game_type" class="form-select setting-input">
                                        <option value="modal"
                                            {{ $setting->getSetting('load_game_type') == 'modal' ? 'selected' : '' }}>
                                            {{ __('پاپ آپ') }}</option>
                                        <option value="page"
                                            {{ $setting->getSetting('load_game_type') == 'page' ? 'selected' : '' }}>
                                            {{ __('آبشاری') }}</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">{{ __('تعداد شلوغی') }}</label>
                                    <input type="text" name="crowd_number"
                                    class="form-control text-left setting-input just-numbers"
                                    placeholder="{{ __('تعداد شلوغی') }}..."
                                    value="{{ $setting->getSetting('crowd_number') }}">
                                </div>
                                <div class="mb-3 rules col-md-12">
                                    <label for="rules" class="form-label">{{ __('قوانین') }}</label>
                                    <textarea name="rules" class="form-control  Reditor1" placeholder="{{ __('قوانین') }}...">{{ $setting->getSetting('rules') }}</textarea>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" id="save-setting"
                                    class="btn btn-success me-2">{{ __('ذخیره تغییرات') }}</button>
                            </div>
                        </div>
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

            $("#columns").select2({
                placeholder: "@lang('جهت انتخاب کلیک کنید')..."
            });

            let loading = `
            <div class="col text-center">
                <span class="spinner-grow" role="status" aria-hidden="true"></span>
            </div>`;

            $(document.body).on("click", "#save-setting", function() {

                var ip = checkIp();
                if (ip) {
                    return;
                }
                // $("#loading").fadeIn();
                var list = $("#setting-form .setting-input").serialize();

                var columns = $("#columns").val();
                list += "&game_table_columns=" + columns;
                var rules=$('.rules .ck-content').html();
                list+="&rules="+rules;
                // var bill_header=$('.bill_header .ck-content').html();
                // list+="&bill_header="+bill_header;
                // var bill_sign=$('.bill_sign .ck-content').html();
                // list+="&bill_sign="+bill_sign;

                var image = $("#upload").prop('files')[0] ?? '';
                var formData = new FormData();
                formData.append("image", image);
                formData.append("no_image", $("#no-image").val());
                formData.append("list", list);


                $.ajax({
                    type: "POST",
                    url: "{{ route('updateSetting') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('ذخیره شد.')",
                            text: "@lang('تنظیمات با موفقیت ذخیره شد.')",
                            icon: "success"
                        });
                        $('#upload').val('');
                        $("#no-image").val('false');
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

            $(document.body).on("change", "#upload", function() {
                var image = $("#upload").prop('files')[0];
                var src = URL.createObjectURL(image);
                $("#uploadedAvatar").attr("src", src);
            });

            $(document.body).on("click", "#cancel", function() {
                console.log($("#upload").val())
                if ($("#upload").val() == '') {
                    console.log('yes')
                    $("#no-image").val('true');
                    var src = "{{ asset('assets/img/no-img.jpg') }}";
                } else {
                    var src =
                        "{{ $setting->getSetting('avatar') != '' ? $setting->getSetting('avatar') : asset('assets/img/no-img.jpg') }}";
                    $('#upload').val('');
                    $("#no-image").val('false');
                }
                $("#uploadedAvatar").attr("src", src);
            });

        });
    </script>
    <script src="{{ asset('assets/js/ckeditor.js') }}"></script>
    <script>
        function editor(className) {
            ClassicEditor.create(document.querySelector(className), {
                toolbar: {
                    items: [
                        'undo', 'redo',
                        '|', 'heading',
                        '|', 'bold', 'italic',
                        '|', 'link', 'insertImage', 'insertTable', 'mediaEmbed',
                        '|', 'bulletedList', 'numberedList'
                    ]
                },
                language: {
                    // The UI will be Arabic.
                    ui: 'fa',
                    // And the content will be edited in Arabic.
                    content: 'fa'
                }
            }).catch(error => {
                console.error(error);
            });
        }
        editor('.Reditor1');
        editor('.Reditor2');
        editor('.Reditor3');
    </script>
@stop
