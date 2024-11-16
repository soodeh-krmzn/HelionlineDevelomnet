@extends('parts.master')

@section('title', 'ترجمه')

@section('head-styles')
    <style>
        .dir-ltr {
            direction: ltr;
        }
    </style>
@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header border-bottom">
                <div class="row ">
                    <div class="col">
                        <button id="add-row" class="btn btn-primary btn-sm">افزودن</button>
                    </div>
                    <div class="col text-end">
                        <button onclick="$('#form').submit()" class="btn btn-warning btn-sm float-left">بروزرسانی
                            ترجمه</button>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <form action="/translate/push" id="form" method="post">
                    @csrf
                    <div class="row" id="list">
                        @foreach ($data as $key => $value)
                            <div class="col-6 mb-2">
                                <input type="text" name="keys[]" class="form-control" value="{{ $key }}">
                            </div>
                            <div class="col-6 mb-2">
                                <input type="text" name="values[]" class="form-control {{ $dirClass }}"
                                    value="{{ $value }}">
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>


        </div>
    </div>
@stop

@section('footer-scripts')
    <script>
        $tag =
            '<div class="col-6 mb-2"><input type="text" name="keys[]" class="form-control" ></div> <div class="col-6 mb-2"><input type="text" name="values[]" class="form-control"></div>';
        $('#add-row').on('click', function() {
            $('#list').prepend($tag);
        });
    </script>
@stop
