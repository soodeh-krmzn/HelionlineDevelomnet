@extends('parts.master')

@section('title', __('پیکربندی عمومی'))

@section('head-styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}'">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor-fa.css') }}">
@stop
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="row">
            <div class="col-md-12">
                @include('setting.nav-bar', ['page' => 'personalize'])
                <div class="card mb-4">
                    <div class="card-body">
                        <div id="setting-form">
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label">{{ __('زبان') }}</label>
                                    @php
                                        switch (app()->getLocale()) {
                                            case 'en':
                                                $flag = 'fi-us';
                                                break;
                                            case 'fa':
                                                $flag = 'fi-ir';
                                                break;
                                        }
                                    @endphp
                                    <a class="btn btn-info" href="https://helionline.ir/?lang=fa" data-language="fa">
                                        <i class="fi fi-ir fis rounded-circle fs-4 me-1"></i>
                                        <span class="align-middle">{{ __('فارسی') }}</span>
                                    </a>
                                    <a class="btn btn-info" href="https://helionline.ir/?lang=en" data-language="en">
                                        <i class="fi fi-us fis rounded-circle fs-4 me-1"></i>
                                        <span class="align-middle">{{ __('انگلیسی') }}</span>
                                    </a>
                                    {{-- @can('unlimited')
                                        <a href="/translate" class="btn btn-primary btn-warning">{{ __('ترجمه') }}</a>
                                    @endcan --}}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-auto mb-3">
                                    <label class="form-label">{{ __('موقعیت زمانی') }}</label>
                                    <select name="timezone" class="form-select setting-input">
                                        <option value="">{{ __('انتخاب کنید') }}</option>
                                        <option value="Asia/Tehran"
                                            {{ $setting->getSetting('timezone') == 'Asia/Tehran' ? 'selected' : '' }}>
                                            @lang('تهران،ایران') (IRST)</option>
                                        <option value="Asia/Muscat"
                                            {{ $setting->getSetting('timezone') == 'Asia/Muscat' ? 'selected' : '' }}>
                                            @lang('مسقط،عمان') (GST)</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <label class="form-label">{{ __('تعداد ارقام موبایل') }}</label>
                                    <input type="number" name="mobile-count" class="form-control setting-input"
                                        placeholder="{{ __('تعداد ارقام موبایل') }}..."
                                        value="{{ $setting->mobileCount() }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-auto">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input repetitive-mobile" {{$setting->getSetting('repetitive-mobile')=='true'?'checked':''}} type="checkbox" id="mobile-uniqnesss">
                                        <label class="form-check-label" for="mobile-uniqnesss"> @lang('امکان ثبت موبایل تکراری')</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <button type="button" id="save-setting"
                                        class="btn btn-success">{{ __('ذخیره تغییرات') }}</button>
                                </div>
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

            let loading = `
            <div class="col text-center">
                <span class="spinner-grow" role="status" aria-hidden="true"></span>
            </div>`;

            $(document.body).on("click", "#save-setting", function() {

                var ip = checkIp();
                if (ip) {
                    return;
                }
                $("#loading").fadeIn();
                var list = $("#setting-form .setting-input").serialize();
                var repetitive_mobile=$('.repetitive-mobile').is(':checked');
                console.log(repetitive_mobile);
                var formData = new FormData();
                list+="&repetitive-mobile="+repetitive_mobile;
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
