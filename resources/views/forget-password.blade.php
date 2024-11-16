@extends('parts.auth-master')
@section('title', 'بازیابی رمز ورود')
@section('head-styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
    <style type="text/css">
        #otp-form {
            display: none;
        }
    </style>
@stop
@section('content')
    <div class="app-brand justify-content-center">
        <img src="assets/img/logo.png">
    </div>
    <div id="mobile-form">
        <form action="">
            <h4 class="mb-3 secondary-font">{{ __('بازیابی رمز ورود') }}</h4>
            <div id="formAuthentication" class="mb-3">
                <div class="mb-3 form-password-toggle">
                    <label class="form-label" for="mobile">{{ __('شماره موبایل') }} <span class="text-danger">*</span></label>
                    <div class="input-group input-group-merge">
                        <input type="text" name="mobile" id="mobile" class="form-control text-start" placeholder="{{ __('شماره موبایل') }}..." aria-describedby="mobile">
                    </div>
                </div>
                <button type="submit" id="check-mobile" class="btn btn-primary d-grid w-100 mb-3 check-mobile">{{ __('ارسال کد تایید') }}</button>
            </div>
        </form>
    </div>
    <div id="otp-form">
        <p class="text-start mb-4">{{ __('ما یک کد تایید به موبایل شما ارسال کردیم. کد ارسال شده را در کادر زیر وارد نمایید.') }}</p>
        <div id="twoStepsForm">
            <form action="">
                <div class="mb-3">
                    <div class="auth-input-wrapper d-flex align-items-center justify-content-sm-between numeral-mask-wrapper">
                        <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" maxlength="1" autofocus>
                        <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" maxlength="1">
                        <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" maxlength="1">
                        <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" maxlength="1">
                        <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" maxlength="1">
                        <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" maxlength="1">
                    </div>
                    <!-- Create a hidden field which is combined by 3 fields above -->
                    <input type="hidden" name="otp" id="otp">
                </div>
                <button type="submit" id="check-otp" class="btn btn-primary d-grid w-100 mb-3">{{ __('تایید کد') }}</button>
                <div class="text-center">
                    {{ __('کد را دریافت نکردید؟') }}
                    <a href="javascript:void(0);" class="check-mobile" id="send-again">{{ __('ارسال دوباره') }}</a>
                    <div id="seconds"></div>
                </div>
            </form>
        </div>
    </div>
    <p class="text-center">
        <a href="{{ route('login') }}">
            <span>{{ __('بازگشت به فرم ورود') }}</span>
        </a>
    </p>
@endsection
@section('footer-scripts')
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>

    <script src="{{ asset('assets/js/pages-auth-two-steps.js') }}"></script>
    <script type="text/javascript">
        function timer() {
            $("#send-again").hide();
            $("#seconds").show();
            var seconds = 60;
            var countdown = setInterval(function() {
                seconds -= 1;
                $("#seconds").html(seconds + ' ثانیه دیگر، مجدد می توانید درخواست کد دهید.');
                if (seconds < 0) {
                    clearInterval(countdown);
                    $("#seconds").hide();
                    $("#send-again").show();
                }
            }, 1000);
        }

        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document.body).on("click", ".check-mobile", function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "{{ __('لطفا کمی صبر نمایید.') }}",
                    text: "{{ __('در حال پردازش هستیم.') }}",
                    icon: "info",
                    showCancelButton: false,
                    showConfirmButton: false,
                    allowOutsideClick: false
                });

                var mobile = $("#mobile").val();

                if (mobile == "") {
                    Swal.fire({
                        title: "{{ __('اخطار') }}",
                        text: "{{ __('لطفا شماره موبایل خود را وارد نمایید.') }}",
                        icon: "error"
                    });
                } else {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('checkMobile') }}",
                        data: {
                            mobile: mobile
                        },
                        success: function(data) {
                            Swal.fire({
                                title: "{{ __('احراز شماره موبایل') }}",
                                text: "{{ __('کد احراز شماره موبایل برای شما پیامک شد.') }}",
                                icon: "success"
                            });
                            $("#mobile-form").hide();
                            $("#otp-form").show();
                            timer();
                        },
                        error: function (data) {
                            Swal.fire({
                                title: "@lang('خطا')",
                                text: data.responseJSON.message,
                                icon: "error"
                            });
                        }
                    });
                }
            });

            $(document.body).on("click", "#check-otp", function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "{{ __('لطفا کمی صبر نمایید...') }}",
                    text: "{{ __('در حال پردازش هستیم.') }}",
                    icon: "info",
                    showCancelButton: false,
                    showConfirmButton: false,
                    allowOutsideClick: false
                });

                var otp = $("#otp").val();
                var mobile = $("#mobile").val();

                if (otp == "") {
                    Swal.fire({
                        title: "{{ __('اخطار') }}",
                        text: "{{ __('لطفا کد احراز را وارد نمایید.') }}",
                        icon: "error"
                    });
                } else {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('checkOTP') }}",
                        data: {
                            otp: otp,
                            mobile: mobile
                        },
                        success: function(data) {
                            Swal.fire({
                                title: "{{ __('عملیات موفق') }}",
                                text: "{{ __('احراز شماره موبایل با موفقیت انجام و رمز جدید برای شما پیامک شد.') }}",
                                icon: "success"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "/login";
                                } else if (result.isDenied) {
                                    window.location.href = "/login";
                                }
                            });
                        },
                        error: function (data) {
                            Swal.fire({
                                title: "{{ __('اخطار') }}",
                                text: "{{ __('اطلاعات وارد شده صحیح نمی باشد.') }}",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        });
    </script>
@stop
