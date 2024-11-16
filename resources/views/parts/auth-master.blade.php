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
<html lang="{{ $lang }}" class="light-style" dir="{{ $dir }}" data-theme="theme-default" data-assets-path="assets/" data-template="horizontal-menu-template">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
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
    <link rel="icon" type="image/png" sizes="196x196" href="{{ asset('assets/img/icons/android-chrome-196x196.png') }}" />
    <meta name="theme-color" content="#66cccc" />
    <meta name="msapplication-TileColor" content="#66cccc" />
    <meta name="msapplication-TileImage" content="{{ asset('assets/img/icons/windows-tile.png') }}">
    <meta name="msapplication-square70x70logo" content="{{ asset('assets/img/icons/windows-small-tile.png') }}" />
    <meta name="msapplication-square150x150logo" content="{{ asset('assets/img/icons/windows-medium-tile.png') }}" />
    <meta name="msapplication-wide310x150logo" content="{{ asset('assets/img/icons/windows-wide-tile.png') }}" />
    <meta name="msapplication-square310x310logo" content="{{ asset('assets/img/icons/windows-large-tile.png') }}" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css">
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/' . $dir . '/' . $dir . '.css') }}">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}">

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

    @yield('head-styles')
</head>
<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <div class="card">
                    <div class="card-body">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>

<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>

<!-- Page JS -->
<script src="{{ asset('assets/js/extended-ui-sweetalert2.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- Global JS -->
<script src="{{ asset('assets/js/global.js') }}"></script>

@yield('footer-scripts')
</body>
</html>
