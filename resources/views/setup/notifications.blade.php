@extends('parts.master')

@section('title', __('اطلاعیه ها و تغییرات نرم افزار'))

@section('head-styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}">
@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="row">
            <div class="col-12">
                <h5 class="secondary-font">@lang('اطلاعیه ها و تغییرات نرم افزار')</h5>
            </div>

            <div class="col-12 mb-4">
                <div class="bs-stepper wizard-vertical vertical wizard-numbered mt-2">
                    <div class="bs-stepper-content">
                        @php
                            $i = $items->count();
                        @endphp
                        @foreach ($items as $item)
                            <div class="content active dstepper-block">
                                <div class="content-header mb-3">
                                    <div class="">
                                        <span class="d-inline-block text-center bg-{{ $item->type == 'update' ? 'success' : 'info' }}"
                                            style="width:20px;margin-left:6px">{{ $i-- }}</span>
                                        <h6 class="mb-1 d-inline-block">{{ $item->title }}</h6>
                                    </div>
                                    <small>{{ $item->type == 'update' ? __('تغییرات نرم افزار') : __('اطلاعیه') }}</small>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <!--h5 class="card-header heading-color">اطلاعات مجموعه</h5-->
                                            <div class="card-body">
                                                <div class="col-12 mb-0">
                                                    <div class=" mb-0 alert alert-{{ $item->type == 'update' ? 'success' : 'info' }}">
                                                        {!! $item->text !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr style="background: #5e26962e" >
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer-scripts')
    <script src="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('assets/js/form-wizard-numbered.js') }}"></script>
    <script src="{{ asset('assets/js/form-wizard-validation.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document.body).on("click", "#store-setup", function() {
                $("#loading").fadeIn();
                $.ajax({
                    type: "POST",
                    url: "{{ route('storeSetup') }}",
                    data: {
                        action: "done"
                    },
                    success: function(data) {
                        $("#loading").fadeOut();
                        Swal.fire({
                            title: "عملیات موفق",
                            text: "عملیات راه اندازی نرم افزار با موفقیت انجام شد.",
                            icon: "success",
                            timer: 3000
                        });
                        window.location.href = "{{ route('dashboard') }}";
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
