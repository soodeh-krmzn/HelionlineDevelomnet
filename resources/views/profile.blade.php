@extends('parts.master')

@section('title', 'پروفایل من')

@section('head-styles')

@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="card">
            <div class="card-datatable table-responsive">
                <div class="row mx-1 my-3">
                    <div class="col">
                        <table class="table table-hover border-top">
                            <thead>
                                <th>@lang('نام')</th>
                                <th>@lang('نام خانوادگی')</th>
                                <th>@lang('موبایل')</th>
                                <th>@lang('نام کاربری')</th>
                                <th>@lang('نقش')</th>
                                <th>دسترسی</th>
                            </thead>
                            <tbody>
                                <td>{{ auth()->user()->name }}</td>
                                <td>{{ auth()->user()->family }}</td>
                                <td>{{ auth()->user()->mobile }}</td>
                                <td>{{ auth()->user()->username }}</td>
                                <td>{{ auth()->user()->group?->name ?? "-" }}</td>
                                <td>{{ auth()->user()->access == 1 ? "نامحدود" : "محدود" }}</td>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="row mx-1 mb-3">
                    <div class="col">
                        <h4>تغییر رمز</h4>
                    </div>
                </div>
                <div class="row mx-1 mb-3">
                    <div class="col-4 form-group">
                        <label class="form-label">رمز قبلی <span class="text-danger">*</span></label>
                        <input type="password" id="old-password" data-id="old-password" class="form-control checkEmpty" placeholder="رمز قبلی...">
                        <div class="invalid-feedback" data-id="old-password" data-error="checkEmpty"></div>
                    </div>
                    <div class="col-4 form-group">
                        <label class="form-label">رمز جدید <span class="text-danger">*</span></label>
                        <input type="password" id="password" data-id="password" class="form-control" placeholder="رمز جدید...">
                        <div class="invalid-feedback" data-id="password" data-error="checkEmpty"></div>
                    </div>
                    <div class="col-4 form-group">
                        <label class="form-label">تکرار رمز جدید <span class="text-danger">*</span></label>
                        <input type="password" id="confirm-password" data-id="confirm-password" class="form-control" placeholder="تکرار رمز جدید...">
                        <div class="invalid-feedback" data-id="confirm-password" data-error="checkEmpty"></div>
                    </div>
                </div>
                <div class="row mx-1 mb-3">
                    <div class="col form-group text-center">
                        <button id="change-password" class="btn btn-success">ذخیره</button>
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

            $(document.body).on("click", "#change-password", function() {
                var e = checkEmpty();
                var old_password = $("#old-password").val();
                var password = $("#password").val();
                var confirm_password = $("#confirm-password").val();
                if (old_password != "" && password != "" && confirm_password != "") {
                    $("#loading").fadeIn();
                    var id = "{{ auth()->id() }}";
                    $.ajax({
                        type: "POST",
                        url: "{{ route('newPassword') }}",
                        data: {
                            old_password: old_password,
                            password: password,
                            password_confirmation: confirm_password,
                            id: id
                        },
                        success: function (data) {
                            $("#old-password").val('');
                            $("#password").val('');
                            $("#confirm-password").val('');
                            $("#loading").fadeOut();
                            Swal.fire({
                                title: "@lang('موفق')",
                                text: "@lang('رمز عبور با موفقیت تغییر یافت. لطفا دوباره وارد شوید.')",
                                icon: "success",
                                timer: 3000
                            }).then(() => {
                                window.location.href = "{{ route('logout') }}";
                            });
                        },
                        error: function (data) {
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
                        text: "لطفا کادرهای ستاره دار را تکمیل نمایید.",
                        icon: "error"
                    });
                }
            });
        });
    </script>
@stop
