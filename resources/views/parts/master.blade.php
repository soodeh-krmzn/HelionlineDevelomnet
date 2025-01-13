<!DOCTYPE html>
@php
    switch (app()->getLocale()) {
        case 'en':
            $lang = 'en';
            $dir = 'ltr';
            break;
        case 'fa':
            $lang = 'fa';
            $dir = 'rtl';
            break;
    }
@endphp
<html lang="{{ $lang }}" class="light-style layout-menu-fixed" dir="{{ $dir }}" data-theme="theme-default"
    data-assets-path="assets/" data-template="horizontal-menu-template">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>@yield('title')</title>
    <meta name="description" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/icons/favicon.ico') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/icons/favicon-16x16.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/icons/favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/img/icons/favicon-96x96.png') }}" />

    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/img/icons/apple-57x57-touch-icon.png') }}" />
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('assets/img/icons/apple-60x60-touch-icon.png') }}" />
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/img/icons/apple-72x72-touch-icon.png') }}" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/icons/apple-76x76-touch-icon.png') }}" />
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/img/icons/apple-114x114-touch-icon.png') }}" />
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets/img/icons/apple-120x120-touch-icon.png') }}" />
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets/img/icons/apple-144x144-touch-icon.png') }}" />
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets/img/icons/apple-152x152-touch-icon.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/icons/apple-180x180-touch-icon.png') }}" />
    <link rel="icon" type="image/png" sizes="196x196"
        href="{{ asset('assets/img/icons/android-chrome-196x196.png') }}" />
    <meta name="theme-color" content="#66cccc" />
    <meta name="msapplication-TileColor" content="#66cccc" />
    <meta name="msapplication-TileImage" content="{{ asset('assets/img/icons/windows-tile.png') }}">
    <meta name="msapplication-square70x70logo" content="{{ asset('assets/img/icons/windows-small-tile.png') }}" />
    <meta name="msapplication-square150x150logo" content="{{ asset('assets/img/icons/windows-medium-tile.png') }}" />
    <meta name="msapplication-wide310x150logo" content="{{ asset('assets/img/icons/windows-wide-tile.png') }}" />
    <meta name="msapplication-square310x310logo" content="{{ asset('assets/img/icons/windows-large-tile.png') }}" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}">
    <!--link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}"-->
    <!--link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}"-->

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}"
        class="template-customizer-theme-css">
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/' . $dir . '/' . $dir . '.css') }}">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/spinkit/spinkit.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/my-' . $dir . '-style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/persiandatepicker/persian-datepicker.min.css?v=1.2') }}">
    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

    @yield('head-styles')
    <style>
        .dark-style .bg-info {
            color: black !important;
        }

        .dark-style .bg-success {
            color: white !important;
        }
    </style>
</head>

<body>
    <div id="loading">
        <span>
            <div class="spinner-grow text-info" role="status">
                <span class="visually-hidden">در حال بارگذاری ...</span>
            </div>
        </span>
    </div>

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">

        <div class="layout-container">

            @include('parts.nav')
            <!-- Layout container -->
            <div class="layout-page">

                <!-- Content wrapper -->
                <div class="content-wrapper">

                    @include('parts.aside')
                    <!-- Content -->
                    @yield('content')
                    <!--/ Content -->
                    @include('parts.footer')
                    <div class="content-backdrop fade"></div>
                </div>
                <!--/ Content wrapper -->
            </div>
            <!--/ Layout container -->
        </div>
    </div>
    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
    <!--/ Layout wrapper -->


    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>

    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->
    <script>
        window.emptyError = "@lang('این کادر الزامی است.')"
    </script>
    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    @if (app()->getLocale() == 'fa')
        <script src="{{ asset('assets/vendor/libs/datatables-bs5/i18n/fa.js') }}"></script>
    @endif
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets/js/extended-ui-sweetalert2.js') }}"></script>
    <script src="{{ asset('assets/js/form-layouts.js') }}"></script>
    <script src="{{ asset('assets/js/forms-extras.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Global JS -->
    <script src="{{ asset('assets/js/global.js?v=1.0') }}"></script>
    @include('sweetalert::alert')
    <script src="{{ asset('assets/js/RMain.js?v=1') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // console.log("{{ app('config')->get('app.timezone') . '-' . session('user_timezone') }}");
            dateMask();
            dateTimeMask();
            moneyFilter();

            let load = `
            <div class="col text-center">
                <span class="spinner-grow" role="status" aria-hidden="true"></span>
            </div>`;

            $(document.body).on("click", "#toggle-help", function() {
                $("#help-offcanvas").html(load);
                var url = "{{ Request::route()->uri }}";
                $.ajax({
                    type: "POST",
                    url: "{{ route('helpMenu') }}",
                    data: {
                        url: url
                    },
                    success: function(data) {
                        $("#help-offcanvas").html(data);
                    }
                });
            });

            $(document.body).on("keyup", ".payment-input", function() {
                totalPayment();
            });

        });
    </script>
    <script src="{{ asset('assets/vendor/persiandatepicker/persian-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/persiandatepicker/persian-date.min.js') }}"></script>
    <script>
        $(document.body).on('focus', '.date-mask', function() {
            $(this).persianDatepicker({
                calendarType: "{{ app()->getLocale() == 'fa' ? 'persian' : 'gregorian' }}",
                initialValue: false,
                observer: true,
                format: 'YYYY/MM/DD',
                initialValueType: "{{ app()->getLocale() == 'fa' ? 'persian' : 'gregorian' }}",
                calendar: {
                    persian: {
                        locale: 'fa'
                    },
                    gregorian: {
                        locale: 'en',
                        format: 'YYYY/MM/DD'
                    }
                },
                autoClose: true
            });
        });
        $(document.body).on('focus', '.datetime-mask', function() {


            if ($(this).attr('disabled')) {
                return false;
            }
            $(this).persianDatepicker({
                calendarType: "{{ app()->getLocale() == 'fa' ? 'persian' : 'gregorian' }}",
                initialValue: false,
                observer: true,
                format: 'YYYY/MM/DD HH:mm:ss',
                timePicker: {
                    enabled: true,
                    meridiem: {
                        enabled: true
                    }
                },
                initialValueType: "{{ app()->getLocale() == 'fa' ? 'persian' : 'gregorian' }}",
                calendar: {
                    persian: {
                        locale: 'fa'
                    },
                    gregorian: {
                        locale: 'en',
                        format: 'YYYY/MM/DD HH:mm'
                    }
                },
                autoClose: true
            });
        });
    </script>
    <script>
        function searchPerson() {
            $(".searchPerson").select2();
            $('#collapseExample').on('shown.bs.collapse', function() {
                $(".searchPerson").select2({
                    minimumInputLength: 3, // Minimum characters to start searching
                    placeholder: "@lang('انتخاب کنید')",
                    @if (app()->getLocale() == 'fa')
                        language: {
                            inputTooShort: function() {
                                return "لطفاً حداقل " + 3 + " کاراکتر وارد کنید";
                            },
                            noResults: function() {
                                return "نتیجه‌ای یافت نشد";
                            },
                            searching: function() {
                                return "در حال جستجو...";
                            },
                        },
                    @endif
                    allowClear: true,
                    ajax: {
                        url: "{{ route('sps') }}",
                        dataType: "json",
                        delay: 500,
                        data: function(params) {
                            return {
                                search: params.term, // Send the search term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data[0].map(function(item) {
                                    return {
                                        id: item.id, // The value to be stored
                                        text: item.name +
                                            " " +
                                            item.family +
                                            `(${item.id})`, // The text to be displayed
                                    };
                                }),
                            };
                        },
                        cache: true,
                    },
                });
                $('.searchPerson').parent().find(".select2-container:not(:first)").remove();
            });
        }

        function searchPersonM() {
            $(".searchPersonM").select2({
                minimumInputLength: 3, // Minimum characters to start searching
                placeholder: "@lang('انتخاب کنید')",
                dropdownParent: $('#crud .modal-content'),
                @if (app()->getLocale() == 'fa')
                    language: {
                        inputTooShort: function() {
                            return "لطفاً حداقل " + 3 + " کاراکتر وارد کنید";
                        },
                        noResults: function() {
                            return "نتیجه‌ای یافت نشد";
                        },
                        searching: function() {
                            return "در حال جستجو...";
                        },
                    },
                @endif
                allowClear: true,
                ajax: {
                    url: "{{ route('sps') }}",
                    dataType: "json",
                    delay: 500,
                    data: function(params) {
                        return {
                            search: params.term, // Send the search term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data[0].map(function(item) {
                                return {
                                    id: item.id, // The value to be stored
                                    text: item.name +
                                        " " +
                                        item.family +
                                        `(${item.id})`, // The text to be displayed
                                };
                            }),
                        };
                    },
                    cache: true,
                },
            });
        }
        $(document.body).on("click", ".enter-default", function() {
            // $('.clear-input').val('');
            //===calculate total value
            var total = 0;
            $(`#${target}`).val(0);
            $('.payment-price').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            //===end
            var initialPrice = $('#defaultPrice').val();
            var rest = 0;
            if (initialPrice > total) {
                rest = initialPrice - total
            }
            var target = $(this).data('target');
            var displayDefaultPrice = $('#displayDefaultPrice').val();

            $(`input[data-id=${target}]`).val(cnf(rest));
            $(`#${target}`).val(rest);

            //===recalculate totalPay
            var typeTotal = 0;
            $('.type-price').each(function() {
                typeTotal += parseFloat($(this).val()) || 0;
            });


            $('#total-pay').html(cnf(typeTotal));
            //===end
        });
    </script>
    <script>
        $(document).on("change", ".status-offline", function() {
            let status = $(this).is(":checked") ? 1 : 0;

            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: "این کار حالت آفلاین را تغییر خواهد داد!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'بله',
                cancelButtonText: 'لغو'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/offline/toggle',
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            offline_mode: status
                        },
                        success: function(response) {
                            Swal.fire('انجام شد!', response.message, 'success');
                        },
                        error: function(xhr) {
                            Swal.fire('خطا!', xhr.responseJSON.message, 'error');
                        }
                    });
                }
            });
        });
    </script>
    <script>
        $(document).on("click", ".license-modal-btn", function() {
            $.ajax({
                url: "{{ route('licenseShow') }}",
                type: "GET",
                success: function(data) {
                    $("#license-result").html(data);
                    $("#licesne").modal('show');
                },
                error: function(data) {
                    Swal.fire({
                        title: "{{ __('اخطار') }}",
                        text: data.responseJSON.message,
                        icon: "error"
                    });
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('checkLicense') }}",
                type: "GET",
                success: function(response) {
                    if (response.isActive) {
                        $('#crud-game').prop('disabled', false);
                    } else {
                        $('#crud-game').prop('disabled', true);

                        $('#crud-game').on('click', function(e) {
                            e.preventDefault();
                            Swal.fire({
                                title: 'خطا',
                                text: 'در حالتی که آفلاین فعال باشد امکان ثبت ورود وجود ندارد.',
                                icon: 'warning',
                                confirmButtonText: 'باشه'
                            });
                        });
                    }
                },
                error: function() {
                    console.error("مشکلی در بررسی وضعیت لایسنس به وجود آمد.");
                }
            });
        });
    </script>
    @yield('footer-scripts')
</body>

</html>
