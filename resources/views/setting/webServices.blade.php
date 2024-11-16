@extends('parts.master')
@section('title', __('وب سرویس ها'))
@section('head-styles')
    <style>
    </style>
@stop
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="row">
            <div class="col-md-12">
                @include('setting.nav-bar', ['page' => 'webServices'])
                <div class="card my-4">
                    <div class="card-body">
                        <div class="card">
                            <div class="card-body">
                                <h4>سرویس های پیامک</h4>
                                <div class="mb-3 row">
                                    <label for="staticEmail" class="col-sm-2 col-form-label">پنل پیامکی خودرا انتخاب
                                        کنید</label>
                                    <div class="col-sm-10">
                                        <div class="row">
                                            <div class="col-sm-10 col-auto">
                                                <select id="sms_panel" class="form-select">
                                                    <option value="">هلی سافت</option>
                                                    <option @selected(($smsPanel = $setting->getSetting('sms_panel')) == 'فراز sms') value="فراز sms">فراز sms</option>
                                                    <option @selected($smsPanel == 'تابان sms') value="تابان sms">تابان sms
                                                    </option>
                                                    <option @selected($smsPanel == 'مدیانا') value="مدیانا">مدیانا</option>
                                                    <option @selected($smsPanel == 'پیام تک') value="پیام تک">پیام تک</option>
                                                </select>
                                            </div>
                                            <div class="col-auto">
                                                <button id="check_sms_panel" class="btn btn-info">تست اتصال</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sms-options" style="{{ $smsPanel ? '' : 'display:none' }}">
                                    <div class="row">
                                        <div class="col-sm-8 form-group">
                                            <label for="" class="form-label">کلید پنل</label>
                                            <input value="{{ $setting->getSetting('sms_api_key') }}" type="text"
                                                id="sms_api_key" class="form-control">
                                        </div>
                                        <div class="col-sm-4 form-group">
                                            <label for="" class="form-label">شماره خط ارسال پیامک</label>
                                            <input value="{{ $setting->getSetting('sms_number') }}" type="text"
                                                id="sms_number" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12 text-center">
                                        <button class="btn btn-primary" id="sms-store-btn">ذخیره</button>
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
            window.sms_panel = "{{ $setting->getSetting('sms_panel') }}";
            $('#sms_panel').on('change', function() {
                if ($(this).val()) {
                    $('.sms-options').show();
                } else {
                    $('.sms-options').hide();
                }
            });
            $('#sms-store-btn').on('click', function() {
                let sms_api_key = $('#sms_api_key').val();
                let sms_number = $('#sms_number').val();
                let sms_panel = $('#sms_panel').val();
                let data = {
                    sms_api_key: sms_api_key,
                    sms_number: sms_number,
                    sms_panel: sms_panel
                };
                actionAjax(window.location.href, data, "@lang('موفق')", "@lang('تغییرات شما اعمال شد.')", function(
                    response) {
                    window.sms_panel = sms_panel;
                });
            });
            $('#check_sms_panel').on('click', function() {
                if (window.sms_panel == false) {
                    Swal.fire({
                        title: "توجه",
                        text: "امکان استعلام از پنل هلی سافت وجود ندارد",
                        icon: "warning",
                    });
                    return;
                }
                let data = {
                    target: 'check_sms_panel_status'
                };
                actionAjax(window.location.href, data, "null", "null", function(response) {
                    Swal.fire({
                        title: "ارتباط موفق",
                        text: 'میزان شارژ پنل شما برار با: ' + response + " ریال می باشد\n",
                        icon: "success",
                    });
                }, function(response) {
                    console.log(response);
                    Swal.fire({
                        title: "ارتباط ناموفق",
                        text: response.responseJSON?.error,
                        icon: "error",
                    });
                });
            });


        });
    </script>

@stop
