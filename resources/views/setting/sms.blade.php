@extends('parts.master')

@section('title', 'پیامک')

@section('head-styles')
<style>
    html.dark-style .dark-tr{
        background-color: rgb(92, 83, 83) !important;
        --bs-table-bg:initial !important;
    }
</style>
@stop
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="row">
            <div class="col-md-12">
                @include('setting.nav-bar', ['page' => 'sms'])
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div id="result">{{ $SmsPattern->showIndex1() }}</div>
                        </div>
                        <div id="setting-form">
                            <div class="row">
                                <div class="my-3 col-md-12">
                                    <label for="sms_sign" class="form-label">امضاء پیامک</label>
                                    <textarea name="sms_sign" class="form-control" placeholder="امضاء پیامک...">{{ $setting->getSetting("sms_sign") }}</textarea>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" id="save-setting" class="btn btn-success me-2">ذخیره امضاء</button>
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
                            title: "ذخیره شد.",
                            text: "تنظیمات با موفقیت ذخیره شد.",
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

            $(document.body).on("click", ".status-sms", function() {
                $("#loading").fadeIn();
                var id = $(this).data('id');
                var status = $(this).is(':checked');
                $.ajax({
                    type: "POST",
                    url: "{{ route('statusSmsPattern') }}",
                    data: {
                        id: id,
                        status: status
                    },
                    success: function(data) {
                        $("#result").html(data);
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "@lang('موفق')",
                            text: "وضعیت با موفقیت تغییر یافت.",
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
