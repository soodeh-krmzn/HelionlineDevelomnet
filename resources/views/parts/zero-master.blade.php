<!DOCTYPE html>
@php
    switch (app()->getLocale()) {
        case "en":
            $lang = "en";
            $dir = "ltr";
            break;
        case "fa":
            $lang = "fa";
            $dir = "rtl";
            break;
    }
@endphp
<html lang="{{ $lang }}" class="light-style layout-menu-fixed" dir="{{ $dir }}" data-theme="theme-default" data-assets-path="assets/"
      data-template="horizontal-menu-template">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>@yield('title')</title>
    <meta name="description" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/icons/favicon.ico') }}"/>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/icons/favicon-16x16.png') }}"/>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/icons/favicon-32x32.png') }}"/>
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/img/icons/favicon-96x96.png') }}"/>

    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/img/icons/apple-57x57-touch-icon.png') }}"/>
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('assets/img/icons/apple-60x60-touch-icon.png') }}"/>
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/img/icons/apple-72x72-touch-icon.png') }}"/>
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/icons/apple-76x76-touch-icon.png') }}"/>
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/img/icons/apple-114x114-touch-icon.png') }}"/>
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets/img/icons/apple-120x120-touch-icon.png') }}"/>
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets/img/icons/apple-144x144-touch-icon.png') }}"/>
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets/img/icons/apple-152x152-touch-icon.png') }}"/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/icons/apple-180x180-touch-icon.png') }}"/>
    <link rel="icon" type="image/png" sizes="196x196"
          href="{{ asset('assets/img/icons/android-chrome-196x196.png') }}"/>
    <meta name="theme-color" content="#66cccc"/>
    <meta name="msapplication-TileColor" content="#66cccc"/>
    <meta name="msapplication-TileImage" content="{{ asset('assets/img/icons/windows-tile.png') }}">
    <meta name="msapplication-square70x70logo" content="{{ asset('assets/img/icons/windows-small-tile.png') }}"/>
    <meta name="msapplication-square150x150logo" content="{{ asset('assets/img/icons/windows-medium-tile.png') }}"/>
    <meta name="msapplication-wide310x150logo" content="{{ asset('assets/img/icons/windows-wide-tile.png') }}"/>
    <meta name="msapplication-square310x310logo" content="{{ asset('assets/img/icons/windows-large-tile.png') }}"/>

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}">

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
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/spinkit/spinkit.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">

    @yield('head-styles')
</head>
<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
    <div class="layout-container">
        <!-- Layout container -->
        <div class="layout-page">
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                @yield('content')
                <!--/ Content -->
                <div class="content-backdrop fade"></div>
            </div>
            <!--/ Content wrapper -->
        </div>
        <!--/ Layout container -->
    </div>
</div>
<!--/ Layout wrapper -->

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>


<!-- Vendors JS -->
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-bs5/i18n/fa.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>

<!-- Page JS -->
<script src="{{ asset('assets/js/extended-ui-sweetalert2.js') }}"></script>
<script src="{{ asset('assets/js/form-layouts.js') }}"></script>
<script src="{{ asset('assets/js/forms-extras.js') }}"></script>

<!-- Global JS -->
<script src="{{ asset('assets/js/global.js') }}"></script>

@yield('footer-scripts')
</body>
</html>
