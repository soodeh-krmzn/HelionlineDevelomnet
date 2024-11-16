@extends('parts.zero-master')
@section('title', 'صفحه یافت نشد')
@section('head-styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-misc.css') }}">
@stop
@section('content')
    <div class="misc-wrapper">
        <h1 class="mb-2 mx-2 secondary-font">صفحه یافت نشد</h1>
        <p class="mb-4 mx-2"></p>
        <div class="mt-5">
            <img src="{{ asset('assets/img/illustrations/page-misc-error-light.png') }}" alt="page-misc-not-authorized-light" width="450" class="img-fluid" data-app-light-img="illustrations/girl-hacking-site-light.png" data-app-dark-img="illustrations/girl-hacking-site-dark.png">
        </div>
    </div>
@endsection
@section('footer-scripts')
@stop
