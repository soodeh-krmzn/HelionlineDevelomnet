@extends('parts.master')

@section('title', 'گزارش اصلاحات')

@section('head-styles')

@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{ breadcrumb() }}
        <div class="card">
            <div class="card-datatable table-responsive">
                <div class="row mx-1">
                    <div id="result" class="col">{{ $report->showIndex("package") }}</div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer-scripts')

@stop
