@extends('parts.auth-master')
@section('title', 'ورود')
@section('head-styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
@stop
@section('content')
    <div class="app-brand justify-content-center">
        <img src="{{ asset('assets/img/logo.png') }}">
    </div>
    <h4 class="mb-3 text-center secondary-font">{{ __('به هلی آنلاین خوش اومدین.') }}</h4>
    <div id="formAuthentication" class="mb-3">
        <div class="mb-3">
            <label for="username" class="form-label">{{ __('نام کاربری') }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control text-start" id="username" placeholder="{{ __('نام کاربری') }}...">
        </div>
        <div class="mb-3 form-password-toggle">
            <div class="d-flex justify-content-between">
                <label for="password" class="form-label">{{ __('رمز ورود') }} <span class="text-danger">*</span></label>
                <a href="{{ route('forgetPassword') }}">
                    <small>{{ __('رمز عبور خود را فراموش کردید؟') }}</small>
                </a>
            </div>
            <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control text-start" name="password" placeholder="{{ __('رمز ورود') }}..." aria-describedby="password">
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
            </div>
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember-me">
                <label class="form-check-label" for="remember-me">{{ __('به خاطر سپاری') }}</label>
            </div>
        </div>
        <div class="mb-3">
            <button type="button" id="check-login" class="btn btn-primary d-grid w-100 submit-by-enter">{{ __('ورود') }}</button>
        </div>
    </div>
    <p class="text-center">
        <span>{{ __('کاربر جدید هستید؟') }}</span>
        <a href="{{ route('register') }}">
            <span>{{ __('یک حساب بسازید.') }}</span>
        </a>
    </p>
@endsection
@section('footer-scripts')
    <script type="text/javascript">
        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let loading = `
            <div class="col text-center">
                <span class="spinner-grow" role="status" aria-hidden="true"></span>
            </div>`;

            $(document.body).on("click", "#check-login", function() {
                Swal.fire({
                    title: "{{ __('لطفا کمی صبر نمایید...') }}",
                    text: "{{ __('در حال پردازش هستیم.') }}",
                    icon: "info",
                    showCancelButton: false,
                    showConfirmButton: false,
                    allowOutsideClick: false
                });

                var username = $("#username").val();
                var password = $("#password").val();

                if (username == "" || password == "") {
                    Swal.fire({
                        title: "{{ __('اخطار') }}",
                        text: "{{ __('لطفا نام کاربری و رمز خود را وارد نمایید.') }}",
                        icon: "error"
                    });
                } else {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('checkLogin') }}",
                        data: {
                            username: username,
                            password: password
                        },
                        success: function (data) {
                            Swal.fire({
                                title: "{{ __('عملیات موفق') }}",
                                text: data.message,
                                icon: "success",
                                timer: 1000
                            }).then((result) => {
                                window.location.href = "/";
                            });
                        },
                        error: function (data) {
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
    </script>
@stop
