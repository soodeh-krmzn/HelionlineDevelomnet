@extends('parts.master')

@section('title', __('پیکربندی کیف پول'))

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
                <div class="card mb-4">
                    <div class="card-body">
                        <div id="setting-form">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">@lang('درصد هدیه شارژ کیف پول')</label>
                                    <input type="text" name="wallet_gift_percent" class="form-control just-numbers" placeholder="@lang('درصد هدیه')..." value="{{ $setting->getSetting("wallet_gift_percent") }}">
                                </div>
                                <div class="col-md-6 d-flex align-items-end justify-content-start">
                                    <button type="button" id="save-setting" class="btn btn-success me-2">@lang('ذخیره تغییرات')</button>
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

            $(document.body).on("click", "#save-setting", function() {
                $("#loading").fadeIn();
                var list = $("#setting-form *").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ route('updateSetting') }}",
                    data: {
                        list: list
                    },
                    success:function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('ذخیر شد.')",
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
