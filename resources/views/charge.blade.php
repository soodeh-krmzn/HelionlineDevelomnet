@extends('parts.master')

@section('title', 'خرید بسته')

@section('head-styles')

@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="row mx-1 my-3">
                <div class="col-12">
                    <div class="alert alert-danger text-center m-0">
                        {{ __('کاربر گرامی شارژ اشتراک شما به پایان رسیده است.') }}
                        <br>
                        {{ __('لطفا جهت تمدید بسته خود روی دکمه زیر کلیک کنید.') }}
                        <br>
                        <a href="https://helisystem.ir/payment/create/{{ auth()->user()->username }}/account" class="btn btn-info mt-3">{{ __('خرید بسته') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer-scripts')

@stop
