@extends('parts.master')
@section('title', __('تنظیمات چاپ'))
@section('head-styles')
    <style>
        .dark-style .ck-content {
            background: #283144 !important;
        }

        .dark-style .ck-toolbar {
            background: #a2d59b !important;
        }
    </style>
@stop
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="row">
            <div class="col-md-12">
                @include('setting.nav-bar', ['page' => 'printSetting'])
                <div class="card my-4">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col">
                                <label class="form-label">{{ __('تنظیمات چاپ') }}</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    @lang('اندازه ها به پیکسل می باشند')
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="form-group">
                                    <label class="form-label">@lang('سایز سربرگ')</label>
                                    <input type="text" class="form-control just-numbers"
                                        value="{{ $setting->getSetting('headSize') }}" id="head-size" placeholder="30">
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="form-group">
                                    <label class="form-label">@lang('سایز عنوان')</label>
                                    <input type="text" class="form-control just-numbers"
                                        value="{{ $setting->getSetting('titleSize') }}" id="title-size" placeholder="25">
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="form-group">
                                    <label class="form-label">@lang('سایز متن')</label>
                                    <input type="text" class="form-control just-numbers"
                                        value="{{ $setting->getSetting('textSize') }}" id="text-size" placeholder="20">
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="form-group">
                                    <label class="form-label">@lang('سایز واحدپولی')</label>
                                    <input type="text" class="form-control just-numbers"
                                        value="{{ $setting->getSetting('currSize') }}" id="curr-size" placeholder="20">
                                </div>
                            </div>
                            <div class="mt-3 bill_header col-md-12">
                                <label class="form-label">{{ __('سربرگ چاپ صورتحساب') }}</label>
                                <textarea name="bill_header" class="form-control Reditor2" placeholder="{{ __('سربرگ چاپ صورتحساب') }}...">{{ $setting->getSetting('bill_header') }}</textarea>
                                {{-- <input type="text" name="bill_header" class="form-control text-start Reditor2 setting-input" placeholder="{{ __('سربرگ چاپ صورتحساب') }}..." value="{{ $setting->getSetting('bill_header') }}"> --}}
                            </div>
                            <div class="my-3 col-md-12 bill_sign">
                                <label class="form-label">{{ __('امضاء چاپ صورتحساب') }}</label>
                                <textarea name="bill_sign" class="form-control Reditor3" placeholder="{{ __('امضاء چاپ صورتحساب') }}...">{{ $setting->getSetting('bill_sign') }}</textarea>
                                {{-- <input type="text" name="bill_sign" class="form-control text-start setting-input" placeholder="{{ __('امضاء چاپ صورتحساب') }}..." value="{{ $setting->getSetting('bill_sign') }}"> --}}
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" style="visibility: hidden">@lang('سایز متن')</label>
                                    <div>
                                        <button id="store-btn" class="btn btn-primary">@lang('ثبت')</button>
                                    </div>
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
    <script src="{{ asset('assets/js/RMain.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#store-btn').on('click', function() {
                let titleSize = $('#title-size').val();
                let textSize = $('#text-size').val();
                let headSize = $('#head-size').val();
                let currSize = $('#curr-size').val();
                var bill_header=$('.bill_header .ck-content').html();
                var bill_sign=$('.bill_sign .ck-content').html();
                let data = {
                    titleSize: titleSize,
                    textSize: textSize,
                    headSize: headSize,
                    bill_sign:bill_sign,
                    bill_header:bill_header,
                    currSize:currSize,


                };
                actionAjax(window.location.href, data," @lang('موفق')", "@lang('تغییرات شما اعمال شد.')");
            });

            if ($(this).val() == "") {
                $("#no-price").show();
                $("#prices").hide();
            } else {
                $("#prices").show();
                $("#no-price").hide();
            }


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
